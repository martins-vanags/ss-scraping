<?php

namespace App\Console\Commands;

use App\Models\Car;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetVinCodesForCarsSynced extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:vin-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Car::whereNotNull('vin_hash')
            ->limit(5)
            ->get()
            ->map(function (Car $car) {
                $vinCode = Http::accept('message/x-ajax')
                    ->withHeaders([
                        'Cookie' => 'LG=lv; sid_c=1; sid=385798e3d1385f2e0577d545d6f3f3aa926001fdb5046f37e48060d2c4a18b93bfa967d2d421a3faaf6eb325f5b0cc3f; PHPSESSID=39ff2845d6843f636f6df67bb7163f5a',
                    ])
                    ->get('https://www.ss.com/w_inc/ajax.php', [
                        'action' => 'show_special_js_data',
                        'version' => 1,
                        'lg' => 'lv',
                        'data' => $car->vin_hash,
                    ]);

                Log::info($vinCode->body());

                $code = null;
                if ($vinCode->successful()) {
                    preg_match('/(?<=1678":")[^"]+/', $vinCode->body(), $matches);
                    $code = $matches[0] ?? null;
                }

                $car->update([
                    'vin_code' => $code,
                ]);
            });
    }
}
