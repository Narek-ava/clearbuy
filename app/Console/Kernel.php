<?php

namespace App\Console;

use App\Console\Commands\RemoveUnnecessaryProductImages;
use App\Jobs\AmazonScrapper;
use App\Models\Product;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Queue\Middleware\ThrottlesExceptions;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Test::class,
        RemoveUnnecessaryProductImages::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $productsTotal = Product::whereNotNull('asin')->count();
            $pages = ceil($productsTotal / 10);
            $skip = 0;
            for ($i = 0; $i < $pages; $i++) {
                $products = Product::whereNotNull('asin')->skip($skip)->take(10)->get();
                AmazonScrapper::dispatch($products);
                $skip += 10;
            }
        })->hourly();

        $schedule->command('zapier:deal-expiry')->everyMinute();
        $schedule->command('remove:unnecessary-product-images ')->weekly();
    }


    // /**
    //  * Get the middleware the job should pass through.
    //  *
    //  * @return array
    //  */
    // public function middleware()
    // {
    //     return [new ThrottlesExceptions(10, 5)];
    // }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
