<?php

namespace App\Console\Commands;

use App\Jobs\MigrateOrderFromWebhook;
use App\shopifyapi;
use Illuminate\Console\Command;

class TransformShopifyOrders extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transform:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transform webhook order responses to the orders table.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $responses = shopifyapi::unprocessed()->orders();
        $count = $responses->count();

        if($count < 1) {
            $this->error("No unprocessed order webhook responses were found");

            return false;
        }

        $responses->chunk(50, function ($row) {
            foreach ($row as $order) {
                MigrateOrderFromWebhook::dispatch($order);
            }
        });

        return true;
    }
}
