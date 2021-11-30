<?php

/**
 *  Box sizes template
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists("EnUpdateProductNestedOptions")) {

    class EnUpdateProductNestedOptions
    {

        /**
         * Constructor.
         */
        public function __construct($action)
        {

            if ($action == 'hooks') {
                $this->en_add_nested_simple_product_hooks();
                $this->en_add_variable_nested_product_hooks();
            }
        }

        /**
         * Add simple product fields.
         */
        public function en_add_nested_simple_product_hooks()
        {

            /* Add simple product fields */
            add_action(
                'woocommerce_product_options_shipping', array($this, 'en_show_nested_product_fields'), 110
            );
            add_action(
                'woocommerce_process_product_meta', array($this, 'en_save_nested_product_fields'), 20
            );
        }

        /**
         * Add variable product fields.
         */
        public function en_add_variable_nested_product_hooks()
        {

            add_action(
                'woocommerce_product_after_variable_attributes', array($this, 'en_show_nested_product_fields'), 110, 3
            );

            add_action(
                'woocommerce_save_product_variation', array($this, 'en_save_nested_product_fields'), 20
            );
        }

        /**
         * Save the simple product fields.
         * @param int $post_id
         */
        function en_save_nested_product_fields($post_id)
        {

            if (isset($post_id) && $post_id > 0) {

                //               add / update Nested Material
                $nestedMaterial = isset($_POST['_nestedMaterials'.$post_id]) ? $_POST['_nestedMaterials'.$post_id]: '';
                update_post_meta($post_id, '_nestedMaterials', esc_attr($nestedMaterial));

//              add / update Nested Percentage
                $nestedPercentage = isset($_POST['_nestedPercentage'.$post_id]) && !empty($_POST['_nestedPercentage'.$post_id]) ? $_POST['_nestedPercentage'.$post_id] : 0;

                update_post_meta($post_id, '_nestedPercentage', esc_attr($nestedPercentage));

//              add / update Nested Dimension
                $nestedDimension = $_POST['_nestedDimension'.$post_id];
                update_post_meta($post_id, '_nestedDimension', esc_attr($nestedDimension));

//              add / update Max. Nested Items
                $maxNestedItems = $_POST['_maxNestedItems'.$post_id];
                update_post_meta($post_id, '_maxNestedItems', esc_attr($maxNestedItems));
//              add / update Max. Nested Items
                $nestedStakingProperty = $_POST['_nestedStakingProperty'.$post_id];
                update_post_meta($post_id, '_nestedStakingProperty', esc_attr($nestedStakingProperty));
            }
        }

        /**
         * Show product fields in variation and simple product.
         * @param array $loop
         * @param object $variation_data
         * @param object $variation
         */
        function en_show_nested_product_fields($loop, $variation_data = array(), $variation = array())
        {

            if (!empty($variation) || isset($variation->ID)) {
                /* Variable products */
                $this->en_product_custom_nested_fields($variation->ID);
            } else {
                /* Simple products */
                $post_id = get_the_ID();
                $this->en_product_custom_nested_fields($post_id);
            }
        }

        /**
         *
         * @param $loop
         * @param $variation_data
         * @param $variation
         * @global $wpdb
         */
        function en_product_custom_nested_fields($post_id)
        {

            $description = "";
            $disable_nested = "";

            $plan_status = apply_filters('en_app_common_plan_status', []);

            // Hazardous plan status
            if (isset($plan_status['nesting'])) {
                if (!in_array(0, $plan_status['nesting']['plan_required'])) {
                    $disable_nested = 'disabled_me';
                    $description = apply_filters("unishippers_freight_plans_notification_link", [3]);
                } elseif (isset($plan_status['nesting']['status'])) {
                    $description = implode(" <br>", $plan_status['nesting']['status']);
                }
            }

            $nestedDimensionSelected = get_post_meta($post_id, '_nestedDimension', true);
            $nestedStakingPropertySelected = get_post_meta($post_id, '_nestedStakingProperty', true);

            //          Nested Material checkbox
            $field_array = array(
                'name' => '_nestedMaterials'.$post_id,
                'id' => '_nestedMaterials'.$post_id,
                'class' => "_nestedMaterials $disable_nested",
                'label' => __(
                    'Nested material', 'woocommerce'
                ),
                'value' => get_post_meta(
                    $post_id, '_nestedMaterials', true
                ),
                'description' => __(
                    "$description", 'woocommerce'
                )
            );
            woocommerce_wp_checkbox($field_array);

//          Nesting percentage input
            $field_array = array(
                'name' => '_nestedPercentage'.$post_id,
                'id' => '_nestedPercentage[' . $post_id . ']',
                'class' => "_nestedPercentage $disable_nested",
                'placeholder' => 'Range from 0 to 100',
                'label' => __(
                    'Nesting(%)', 'woocommerce'
                ),
                'desc_tip' => true,
                'description' => __("How much of the item can be nested into another. Default 0, Range from 0 to 100.", "woocommerce"),
                'value' => get_post_meta(
                    $post_id, '_nestedPercentage', true
                ),
            );
            woocommerce_wp_text_input($field_array);

//          Nested Dimension dropdown
            $field_array = array(
                'name' => '_nestedDimension'.$post_id,
                'id' => '_nestedDimension',
                'class' => "_nestedDimension $disable_nested",
                'label' => __(
                    'Nested dimension', 'woocommerce'
                ),
                'desc_tip' => true,
                'value' => $nestedDimensionSelected,
                'description' => __("This setting identifies which dimension will be used for the nesting property.", "woocommerce"),
                'options' => array(
                    'length' => 'Length',
                    'width' => 'Width',
                    'height' => 'Height',
                ),
            );
            woocommerce_wp_select($field_array);

//          Maximum nested items input field
            $field_array = array(
                'name' => '_maxNestedItems'.$post_id,
                'id' => '_maxNestedItems[' . $post_id . ']',
                'class' => "_nestedDimension $disable_nested",
                'label' => __(
                    'Maximum nested items', 'woocommerce'
                ),
                'desc_tip' => true,
                'description' => __('It represents the maximum number of items that can be nested into one another.', 'woocommerce'),
                'value' => get_post_meta(
                    $post_id, '_maxNestedItems', true
                ),
            );
            woocommerce_wp_text_input($field_array);

//          Stacking property dropdown
            $field_array = array(
                'name' => '_nestedStakingProperty'.$post_id,
                'id' => '_nestedStakingProperty',
                'class' => "_nestedStakingProperty $disable_nested",
                'label' => __(
                    'Stacking property', 'woocommerce'
                ),
                'desc_tip' => true,
                'value' => $nestedStakingPropertySelected,
                'description' => __('This setting identifies how the nested stacks should be handled if the total number of items exceeds the maximum.', 'woocommerce'),
                'options' => array(
                    'Evenly' => 'Evenly',
                    'Maximized' => 'Maximized',
                ),
            );
            woocommerce_wp_select($field_array);
        }

    }

    /* Initialize object */
    new EnUpdateProductNestedOptions('hooks');
}