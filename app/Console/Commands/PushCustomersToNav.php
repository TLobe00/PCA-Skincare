<?php

namespace App\Console\Commands;

use App\Customer;
use App\Jobs\ToNav\PushCustomerToNav;
use Illuminate\Console\Command;

class PushCustomersToNav extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:new-customers-nav
                            {--id= : Local DB id of customer.}
                            {--shopify-id= : Shopify id of customer.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push customers from shopify (DB) to Nav';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $newCustomers = Customer::unprocessed();

        if($this->option('id')) {
            $newCustomers->where('id', $this->option('id'));
        } else if($this->option('shopify-id')) {
            $newCustomers->where('shopify_id', $this->option('shopify-id'));
        }

        if (($count = $newCustomers->count()) < 1) {
            $this->error("No customers were found.");

            return;
        }

        $bar = $this->output->createProgressBar($count);

        $newCustomers->chunk(50, function ($row) use($bar) {
            foreach ($row as $customer) {
                PushCustomerToNav::dispatch($customer);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->output->newLine(1);
    }
}
