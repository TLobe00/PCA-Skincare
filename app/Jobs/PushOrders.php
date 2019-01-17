<?php

namespace App\Jobs;

use App\Http\Controllers\webhooks\Order;
use App\Transformers\OrderTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Single: $data = fractal()->item([], new OrderTransformer())->toArray();
        //Multi: $data = fractal()->collection([], new OrderTransformer())->toArray();
    }
}
