<?php

//Exit if accessed directly.
defined('ABSPATH') or exit;

/**
 * Filter hook on add to cart validation
 * @param bool $bool
 * @param int $product_id
 * @param int $quantity
 */
add_filter('woocommerce_add_to_cart_validation', function( $bool, $product_id, $quantity ){

    //Begin validation
    $cart_validation = $this->cart_validation( 'add', $quantity, $product_id );

    //Check return bool
    if( $cart_validation == false ){
        return false;
    }

    return $bool;

}, 10, 3);