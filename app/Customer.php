<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'shopify_id', 'nav_no', 'name', 'address', 'address_2', 'city', 'county', 'post_code',
        'country_region_code', 'customer_posting_group', 'phone_no', 'tax_liable', 'e_mail', 'memo', 'etag', 'created_at', 'updated_at',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'customer_posting_group'       => 'CUST',
        'business_type_code'           => 'CONSUMER',
        'business_specialization_code' => 'CONSUMER',
    ];

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
