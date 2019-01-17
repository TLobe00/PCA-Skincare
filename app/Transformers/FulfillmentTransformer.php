<?php

namespace App\Transformers;

class FulfillmentTransformer extends aBaseNavTransformer {
    /**
     * @var array
     */
    protected $map = [
        'created_at'                   => 'created_at',
        'line_items'                   => 'line_items',
        'location_id'                  => 'location_id',
        'notify_customer'              => 'notify_customer',
        'order_id'                     => 'order_id',
        'receipt'                      => 'receipt',
        'service'                      => 'service',
        'shipment_status'              => 'shipment_status',
        'status'                       => 'status',
        'tracking_company'             => 'tracking_company',
        'tracking_number'              => 'tracking_number',
        'tracking_numbers'             => 'tracking_numbers',
        'tracking_url'                 => 'tracking_url',
        'tracking_urls'                => 'tracking_urls',
        'updated_at'                   => 'updated_at',
        'variant_inventory_management' => 'variant_inventory_management',
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
     * @return mixed
     */
    protected function handleLineItems($payload) {
        return fractal()->collection($payload, new LineItemTransformer())->toArray();
    }
}
