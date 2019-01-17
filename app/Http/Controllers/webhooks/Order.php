<?php

namespace App\Http\Controllers\webhooks;


use App\Http\Controllers\Controller;
use App\Jobs\MigrateOrderFromWebhook;
use App\Jobs\ToNav\PushOrderUpdatetoNav;
use App\shopifyapi;
use App\webhook_calls;
use Illuminate\Http\Request;
use Log;

class Order extends Controller {
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createOrder(Request $request) {
        $data = collect($request->json()->all());
        $webhooks_call = webhook_calls::forName('Order creation')->whereNotNull('id')->first();

        $shopifyapisave = new shopifyapi;
        $shopifyapisave->savetext = $data;

        if ($webhooks_call) {
            $shopifyapisave->webhook_call_id = $webhooks_call->id;
        }

        $shopifyapisave->save();

        MigrateOrderFromWebhook::dispatch($shopifyapisave);

        return response("OK");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateOrder(Request $request) {
        $data = collect($request->json()->all());
        $webhooks_call = webhook_calls::forName('Order update')->whereNotNull('id')->first();

        $shopifyapisave = new shopifyapi;
        $shopifyapisave->savetext = $data;

        if($webhooks_call) {
            $shopifyapisave->webhook_call_id = $webhooks_call->id;
        }

        $shopifyapisave->save();

        PushOrderUpdatetoNav::dispatch(shopifyapi::find($shopifyapisave->id));

        return response("OK");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function refundOrder(Request $request) {
        $data = collect($request->json()->all());
        $webhooks_call = webhook_calls::forName('Refund create')->whereNotNull('id')->first();

        $shopifyapisave = new shopifyapi;
        $shopifyapisave->savetext = $data;

        if($webhooks_call) {
            $shopifyapisave->webhook_call_id = $webhooks_call->id;
        }

        $shopifyapisave->save();

        Log::info($data);
        return response("OK");
    }
}
