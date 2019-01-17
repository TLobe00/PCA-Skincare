<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class webhook_calls extends Model {
    /**
     * @var string
     */
    protected $table = 'webhook_calls';

    /**
     * @var array
     */
    protected $fillable = ['webhook_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apis() {
        return $this->hasMany(shopifyapi::class, 'webhook_call_id');
    }

    /**
     * @param $query
     * @param $name
     * @return mixed
     */
    public function scopeForName($query, $name) {
        return $query->where('webhook_name', $name);
    }
}
