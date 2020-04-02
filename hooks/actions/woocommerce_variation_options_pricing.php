<?php

//Exit if accessed directly.
defined('ABSPATH') or exit;

/**
 * Action hook to add custom pricing fields for each variation in a loop
 * @param int $loop Position in the loop.
 * @param array $variation_data Variation data.
 * @param WP_Post $variation Post data.
 * @return void
 */
add_action('woocommerce_variation_options_pricing', function($loop, $variation_data, $variation){

    //Get min and max settings
    $min = $this->get_meta($variation->ID, '_variable_product_min_quantity');
    $max = $this->get_meta($variation->ID, '_variable_product_max_quantity');

    woocommerce_wp_text_input(
        [
            'id'            => "_variable_product_min_quantity_{$loop}",
            'type'          => 'number',
            'name'          => "_variable_product_min_quantity[{$loop}]",
            'wrapper_class' => "form-row form-row-first",
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
    
    woocommerce_wp_text_input(
        [
            'id'            => "_variable_product_max_quantity_{$loop}",
            'type'          => 'number',
            'name'          => "_variable_product_max_quantity[{$loop}]",
            'wrapper_class' => "form-row form-row-last",
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

}, 10, 3);