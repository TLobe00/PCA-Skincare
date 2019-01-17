<?php

namespace App\Console\Commands;

use App\Jobs\ToNav\PushOrderToNav;
use App\Order;
use Illuminate\Console\Command;

class PushOrdersToNav extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:orders-nav';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push orders from shopify (DB) to Nav';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $newOrders = Order::unprocessed();

        if ($newOrders->count() < 1) {
            $this->error("No new orders were found.");

            return;
        }

        $bar = $this->output->createProgressBar($newOrders->count());

        $newOrders->chunk(50, function ($row) use ($bar) {
            foreach ($row as $customer) {
                PushOrderToNav::dispatch($customer);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->output->newLine(1);
    }
}
