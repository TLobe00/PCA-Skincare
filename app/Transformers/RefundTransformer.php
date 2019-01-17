<?php

namespace App\Transformers;

use Carbon\Carbon;

class RefundTransformer extends aBaseNavTransformer {
    protected $map = [];

    /**
     * A Fractal transformer.
     *
     * @param null $payload
     * @return array
     */
    public function transform($payload = null) {
        if (is_null($payload)) {
            return [];
        }

        $data = $this->mapData($payload);

        return $data;
    }

    /**
     * @param $payload
     * @return null|string
     */
    protected function handleCreatedAt($payload) {
        $created = null;

        if (isset($payload['created_at'])) {
            $created = Carbon::parse($payload['created_at'])->tz('UTC')->toDateTimeString();
        }

        return $created;
    }

    /**
     * @param $payload
     * @return null|string
     */
    protected function handleUpdatedAt($payload) {
        $created = null;

        if (isset($payload['updated_at'])) {
            $created = Carbon::parse($payload['updated_at'])->tz('UTC')->toDateTimeString();
        }

        return $created;
    }
}
