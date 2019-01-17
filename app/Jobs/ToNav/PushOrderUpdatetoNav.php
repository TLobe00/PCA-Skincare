<?php

namespace App\Jobs\ToNav;

use App\Order;
use App\Services\NavApiService;
use App\shopifyapi;
use App\Transformers\NavOrderTransformer;
use App\Transformers\OrderTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushOrderUpdatetoNav implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $api;

    /**
     * Create a new job instance.
     *
     * @param shopifyapi $api
     */
    public function __construct(shopifyapi $api) {
        $this->api = $api;
    }

    /**
     * Execute the job.
     *
     * @param NavApiService $nav
     * @return void
     */
    public function handle(NavApiService $nav) {
        $payload = json_decode($this->api->savetext, true);

        if(!isset($payload['id'])) {
            \Log::error("Order(local: {$this->api->id}): api response did not contain an id field.");
            $this->fail();
        }

        $order = Order::where('shopify_id', $payload['id'])->first();

        //If can't find customer
        if(!$order) {
            \Log::error("Order(shopify: {$payload['id']}): could not find local record for this shopify id.");
            $this->fail();

            return;
        }

        // Fetch ETag from nav for customer
        $navOrderRes = $nav->getSalesOrder($order->nav_no);

        if(!$navOrderRes['success'] || empty($navOrderRes['data'])) {
            \Log::error("Order(shopify: {$order->shopify_id}): could not fetch sales order from Nav.");
            $this->fail();
        }

        //Update local cache of customer
        $updateLocalPayload = fractal()->item($payload, new OrderTransformer())->toArray();
        //Fill customer model with payload to handle filling default values
        $model = (new Order())->fill($updateLocalPayload)->toArray();
        //Transform to payload Nav likes
        $updateNavPayload = fractal()->item($model, new NavOrderTransformer())->toArray();
        //Manually add prefetched ETag
        $updateNavPayload['ETag'] = $navOrderRes['data']['ETag'];

        $res = $nav->updateSalesOrder($order->nav_no, $updateNavPayload['ETag'], $updateNavPayload);

        if ($res['success']) {
            //Update customer with update record
            $order->fill($updateLocalPayload);
            $order->save();
            $this->api->processed = 1;
            $this->api->save();
        } else {
            \Log::error("Order(shopify: {$order->shopify_id}): " . $res['error']);
            $this->fail();
        }
    }
}
