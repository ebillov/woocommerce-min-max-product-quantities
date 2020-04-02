<?php

//Exit if accessed directly.
defined('ABSPATH') or exit;

/**
 * Action hook to save the custom field added on each variation loop
 * @param int $variation_id the variation id
 * @param int $i Position in the loop.
 * @return void
 */
add_action('woocommerce_save_product_variation', function($variation_id, $i){

    //Sanitize the data
    $min = wc_stock_amount( wp_unslash( $_POST['_variable_product_min_quantity'][ $i ] ) );
    $max = wc_stock_amount( wp_unslash( $_POST['_variable_product_max_quantity'][ $i ] ) );

    //Defaults
    $min = ( $min == '' || $min < 0 ) ? '' : $min;
    $max = ( $max == '' || $max < 0 ) ? '' : $max;

    //Begin saving
    $this->set_meta( $variation_id, '_variable_product_min_quantity', $min );
    $this->set_meta( $variation_id, '_variable_product_max_quantity', $max );
    
}, 10, 2);