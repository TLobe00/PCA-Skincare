<?php

namespace App\Console\Commands;

use App\Jobs\MigrateCustomerFromWebhook;
use App\shopifyapi;
use Illuminate\Console\Command;

class TransformShopifyCustomers extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transform:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transform webhook customer responses to the customers table.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $responses = shopifyapi::unprocessed()->newCustomers();
        $count = $responses->count();

        if($count < 1) {
            $this->error("No unprocessed customer webhook responses were found");

            return false;
        }

        $responses->chunk(50, function ($row) {
            foreach ($row as $customer) {
                MigrateCustomerFromWebhook::dispatch($customer);
            }
        });

        return true;
    }
}
