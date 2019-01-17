<?php

namespace App\Transformers;


use Carbon\Carbon;

class CustomerTransformer extends aBaseNavTransformer {
    /**
     * @var array
     */
    protected $map = [
        'id'                     => 'shopify_id',
        'name'                   => 'name',
        'address'                => 'address',
        'city'                   => 'city',
        'county'                 => 'county',
        'post_code'              => 'post_code',
        'country_region_code'    => 'country_region_code',
        'phone'                  => 'phone_no',
        'tax_exempt'             => 'tax_liable',
        'email'                  => 'e_mail',
        'note'                   => 'memo',
        'created_at'             => 'created_at',
        'updated_at'             => 'updated_at',

        //'addresses'  => 'addresses',
        //'accepts_marketing'    => 'accepts_marketing',
        //'default_address'      => 'default_address',
        //'last_name'  => 'last_name',
        //'last_order_id'        => null,
        //'last_order_name'      => null,
        //'metafield'            => null,
        //'multipass_identifier' => null,
        //'orders_count'         => 'orders_count',
        //'tags'                 => 'tags',
        //'total_spent'          => 'total_spent',
        //'verified_email'       => 'verified_email',
    ];

    /**
     * Transformer for Shopify to Nav customer data
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
     * @return null
     */
    protected function handleAddress($payload) {
        $address = null;

        if (isset($payload['default_address']) && isset($payload['default_address']['address1'])) {
            $address = $payload['default_address']['address1'];
        }

        return $address;
    }

    protected function handleCity($payload) {
        $city = null;

        if (isset($payload['default_address']) && isset($payload['default_address']['city'])) {
            $city = $payload['default_address']['city'];
        }

        return $city;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleCounty($payload) {
        $county = null;

        if (isset($payload['default_address']) && isset($payload['default_address']['province_code'])) {
            $county = $payload['default_address']['province_code'];
        }

        return $county;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleCountryRegionCode($payload) {
        $country = null;

        if (isset($payload['default_address']) && isset($payload['default_address']['country_code'])) {
            $country = $payload['default_address']['country_code'];
        }

        if ($country === 'US') {
            $country = 'USA';
        }

        return $country;
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
     * @return string
     */
    protected function handleName($payload) {
        $name = '';

        if (isset($payload['first_name'])) {
            $name .= $payload['first_name'];
        }

        if (isset($payload['last_name'])) {
            if (strlen($name) > 0) {
                $name .= ' ';
            }

            $name .= $payload['last_name'];
        }

        $name = trim($name);

        return $name;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handlePostCode($payload) {
        $county = null;

        if (isset($payload['default_address']) && isset($payload['default_address']['zip'])) {
            $county = $payload['default_address']['zip'];
        }

        return $county;
    }

    /**
     * @param $payload
     * @return bool|null
     */
    protected function handleTaxExempt($payload) {
        $liable = null;

        if (isset($payload['tax_exempt'])) {
            $liable = !$payload['tax_exempt'];
        }

        return $liable;
    }

    /**
     * @param $payload
     * @return null|string
     */
    protected function handleUpdatedAt($payload) {
        $updated = null;

        if (isset($payload['updated_at'])) {
            $updated = Carbon::parse($payload['updated_at'])->tz('UTC')->toDateTimeString();
        }

        return $updated;
    }
}
