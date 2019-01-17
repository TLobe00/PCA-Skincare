<?php

namespace App\Jobs;

use App\shopifyapi;
use App\Transformers\CustomerTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushCustomer implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var null
     */
    private $dbId;

    /**
     * Create a new job instance.
     *
     * @param null $dbId
     */
    public function __construct($dbId = null) {
        $this->dbId = $dbId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $response = shopifyapi::find($this->dbId);

        if (!$response) {
            \Log::error("Could not find api record: {$response->id}");
            $this->fail();
            return;
        }

        $payload = $response->savetext;

        if (!$payload) {
            \Log::error("No savetext for api record: {$response->id}");
            $this->fail();
            return;
        }

        $payload = json_decode($payload, true);
        $payload = fractal()->item($payload, new CustomerTransformer())->toArray();

        //TODO: send to nav
    }
}
