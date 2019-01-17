<?php

namespace App\Transformers;

class ProductTransformer extends aBaseCustomTransformer {
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

        //TODO: Fill out nav specific data structure
        $data = $this->mapData($payload);

        return $data;
    }
}
