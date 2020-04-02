<?php

//Exit if accessed directly.
defined('ABSPATH') or exit;

//Filter hook to cart validation
add_filter('woocommerce_product_options_inventory_product_data', function(){

    global $product_object;

    //Get min and max settings
    $min = $this->get_meta($product_object->get_id(), '_product_min_quantity');
    $max = $this->get_meta($product_object->get_id(), '_product_max_quantity');

    ?>
    <div class="options_group show_if_simple show_if_variable">

    <?php
        woocommerce_wp_text_input(
            [
                'id'            => "_product_min_quantity",
                'type'          => 'number',
                'name'          => "_product_min_quantity",
                'value'         => ( wc_stock_amount( $min ) == '' || wc_stock_amount( $min ) < 0 ) ? '' : wc_stock_amount( $min ),
                'label'         => __( 'Minimum Quantity', 'woocommerce' ),
                'placeholder'   => __( 'Minimum product quantity', 'woocommerce' ),
                'desc_tip'      => true,
                'description'   => __( 'Minimum product quantity that can be added to the cart. Leave empty to disable.', 'woocommerce' ),
                'custom_attributes' => [
                    'min' => 0
                ]
            ]
        );
    ?>

    <?php
        woocommerce_wp_text_input(
            [
                'id'            => "_product_max_quantity",
                'type'          => 'number',
                'name'          => "_product_max_quantity",
                'value'         => ( wc_stock_amount( $max ) == '' || wc_stock_amount( $max ) < 0 ) ? '' : wc_stock_amount( $max ),
                'label'         => __( 'Maximum Quantity', 'woocommerce' ),
                'placeholder'   => __( 'Maximum product quantity', 'woocommerce' ),
                'desc_tip'      => true,
                'description'   => __( 'Maximum product quantity that can be added to the cart. Leave empty to disable.', 'woocommerce' ),
                'custom_attributes' => [
                    'min' => 0
                ]
            ]
        );
    ?>

    </div>
    <?php

});