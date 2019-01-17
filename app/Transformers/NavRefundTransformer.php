<?php

namespace App\Transformers;


class NavRefundTransformer extends aBaseNavTransformer {
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
}
