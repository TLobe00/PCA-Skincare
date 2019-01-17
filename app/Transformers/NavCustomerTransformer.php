<?php

namespace App\Transformers;


class NavCustomerTransformer extends aBaseNavTransformer {
    /**
     * @var bool
     */
    protected $formatKeys = true;

    /**
     * @var array
     */
    protected $ignoreFormatting = ['etag'];

    /**
     * @var array
     */
    protected $map = [
        'name'                         => 'name',
        'address'                      => 'address',
        'address_2'                    => 'address_2',
        'city'                         => 'city',
        'county'                       => 'county',
        'post_code'                    => 'post_code',
        'customer_posting_group'       => 'customer_posting_group',
        'country_region_code'          => 'country_region_code',
        'business_type_code'           => 'business_type_code',
        'business_specialization_code' => 'business_specialization_code',
        'phone_no'                     => 'phone_no',
        'tax_liable'                   => 'tax_liable',
        'e_mail'                       => 'e_mail',
        'memo'                         => 'memo',
        'memo_2'                       => 'memo_2',
    ];

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
     * @param $key
     * @return mixed|string
     */
    protected function keyFormatter($key) {
        $key = str_replace('_', ' ', $key);
        $key = title_case($key);
        $key = str_replace(' ', '_', $key);

        return $key;
    }

    /**
     * @param $payload
     * @return bool
     */
    protected function handleTaxLiable($payload) {
        $val = true;

        if (isset($payload['tax_liable'])) {
            $val = boolval($payload['tax_liable']);
        }

        return $val;
    }
}
