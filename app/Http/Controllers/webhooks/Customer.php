<?php

namespace App\Http\Controllers\webhooks;

use App\Jobs\MigrateCustomerFromWebhook;
use App\Jobs\ToNav\PushCustomerUpdateToNav;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use App\shopifyapi;
use App\webhook_calls;

class Customer extends Controller {
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createCustomer(Request $request) {
        $data = collect($request->json()->all());
        $webhooks_call = webhook_calls::forName('Customer creation')->whereNotNull('id')->first();

        $shopifyapisave = new shopifyapi;
        $shopifyapisave->savetext = $data;

        if($webhooks_call) {
            $shopifyapisave->webhook_call_id = $webhooks_call->id;
        }

        $shopifyapisave->save();

        // Migrate api response to the customers table
        MigrateCustomerFromWebhook::dispatch(shopifyapi::find($shopifyapisave->id));

        return response("OK");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateCustomer(Request $request) {
        $data = collect($request->json()->all());
        $webhooks_call = webhook_calls::forName('Customer update')->whereNotNull('id')->first();

        $shopifyapisave = new shopifyapi;
        $shopifyapisave->savetext = $data;

        if($webhooks_call) {
            $shopifyapisave->webhook_call_id = $webhooks_call->id;
        }

        $shopifyapisave->save();

        PushCustomerUpdateToNav::dispatch(shopifyapi::find($shopifyapisave->id));

        return response("OK");
    }
}
