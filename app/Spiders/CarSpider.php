<?php

namespace App\Spiders;

use App\Models\Car;
use Generator;
use Illuminate\Support\Str;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class CarSpider extends BasicSpider
{
    public function parse(Response $response): Generator
    {
        dump($response->getUri());

        $uniqueViews = $response->filter("span#show_cnt_stat")->innerText();

        dd($uniqueViews);


//        Car::create([
//            'reference_url' => $response->getUri(),
//            'unique_views' => '',
//            'description' => '',
//            'mark' => '',
//            'model' => '',
//            'year' => '',
//            'motor' => '',
//            'fuel_type' => '',
//            'gearbox' => '',
//            'color' => '',
//            'body_type' => '',
//            'mileage_in_km' => '',
//            'technical_inspection_date' => '',
//            'prince_in_cents' => '',
//            'upload_date' => '',
//            'specifications' => '',
//        ]);


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
