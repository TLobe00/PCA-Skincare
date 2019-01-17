<?php

namespace App\Console\Commands;

use App\Jobs\ToNav\PushCustomerUpdateToNav;
use App\shopifyapi;
use Illuminate\Console\Command;

class PushCustomerUpdatesToNav extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:update-customers-nav';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push customer updates to Nav.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $updates = shopifyapi::unprocessed()->updatedCustomers();

        if (($count = $updates->count()) < 1) {
            $this->error("No customer updates were found.");

            return;
        }

        $bar = $this->output->createProgressBar($count);

        $updates->chunk(50, function ($row) use($bar) {
            foreach($row as $customer) {
                PushCustomerUpdateToNav::dispatch($customer);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->output->newLine(1);
    }
}
