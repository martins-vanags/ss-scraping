<?php

namespace App\Console\Commands;

use App\Models\Car;
use Illuminate\Console\Command;

class TruncateCarsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:truncate-cars-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Car::truncate();
    }
}
