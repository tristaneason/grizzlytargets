<?php

class EnUniShipfreightFdo
{

    public $en_fdo_meta_data;

    /**
     * product hazardous.
     * @param array $package
     * @param array $en_fdo_meta_data
     * @return array
     */
    public function en_package_hazardous($package, $en_fdo_meta_data)
    {
        $hazmat = (isset($package['hazardousMaterial']) && $package['hazardousMaterial'] == 'yes') ? true : false;
        $en_fdo_meta_data['accessorials']['hazmat'] = $hazmat;
        return $en_fdo_meta_data;
    }

    /**
     * arrange cart objects.
     * @param type $package
     * @return array
     */
    public function en_cart_package($package)
    {

        $this->en_fdo_meta_data['plugin_type'] = 'ltl';
        $this->en_fdo_meta_data['plugin_name'] = 'unishippers_freight';
        $accessorials['residential'] = get_option('wc_settings_unishipper_residential_delivery') == 'yes' ? true : false;
        $accessorials['liftgate'] = get_option('wc_settings_unishippers_freight_lift_gate_delivery') == 'yes' ? true : false;
        $this->en_fdo_meta_data['accessorials'] = $accessorials;

        (isset($package['items'])) ? $this->en_package_items($package['items']) : '';
        (isset($package['origin'])) ? $this->en_package_address($package['origin']) : '';

        return $this->en_fdo_meta_data;
    }

    /**
     * arrange items.
     * @param type $items
     */
    public function en_package_items($items)
    {
        $this->en_fdo_meta_data['items'] = [];
        foreach ($items as $item_key => $item_data) {
            $productId = $productName = $productQty = $actualProductPrice = $products = $productPrice = $productWeight = $productLength = $productWidth = $productHeight = $ptype = $hazardousMaterial = $productType = $productSku = $productClass = $attributes = $variantId = $nestedMaterial = $nestedPercentage = $nestedDimension = $nestedItems = $stakingProperty = '';
            extract($item_data);

            $meta_data = [];
            if (!empty($attributes)) {
                foreach ($attributes as $attr_key => $attr_value) {
                    $meta_data[] = [
                        'key' => $attr_key,
                        'value' => $attr_value,
                    ];
                }
            }

            $item = [
                'id' => $productId,
                'name' => $productName,
                'quantity' => $productQty,
                'price' => $productPrice,
                'price' => $actualProductPrice,
                'weight' => $productWeight,
                'length' => $productLength,
                'width' => $productWidth,
                'height' => $productHeight,
                'type' => $ptype,
                'hazmat' => $hazardousMaterial,
                'product' => $productType,
                'sku' => $productSku,
                'attributes' => $attributes,
                'shipping_class' => $productClass,
                'variant_id' => $variantId,
                'meta_data' => $meta_data,
                // Nesting
                'nested_material' => $nestedMaterial,
                'nested_percentage' => $nestedPercentage,
                'nested_dimension' => $nestedDimension,
                'nested_items' => $nestedItems,
                'staking_property' => $stakingProperty,
            ];

            // Hook for flexibility adding to package
            $item = apply_filters('en_fdo_package', $item, $item_data);
            $this->en_fdo_meta_data['items'][$item_key] = $item;
        }
    }

    /**
     * Get address.
     * @param array $address
     */
    public function en_package_address($address)
    {
        (isset($address['locationId'])) ? $address['id'] = $address['locationId'] : '';
        $this->en_fdo_meta_data['address'] = $address;
    }

}
