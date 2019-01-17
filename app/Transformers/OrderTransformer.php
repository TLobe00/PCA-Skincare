<?php

namespace App\Transformers;

use Carbon\Carbon;
use League\ISO3166\ISO3166;

class OrderTransformer extends aBaseNavTransformer {
    /**
     * @var array
     */
    protected $map = [
        'id'               => 'shopify_id',
        'name'             => 'shopify_name',
        'currency'         => 'currency_code',
        'billing_address'  => [
            'first_name'    => 'bill_to_contact',
            'address1'      => 'bill_to_address',
            'address2'      => 'bill_to_address2',
            'city'          => 'bill_to_city',
            'province_code' => 'bill_to_county',
            'zip'           => 'bill_to_post_code',
            'phone'         => 'bill_to_phone_no',
            'company'       => 'bill_to_name',
        ],
        'payment_details'  => [
            'credit_card_number' => 'cc_to_authorize',
        ],
        'shipping_address' => [
            'first_name'    => 'ship_to_contact',
            'address1'      => 'ship_to_address',
            'address2'      => 'ship_to_address2',
            'city'          => 'ship_to_city',
            'province_code' => 'ship_to_county',
            'zip'           => 'ship_to_post_code',
            'phone'         => 'ship_to_phone_no',
            'company'       => 'ship_to_name',
            'country_code'  => 'ship_to_country_region_code',
        ],
        'created_at'       => 'created_at',
        'updated_at'       => 'updated_at',
        //        'app_id'                  => 'app_id',
        //        'billing_address'         => 'billing_address',
        //        'browser_id'              => 'browser_id',
        //        'buyer_accepts_marketing' => 'buyer_accepts_marketing',
        //        'cancel_reason'           => 'cancel_reason',
        //        'cancelled_at'            => 'cancelled_at',
        //        'cart_token'              => 'cart_token',
        //        'client_details'          => 'client_details',
        //        'closed_at'               => 'closed_at',
        //        'created_at'              => 'created_at',
        //        'currency'                => 'currency',
        //        'customer'                => 'customer',
        //        'customer_locale'         => 'customer_locale',
        //        'discount_codes'          => 'discount_codes',
        //        'email'                   => 'email',
        //        'financial_status'        => 'financial_status',
        //        'fulfillments'            => 'fulfillments',
        //        'fulfillment_status'      => 'fulfillment_status',
        //        'tags'                    => 'tags',
        //        'gateway'                 => 'gateway',
        //        'landing_site'            => 'landing_site',
        //        'line_items'              => 'line_items',
        //        'location_id'             => 'location_id',
        //        'name'                    => 'name',
        //        'note'                    => 'note',
        //        'note_attributes'         => 'note_attributes',
        //        'number'                  => 'number',
        //        'order_number'            => 'order_number',
        //        'payment_details'         => 'payment_details',
        //        'payment_gateway_names'   => 'payment_gateway_names',
        //        'phone'                   => 'phone',
        //        'processed_at'            => 'processed_at',
        //        'processing_method'       => 'processing_method',
        //        'referring_site'          => 'referring_site',
        //        'refunds'                 => 'refunds',
        //        'shipping_address'        => 'shipping_address',
        //        'shipping_lines'          => 'shipping_lines',
        //        'source_name'             => 'source_name',
        //        'subtotal_price'          => 'subtotal_price',
        //        'tax_lines'               => 'tax_lines',
        //        'taxes_included'          => 'taxes_included',
        //        'token'                   => 'token',
        //        'total_discounts'         => 'total_discounts',
        //        'total_line_items_price'  => 'total_line_items_price',
        //        'total_price'             => 'total_price',
        //        'total_tax'               => 'total_tax',
        //        'total_weight'            => 'total_weight',
        //        'updated_at'              => 'updated_at',
        //        'user_id'                 => 'user_id',
        //        'order_status_url'        => 'order_status_url',
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

    /**
     * @param $payload
     * @return null|string
     */
    protected function handleBillingAddressFirstName($payload) {
        $name = null;

        if(isset($payload['billing_address']) && isset($payload['billing_address']['first_name'])) {
            $name = $payload['billing_address']['first_name'];
        }

        if(isset($payload['billing_address']) && isset($payload['billing_address']['last_name'])) {
            if(is_null($name)) {
                $name = '';
            }

            if(strlen($name) > 0) {
                $name .= ' ';
            }

            $name .= $payload['billing_address']['last_name'];
        }

        return $name;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleBillingAddressAddress1($payload) {
        $address = null;

        if(isset($payload['billing_address']) && isset($payload['billing_address']['address1'])) {
            $address = $payload['billing_address']['address1'];
        }

        return $address;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleBillingAddressAddress2($payload) {
        $address = null;

        if(isset($payload['billing_address']) && isset($payload['billing_address']['address2'])) {
            $address = $payload['billing_address']['address2'];
        }

        return $address;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleBillingAddressCity($payload) {
        $city = null;

        if(isset($payload['billing_address']) && isset($payload['billing_address']['city'])) {
            $city = $payload['billing_address']['city'];
        }

        return $city;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleBillingAddressProvinceCode($payload) {
        $val = null;

        if(isset($payload['billing_address']) && isset($payload['billing_address']['province_code'])) {
            $val = $payload['billing_address']['province_code'];
        }

        return $val;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleBillingAddressZip($payload) {
        $val = null;

        if(isset($payload['billing_address']) && isset($payload['billing_address']['zip'])) {
            $val = $payload['billing_address']['zip'];
        }

        return $val;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleBillingAddressPhone($payload) {
        $val = null;

        if(isset($payload['billing_address']) && isset($payload['billing_address']['phone'])) {
            $val = $payload['billing_address']['phone'];
        }

        return $val;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleBillingAddressCompany($payload) {
        $val = null;

        if(isset($payload['billing_address']) && isset($payload['billing_address']['company'])) {
            $val = $payload['billing_address']['company'];
        }

        return $val;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handlePaymentDetailsCreditCardNumber($payload) {
        $val = null;

        if(isset($payload['payment_details']) && isset($payload['payment_details']['credit_card_number'])) {
            $val = $payload['payment_details']['credit_card_number'];
        }

        return $val;
    }

    /**
     * @param $payload
     * @return null|string
     */
    protected function handleShippingAddressFirstName($payload) {
        $name = null;

        if(isset($payload['shipping_address']) && isset($payload['shipping_address']['first_name'])) {
            $name = $payload['shipping_address']['first_name'];
        }

        if(isset($payload['shipping_address']) && isset($payload['shipping_address']['last_name'])) {
            if(is_null($name)) {
                $name = '';
            }

            if(strlen($name) > 0) {
                $name .= ' ';
            }

            $name .= $payload['shipping_address']['last_name'];
        }

        return $name;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleShippingAddressAddress1($payload) {
        $address = null;

        if(isset($payload['shipping_address']) && isset($payload['shipping_address']['address1'])) {
            $address = $payload['shipping_address']['address1'];
        }

        return $address;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleShippingAddressAddress2($payload) {
        $address = null;

        if(isset($payload['shipping_address']) && isset($payload['shipping_address']['address2'])) {
            $address = $payload['shipping_address']['address2'];
        }

        return $address;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleShippingAddressCity($payload) {
        $city = null;

        if(isset($payload['shipping_address']) && isset($payload['shipping_address']['city'])) {
            $city = $payload['shipping_address']['city'];
        }

        return $city;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleShippingAddressProvinceCode($payload) {
        $val = null;

        if(isset($payload['shipping_address']) && isset($payload['shipping_address']['province_code'])) {
            $val = $payload['shipping_address']['province_code'];
        }

        return $val;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleShippingAddressZip($payload) {
        $val = null;

        if(isset($payload['shipping_address']) && isset($payload['shipping_address']['zip'])) {
            $val = $payload['shipping_address']['zip'];
        }

        return $val;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleShippingAddressPhone($payload) {
        $val = null;

        if(isset($payload['shipping_address']) && isset($payload['shipping_address']['phone'])) {
            $val = $payload['shipping_address']['phone'];
        }

        return $val;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleShippingAddressCompany($payload) {
        $val = null;

        if(isset($payload['shipping_address']) && isset($payload['shipping_address']['company'])) {
            $val = $payload['shipping_address']['company'];
        }

        return $val;
    }

    /**
     * @param $payload
     * @return null
     */
    protected function handleShippingAddressCountryCode($payload) {
        $val = null;

        if(isset($payload['shipping_address']) && isset($payload['shipping_address']['country_code'])) {
            $val = $payload['shipping_address']['country_code'];
        }

        if(!empty($val)) {
            $val = (new ISO3166())->alpha2($val);

            if($val) {
                $val = $val['alpha2'];
            }
        }

        return $val;
    }
}
