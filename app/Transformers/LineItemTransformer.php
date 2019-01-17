<?php

namespace App\Transformers;

class LineItemTransformer extends aBaseNavTransformer {
    /**
     * @var array
     */
    protected $map = [
        //'variant_id'                   => 'variant_id',
        'title'         => 'Description',
        'quantity'      => 'Quantity',
        'price'         => 'Unit_Price',
        'sku'           => 'No',
        'variant_title' => 'Description_2',
        //        'vendor'                       => 'vendor',
        //'fulfillment_service'          => 'fulfillment_service',
        //        'product_id'                   => 'product_id',
        //        'requires_shipping' => 'requires_shipping',
//        'taxable'       => 'Tax_Liable',
        //'gift_card'                    => 'gift_card',
        //'name'                         => 'name',
        //'variant_inventory_management' => 'variant_inventory_management',
        //        'properties'                   => 'properties',
        //        'product_exists'               => 'product_exists',
        //        'fulfillment_quantity'         => 'fulfillment_quantity',
        //        'grams'                        => 'grams',
        //        'total_discount'    => 'total_discount',
        //        'fulfillment_status'           => 'fulfillment_status',
        //        'tax_lines'                    => 'tax_lines',
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
     * @return null
     */
    protected function handleSku($payload) {
        $val = null;

        if (isset($payload['sku']) && !empty($payload['sku'])) {
            $parts = explode('_', $payload['sku']);
            $val = $parts[0];
        }

        return $val;
    }

    /**
     * @param $payload
     * @return null|string
     */
    protected function handleQuantity($payload) {
        $val = null;

        if (isset($payload['quantity']) && !empty($payload['quantity'])) {
            $val = $payload['quantity'];
            $val = "{$val}";
        }

        return $val;
    }
    
    protected function handleTitle($payload) {
        $val = null;
        if (isset($payload['title']) && !empty($payload['title'])) {
            $val = substr($payload['title'],0,49);
            $val = "{$val}";
        }
        return $val;
    }
}
