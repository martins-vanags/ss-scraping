<?php

namespace App\Spiders;

use App\Models\Car;
use Carbon\Carbon;
use Generator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class CarSpider extends BasicSpider
{
    public function parse(Response $response): Generator
    {
        $uploadDate = $response->filter("#page_main table tr:contains('Izdrukāt') td[align]")->text();
        $uploadDate = Str::of($uploadDate)->after('Datums: ')->toString();

        $request = Str::of($response->getUri())->explode('/')->toArray();
        $mark = $request[7];
        $model = $request[8];

        $year = $response->filter("td#tdo_18")->text();

        try {
            $motorSpecifications = $response->filter("td#tdo_15")->text();
            [$motor, $fuelType] = Str::of($motorSpecifications)->explode(' ')->toArray();
        } catch (InvalidArgumentException $e) {
            $motor = 'Elektrisks';
            $fuelType = 'Elektrisks';
        }


        $gearBox = $response->filter("td#tdo_35")->text();

        try {
            $color = $response->filter("td#tdo_17")->text();
            $color = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x{A0}]/u', '', $color);
        } catch (InvalidArgumentException $e) {
            $color = null;
        }

        if (is_null($color)) {
            try {
                $color = $response->filter('td#tdo_88')->text();
                $color = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x{A0}]/u', '', $color);
            } catch (InvalidArgumentException $e) {
                $color = null;
            }
        }

        if (is_null($color)) {
            $color = 'Nav norādīts';
        }

        $bodyType = $response->filter("td#tdo_32")->text();

        try {
            $mileageInKm = $response->filter("td#tdo_16")->text();
        } catch (InvalidArgumentException $e) {
            $mileageInKm = 'Nav norādīts';
        }

        $technicalInspectionDate = $response->filter("td#tdo_223")->text();

        $price = $response->filter("span.ads_price")->text();

        $price = Str::of($price)->remove('€')->remove(' ')->toInteger();

        try {
            $specifications = $response->filter("div#msg_div_msg")->html();
        } catch (InvalidArgumentException $e) {
            $specifications = null;
        }

        preg_match_all('/<b class="auto_c">(.*?)<\/b>/s', $specifications, $matches);
        [, $filteredMatches] = $matches;
        $specifications = $filteredMatches;

        Car::create([
            'reference_url' => $response->getUri(),
            'mark' => $mark,
            'model' => $model,
            'year' => $year,
            'motor' => $motor,
            'fuel_type' => $fuelType,
            'gearbox' => $gearBox,
            'color' => $color,
            'body_type' => $bodyType,
            'mileage_in_km' => $mileageInKm,
            'technical_inspection_date' => $technicalInspectionDate,
            'price' => $price,
            'upload_date' => Carbon::make($uploadDate)->toDateTimeString(),
            'specifications' => json_encode($specifications),
        ]);

        yield $this->item([]);
    }


    /** @return Request[] */
    protected function initialRequests(): array
    {
        return [
            new Request(
                method: 'GET',
                uri: 'https://www.ss.com/lv/transport/cars/',
                parseMethod: [
                    $this,
                    'getCarsWithTheirCounts'
                ]
            )
        ];
    }

    public function getCarsWithTheirCounts(Response $response): Generator
    {
        $availableCarLinks = $response->filter("td[width='75%'] h4.category a")->extract(['href']);

        foreach ($availableCarLinks as $link) {
            yield $this->request(
                'GET',
                'https://www.ss.com' . $link . 'today/sell/',
                'getPaginationLinksForCars'
            );
        }
    }

    public function getPaginationLinksForCars(Response $response): Generator
    {
        $totalCarsAdded = $response->filter("#today_cnt_sl option[selected]")->text();

        $count = Str::of($totalCarsAdded)->after('-')->trim()->toInteger();
        $carsInOnePage = 30;

        if ('https://www.ss.com/lv/transport/cars/bmw/today/sell/' === $response->getUri()) {
            $carsInOnePage = 60;
        }

        $pagesToCrawl = [];

        if ($count > $carsInOnePage) {
            for ($i = 1; $i < round($count / $carsInOnePage); $i++) {
                $pagesToCrawl[] = $response->getUri() . 'page' . $i . '.html';
            }
        } else {
            $pagesToCrawl[] = $response->getUri() . 'page1.html';
        }

        foreach ($pagesToCrawl as $page) {
            yield $this->request(
                'GET',
                $page,
                'getCarDetailPage'
            );
        }
    }

    public function getCarDetailPage(Response $response): Generator
    {
        $carLinks = $response->filter("tr[id^='tr_'] a")->extract(['href']);

        $carLinks = collect($carLinks)->unique()->toArray();

        foreach ($carLinks as $link) {
            yield $this->request(
                'GET',
                'https://www.ss.com' . $link,
            );
        }
    }

}
