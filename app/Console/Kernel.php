<?php

namespace App\Console;

use App\Console\Commands\PushCustomersToNav;
use App\Console\Commands\PushCustomerUpdatesToNav;
use App\Console\Commands\TransformShopifyCustomers;
use App\Console\Commands\TransformShopifyOrders;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\Products;
use App\Console\Commands\Customer;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Products::class,
        Customer::class,
        PushCustomersToNav::class,
        PushCustomerUpdatesToNav::class,
        TransformShopifyCustomers::class,
        TransformShopifyOrders::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
