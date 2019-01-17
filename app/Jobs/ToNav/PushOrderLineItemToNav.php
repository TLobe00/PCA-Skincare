<?php

namespace App\Jobs\ToNav;

use App\Order;
use App\Services\NavApiService;
use App\Transformers\LineItemTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushOrderLineItemToNav implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var array
     */
    protected $lineItemData;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     * @param array $lineItem
     */
    public function __construct(Order $order, $lineItem = []) {
        $this->order = $order;
        $this->lineItemData = $lineItem;
    }

    /**
     * Execute the job.
     *
     * @param NavApiService $nav
     * @return void
     */
    public function handle(NavApiService $nav) {
        if(empty($this->order->nav_no)) {
            \Log::error("Order({$this->order->id}): the nav_no was empty for this order.");
            $this->fail();

            return;
        }

        $payload = fractal()->item($this->lineItemData, new LineItemTransformer())->toArray();
        \Log::info($payload);
        $res = $nav->createOrderLine($this->order->nav_no, $payload);

        if(!$res['success']) {
            \Log::error("Order({$this->order->id}): could not push the order item in Nav.");
            \Log::error($res['error']);
            $this->fail();

            return;
        }
    }
}
