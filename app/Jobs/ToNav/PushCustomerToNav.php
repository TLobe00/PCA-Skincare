<?php

namespace App\Jobs\ToNav;

use App\Customer;
use App\Services\NavApiService;
use App\Transformers\NavCustomerTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushCustomerToNav implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * Create a new job instance.
     *
     * @param Customer $customer
     */
    public function __construct(Customer $customer) {
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     *
     * @param NavApiService $nav
     * @return void
     */
    public function handle(NavApiService $nav) {
        $payload = fractal()->item($this->customer->toArray(), new NavCustomerTransformer())->toArray();
        $res = $nav->createCustomer($payload);

        if($res['success']) {
            if(isset($res['data']['No'])) {
                $this->customer->nav_no = $res['data']['No'];
            }

            if(isset($res['data']['ETag'])) {
                $this->customer->etag = $res['data']['ETag'];
            }

            $this->customer->save();
        } else {
            \Log::error("Customer({$this->customer->shopify_id}): " . $res['error']);
            $this->fail();
        }
    }
}
