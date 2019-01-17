<?php

namespace App\Transformers;

class NavOrderTransformer extends aBaseNavTransformer {
    /**
     * @var array
     */
    protected $map = [
        'bill_to_name'                => 'Bill_to_Name',
        'bill_to_address'             => 'Bill_to_Address',
        'bill_to_address2'            => 'Bill_to_Address_2',
        'bill_to_city'                => 'Bill_to_City',
        'bill_to_county'              => 'Bill_to_County',
        'bill_to_post_code'           => 'Bill_to_Post_Code',
        'bill_to_customer_no'         => 'Bill_to_Customer_No',
        'bill_to_phone_no'            => 'Bill_to_Phone_No',
        'bill_to_contact'             => 'Bill_to_Contact',
        'cc_to_authorize'             => 'CC_to_Authorize',
        'ship_to_name'                => 'Ship_to_Name',
        'ship_to_address'             => 'Ship_to_Address',
        'ship_to_address2'            => 'Ship_to_Address_2',
        'ship_to_city'                => 'Ship_to_City',
        'ship_to_county'              => 'Ship_to_County',
        'ship_to_post_code'           => 'Ship_to_Post_Code',
        'ship_to_phone_no'            => 'Ship_to_Phone_No',
        'ship_to_contact'             => 'Ship_to_Contact',
        'ship_to_country_region_code' => 'Ship_to_Country_Region_Code',
        'sell_to_customer_no'         => 'Sell_to_Customer_No',
        'shopify_name'                  => 'External_Document_No',
        'location_code'               => 'Location_Code',
        'caller_id'                   => 'Caller_ID',
        'salesperson_code'            => 'Salesperson_Code',
        'campaign_no'                 => 'Campaign_No',
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
