<?php

namespace App\Transformers;

class CustomerAddressTransformer extends aBaseCustomTransformer {
    /**
     * @var array
     */
    protected $map = [
        'customer_id'   => 'customer_id',
        'first_name'    => 'first_name',
        'last_name'     => 'last_name',
        'company'       => 'company',
        'address1'      => 'address1',
        'address2'      => 'address2',
        'city'          => 'city',
        'province'      => 'province',
        'country'       => 'country',
        'zip'           => 'zip',
        'phone'         => 'phone',
        'name'          => 'name',
        'province_code' => 'province_code',
        'country_code'  => 'country_code',
        'country_name'  => 'country_name',
        'default'       => 'default',
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
}
