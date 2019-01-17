<?php

namespace App\Jobs\ToNav;

use App\Refund;
use App\Services\NavApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushRefundToNav implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Refund
     */
    protected $refund;

    /**
     * Create a new job instance.
     *
     * @param Refund $refund
     */
    public function __construct(Refund $refund) {
        $this->refund = $refund;
    }

    /**
     * Execute the job.
     *
     * @param NavApiService $nav
     * @return void
     */
    public function handle(NavApiService $nav) {
        $payload = fractal()->item($this->refund->toArray(), new NavOrderTransformer())->toArray();
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
