<?php

namespace App\Jobs;

use App\Customer;
use App\Order;
use App\Services\NavApiService;
use App\shopifyapi;
use App\Transformers\CustomerTransformer;
use App\Transformers\OrderTransformer;
use App\webhook_calls;
use App\Jobs\ToNav\PushCustomerToNav;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use RocketCode\Shopify\Client;

class MigrateOrderFromWebhook implements ShouldQueue {
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
     * @param NavApiService $nav
     * @return void
     */
    public function handle(NavApiService $nav) {
        $response = json_decode($this->api->savetext, true);

        if (is_null($response) || empty($response)) {
            \Log::error("Order({$this->api->id}: the shopify api response is invalid.");
            $this->fail();

            return;
        }

        $order = Order::where('shopify_id', $response['id'])->first();

        if ($order) {
            \Log::error("Order({$this->api->id}): this order has already been migrated to the orders table.");
            $this->fail();

            return;
        }

        $payload = fractal()->item($response, new OrderTransformer())->toArray();
        $order = Order::create($payload);

        if (isset($response['customer'])) {
            $customer = Customer::orWhere('shopify_id', $response['customer']['id'])
                ->orWhere('email', $response['email'])
                ->first();

            if ($customer && $customer->nav_no) {
                $order->bill_to_customer_no = $customer->nav_no;
                $order->sell_to_customer_no = $customer->nav_no;
            } else if ($customer && !$customer->nav_no) {
                //Try to find customer by email in Nav
                $navCustomerRes = $nav->getCustomerByEmail($response['email']);

                if ($navCustomerRes['success'] && sizeof($navCustomerRes['data']['value'])) {
                    $navC = $navCustomerRes['data']['value'][0];
                    $customer->nav_no = $navC['No'];
                    $customer->save();

                    $order->bill_to_customer_no = $customer->nav_no;
                    $order->sell_to_customer_no = $customer->nav_no;
                } else {
                                        
                    PushCustomerToNav::dispatch($customer);
                    $customer2 = Customer::where('id', $customer->id)->first();
                    $order->bill_to_customer_no = $customer2->nav_no;
                    $order->sell_to_customer_no = $customer2->nav_no;
                    
                    \Log::error("Order(shopify: {$order->id})1: the nav_no for this customer hasn't been saved locally and we could not find the customer in Nav");
                    //$this->fail();

                    //return;
                }
            } else {
                $navCustomerRes = $nav->getCustomerByEmail($response['email']);

                if ($navCustomerRes['success'] && sizeof($navCustomerRes['data']['value'])) {
                    $navC = $navCustomerRes['data']['value'][0];
                    
                    $customer = Customer::create([
                        'nav_no'              => $navC['No'],
                        'e_mail'              => $navC['E_Mail'],
                        'name'                => $navC['Name'],
                        'address'             => $navC['Address'],
                        'address_2'           => $navC['Address_2'],
                        'city'                => $navC['City'],
                        'county'              => $navC['County'],
                        'post_code'           => $navC['Post_Code'],
                        'country_region_code' => $navC['Country_Region_Code'],
                        'phone_no'            => $navC['Phone_No'],
                        'tax_liable'          => $navC['Tax_Liable'] ? 1 : 0,
                        'memo'                => $navC['Memo'],
                        'memo_2'              => $navC['Memo_2'],
                        'created_at'          => Carbon::now()->toDateTimeString(),
                        'updated_at'          => Carbon::now()->toDateTimeString(),
                    ]);

                    $order->bill_to_customer_no = $customer->nav_no;
                    $order->sell_to_customer_no = $customer->nav_no;
                } else {
                    
                    
                    $customer = Customer::create([
                        'shopify_id'          => $response['customer']['id'],
                        'e_mail'              => $response['customer']['email'],
                        'name'                => $response['customer']['default_address']['name'],
                        'address'             => $response['customer']['default_address']['address1'],
                        'address_2'           => $response['customer']['default_address']['address2'],
                        'city'                => $response['customer']['default_address']['city'],
                        'county'              => $response['customer']['default_address']['province_code'],
                        'post_code'           => $response['customer']['default_address']['zip'],
                        'country_region_code' => 'USA',
                        'phone_no'            => $response['customer']['phone'],
                        'tax_liable'          => $response['customer']['tax_exempt'] ? 1 : 0,
                        'memo'                => $response['customer']['note'],
                        'memo_2'              => '',
                        'created_at'          => Carbon::now()->toDateTimeString(),
                        'updated_at'          => Carbon::now()->toDateTimeString(),
                    ]);
                    
                    PushCustomerToNav::dispatch($customer);
                    $customer2 = Customer::where('id', $customer->id)->first();
                    $order->bill_to_customer_no = $customer2->nav_no;
                    $order->sell_to_customer_no = $customer2->nav_no;
                    
                    \Log::info($customer);
                    \Log::error("Order(shopify: {$order->id})2: the nav_no for this customer hasn't been saved locally and we could not find the customer in Nav");
                }
                /*
                // We don't have the customer locally so check if it's in shopify and in nav
                $shopifyCustomerRes = json_decode(Client::Customer()->search(['query' => $response['email']]), true);
                
                \Log::info($shopifyCustomerRes);
                
                if(array_key_exists('errors', $shopifyCustomerRes) || empty($shopifyCustomerRes)) {
                    //TODO: what now? ...we couldn't find the customer in shopify
                } else {
                    $customerPayload = $shopifyCustomerRes[0];
                    $webhooksCall = webhook_calls::forName('Customer creation')->whereNotNull('id')->first();

                    $shopifyapisave = new shopifyapi;
                    $shopifyapisave->savetext = json_encode($customerPayload);

                    if($webhooksCall) {
                        $shopifyapisave->webhook_call_id = $webhooksCall->id;
                    }

                    $shopifyapisave->save();
                    $customerPayload = json_decode($shopifyapisave->savetext, true);
                    $payload = fractal()->item($customerPayload, new CustomerTransformer())->toArray();

                    $customer = Customer::create($payload);

                    $shopifyapisave->update([
                        'processed' => 1,
                    ]);
                }

                //TODO @ aclinton/TLo: start back up here
                // $customer might be defined if it was found above, but we still don't have a nav_no

                //TODO for TLo: This is not working properly. Need to fix odata query syntax maybe?
                $navCustomerRes = $nav->getCustomerByEmail($response['email']);

                if($navCustomerRes['success'] && sizeof($navCustomerRes['data']['value'])) {
                    $navC = $navCustomerRes['data']['value'][0];

                    if($customer) {
                        $customer->nav_no = $navC['No'];
                        $customer->save();

                        $order->bill_to_customer_no = $customer->nav_no;
                        $order->sell_to_customer_no = $customer->nav_no;
                    } else {
                        //TODO: customer wasn't found previously in nav on line 90, we will need to create in shopify
                        // and then assign the nav_no
                    }
                } else {
                    //TODO: couldn't find the customer in nav so we can't get nav_no...order is out of luck?
                }
                */
            }
        }

        $order->apiResponse()->associate($this->api);
        $order->save();

        $this->api->update([
            'processed' => 1,
        ]);
    }
}
