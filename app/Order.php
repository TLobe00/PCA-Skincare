<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'shopify_id', 'is_update', 'nav_no', 'etag', 'created_at', 'updated_at', 'currency_code', 'bill_to_customer_no',
        'bill_to_name', 'bill_to_address', 'bill_to_address_2', 'bill_to_city', 'bill_to_county', 'bill_to_post_code',
        'bill_to_phone_no', 'bill_to_contact', 'cc_to_authorize', 'ship_to_name', 'ship_to_address', 'ship_to_address_2',
        'ship_to_city', 'ship_to_county', 'ship_to_post_code', 'ship_to_country_region_code', 'ship_to_phone_no',
        'ship_to_contact', 'sell_to_address', 'sell_to_address_2', 'sell_to_city', 'sell_to_contact', 'sell_to_contact_no',
        'sell_to_county', 'sell_to_customer_name', 'sell_to_customer_no', 'sell_to_phone_no', 'sell_to_post_code'
    ];

    /**
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    protected $attributes = [
        'location_code'       => 'DC',
        'caller_id'           => 'INET',
        'salesperson_code' => 'ONLINE',
    ];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function apiResponse() {
        return $this->belongsTo(shopifyapi::class, 'api_id');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeUnprocessed($query) {
        return $query->where(function ($q) {
            $q->orWhereNull('nav_no')->orWhereNull('etag');
        });
    }
}
