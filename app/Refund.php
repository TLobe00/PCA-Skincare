<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model {
    public $timestamps = false;

    protected $fillable = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

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
