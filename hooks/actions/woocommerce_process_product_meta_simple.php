<?php

//Exit if accessed directly.
defined('ABSPATH') or exit;

/**
 * Action hook to save simple product custom fields
 * @param int $product_id
 * @return void
 */
add_action('woocommerce_process_product_meta_simple', function($product_id){

    //Sanitize the data
    $min = wc_stock_amount( wp_unslash( $_POST['_product_min_quantity'] ) );
    $max = wc_stock_amount( wp_unslash( $_POST['_product_max_quantity'] ) );

    //Defaults
    $min = ( $min == '' || $min < 0 ) ? '' : $min;
    $max = ( $max == '' || $max < 0 ) ? '' : $max;

    //Begin saving
    $this->set_meta( $product_id, '_product_min_quantity', $min );
    $this->set_meta( $product_id, '_product_max_quantity', $max );

});