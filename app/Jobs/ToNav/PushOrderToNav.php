<?php

namespace App\Jobs\ToNav;

use App\Order;
use App\Services\NavApiService;
use App\Transformers\NavOrderTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushOrderToNav implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Order
     */
    private $order;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order) {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @param NavApiService $nav
     * @return void
     */
    public function handle(NavApiService $nav) {
        $payload = fractal()->item($this->order->toArray(), new NavOrderTransformer())->toArray();
        \Log::info($payload);
        $res = $nav->createorder($payload);

        if($res['success']) {
            if(isset($res['data']['No'])) {
                $this->order->nav_no = $res['data']['No'];
            }

            if(isset($res['data']['ETag'])) {
                $this->order->etag = $res['data']['ETag'];
            }

            $this->order->save();

            if($this->order->apiResponse) {
                $data = json_decode($this->order->apiResponse->savetext, true);

                if(isset($data['line_items'])) {
                    foreach($data['line_items'] as $li) {
                        PushOrderLineItemToNav::dispatch($this->order, $li);
                    }
                }
            }
        } else {
            \Log::error("Order({$this->order->shopify_id}): " . $res['error']);
            $this->fail();
        }
    }
}
