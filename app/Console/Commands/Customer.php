<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
//use App\Jobs\PushProducts;
use Log;
use App\shopifyapi;
use App\webhook_calls;

class Customer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pca:customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push customers to nav';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //PushProducts::dispatch()->delay(now()->addSeconds(30));
    }
}
