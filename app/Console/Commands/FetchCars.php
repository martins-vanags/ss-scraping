<?php

namespace App\Console\Commands;

use App\Spiders\CarSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;

class FetchCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-cars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch cars from scraper and store them in the database.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Roach::startSpider(CarSpider::class);
    }
}
