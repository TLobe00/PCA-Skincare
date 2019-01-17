<?php

namespace App\Console\Commands;

use App\Jobs\ToNav\PushRefundToNav;
use App\Refund;
use Illuminate\Console\Command;

class PushRefundsToNav extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:refunds-nav';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push refunds from shopify (DB) to Nav';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $newRefunds = Refund::unprocessed();

        if ($newRefunds->count() < 1) {
            $this->error("No refunds were found.");

            return;
        }

        $bar = $this->output->createProgressBar($newRefunds->count());

        $newRefunds->chunk(50, function ($row) use ($bar) {
            foreach ($row as $refund) {
                PushRefundToNav::dispatch($refund);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->output->newLine(1);
    }
}
