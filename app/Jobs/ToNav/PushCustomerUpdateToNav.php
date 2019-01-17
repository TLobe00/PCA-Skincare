<?php

namespace App\Jobs\ToNav;

use App\Customer;
use App\Services\NavApiService;
use App\shopifyapi;
use App\Transformers\NavCustomerTransformer;
use App\Transformers\CustomerTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushCustomerUpdateToNav implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var shopifyapi
     */
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
            \Log::error("Customer(local: {$this->api->id}): api response did not contain an id field.");
            $this->fail();
        }

        $customer = Customer::where('shopify_id', $payload['id'])->first();

        //If can't find customer
        if(!$customer) {
            \Log::error("Customer(shopify: {$payload['id']}): could not find local record for this shopify id.");
            $this->fail();

            return;
        }

        // Fetch ETag from nav for customer
        $navCustomerRes = $nav->getCustomer($customer->nav_no);

        if(!$navCustomerRes['success'] || empty($navCustomerRes['data'])) {
            \Log::error("Customer(shopify: {$customer->shopify_id}): could not fetch customer from Nav.");
            $this->fail();
        }

        //Update local cache of customer
        $updateLocalPayload = fractal()->item($payload, new CustomerTransformer())->toArray();
        //Fill customer model with payload to handle filling default values
        $model = (new Customer)->fill($updateLocalPayload)->toArray();
        //Transform to payload Nav likes
        $updateNavPayload = fractal()->item($model, new NavCustomerTransformer())->toArray();
        //Manually add prefetched ETag
        $updateNavPayload['ETag'] = $navCustomerRes['data']['ETag'];

        $res = $nav->updateCustomer($customer->nav_no, $updateNavPayload['ETag'], $updateNavPayload);

        if ($res['success']) {
            //Update customer with update record
            $customer->fill($updateLocalPayload);
            $customer->save();
            $this->api->processed = 1;
            $this->api->save();
        } else {
            \Log::error("Customer(shopify: {$customer->shopify_id}): " . $res['error']);
            $this->fail();
        }
    }
}
