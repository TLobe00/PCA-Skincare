<?php

namespace App\Jobs;

use App\Customer;
use App\shopifyapi;
use App\Transformers\CustomerTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MigrateCustomerFromWebhook implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var shopifyapi
     */
    private $api;

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
     * @return void
     */
    public function handle() {
        $response = json_decode($this->api->savetext, true);

        if (is_null($response) || empty($response)) {
            \Log::error("Customer({$this->api->id}: the shopify api response is invalid.");
            $this->fail();

            return;
        }

        $customer = Customer::where('shopify_id', $response['id'])->first();

        if ($customer) {
            \Log::error("Customer({$this->api->id}): this customer has already been migrated to the customers table.");
            $this->fail();

            return;
        }

        $payload = fractal()->item($response, new CustomerTransformer())->toArray();

        Customer::create($payload);

        $this->api->update([
            'processed' => 1,
        ]);
    }
}
