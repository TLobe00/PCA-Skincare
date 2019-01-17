<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class shopifyapi extends Model
{
    /**
     * @var string
     */
	protected $table = 'api';

    /**
     * @var array
     */
	protected $fillable = ['savetext', 'processed', 'webhook_call_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function webhook() {
	    return $this->belongsTo(webhook_calls::class, 'webhook_call_id');
    }

    /**
     * @return bool
     */
    public function getIsOrderCreateAttribute() {
	    return optional($this->webhook)->id == 3;
    }

    /**
     * @return bool
     */
    public function getIsCustomerCreateAttribute() {
        return optional($this->webhook)->id == 1;
    }

    /* === QUERY SCOPES === */

    /**
     * @param $query
     * @return mixed
     */
    public function scopeUnprocessed($query) {
	    return $query->where(function ($q) {
	        $q->orWhere('processed', 0)->orWhereNull('processed');
        });
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeCustomers($query) {
        $customerWebhooks = ['Customer creation', 'Customer update'];

        return $query->whereHas('webhook', function ($q) use($customerWebhooks) {
            $q->whereIn('webhook_name', $customerWebhooks);
        });
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNewCustomers($query) {
	    return $query->whereHas('webhook', function ($q) {
            $q->where('webhook_name', 'Customer creation');
        });
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeUpdatedCustomers($query) {
        return $query->whereHas('webhook', function ($q) {
            $q->where('webhook_name', 'Customer update');
        });
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNewOrders($query) {
        return $query->whereHas('webhook', function ($q) {
            $q->where('webhook_name', 'Order creation');
        });
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOrders($query) {
        $orderWebhookNames = ['Order creation', 'Order update' /*, 'Refund create'*/];

        return $query->whereHas('webhook', function ($q) use($orderWebhookNames) {
            $q->whereIn('webhook_name', $orderWebhookNames);
        });
    }
}
