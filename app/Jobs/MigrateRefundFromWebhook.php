<?php

namespace App\Jobs;

use App\Refund;
use App\shopifyapi;
use App\Transformers\RefundTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MigrateRefundFromWebhook implements ShouldQueue {
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
     * @return void
     */
    public function handle() {
        $response = json_decode($this->api->savetext, true);

        if (is_null($response) || empty($response)) {
            \Log::error("Refund({$this->api->id}: the shopify api response is invalid.");
            $this->fail();

            return;
        }

        $refund = Refund::where('shopify_id', $response['id'])->first();

        if ($refund) {
            \Log::error("Refund({$this->api->id}): this refund has already been migrated to the refunds table.");
            $this->fail();

            return;
        }

        $payload = fractal()->item($response, new RefundTransformer())->toArray();

        Refund::create($payload);

        $this->api->update([
            'processed' => 1,
        ]);
    }
}
