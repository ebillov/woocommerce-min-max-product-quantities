<?php

//Exit if accessed directly.
defined('ABSPATH') or exit;

/**
 * Filter hook on update cart validation
 * @param bool $bool
 * @param string $cart_item_key
 * @param array $values
 * @param int $quantity
 */
add_filter('woocommerce_update_cart_validation', function( $bool, $cart_item_key, $values, $quantity ){

    //Begin validation
    $cart_validation = $this->cart_validation( 'update', $quantity, $values['product_id'], $values['variation_id'] );

    //Check return bool
    if( $cart_validation == false ){
        return false;
    }

    return $bool;

}, 10, 4);