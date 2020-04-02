<?php
/*
Plugin Name: WooCommerce Min & Max Product Quantities
Plugin URI:  https://virson.wordpress.com/
Description: An extension plugin to WooCommerce that enables minimum and maximum product quantity limitations when adding to cart.
Version:     0.0.1a
Author:      Virson Ebillo
Author URI:  https://virson.wordpress.com/
License:     GNUv3
 
WooCommerce Tabulated Bulk Shop is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.
 
WooCommerce Tabulated Bulk Shop is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with WooCommerce Tabulated Bulk Shop. If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.
*/

//Exit if accessed directly.
defined('ABSPATH') or exit;

//Checks plugin dependency
if(function_exists('is_plugin_active')){

    //Deactivate if plugin dependency is not activated
	if( !is_plugin_active('woocommerce/woocommerce.php') ){
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die('Error on activating <b>WooCommerce Min & Max Product Quantities</b> plugin:<br />Please enable/activate <a href="https://wordpress.org/plugins/woocommerce/" target="_blank"><b>WooCommerce</b></a> plugin before using this plugin. <a href="' . admin_url('plugins.php') . '">Go back.</a>');
    }

}

//Register the dependency plugin for deactivation hook
register_deactivation_hook('woocommerce/woocommerce.php', function(){

    //Check if our plugin is activtated
    if(is_plugin_active( plugin_basename(__FILE__) )){

        //Deactivate the plugins
        deactivate_plugins( plugin_basename(__FILE__) );
        deactivate_plugins( 'woocommerce/woocommerce.php' );
        wp_die('The <b>WooCommerce Min & Max Product Quantities</b> plugin was deactivated because the dependency plugin:<br><a href="https://wordpress.org/plugins/woocommerce/" target="_blank"><b>WooCommerce</b></a> was deactivated. <a href="' . admin_url('plugins.php') . '">Go back.</a>');

    }

});

//Define our constants
define('WMMPQ_DIR_URL', preg_replace('/\s+/', '', plugin_dir_url(__FILE__)));
define('WMMPQ_DIR_PATH', preg_replace('/\s+/', '', plugin_dir_path(__FILE__)));

//Include the main class.
if( !class_exists( 'WMMPQ_Main', false ) ){
	include_once WMMPQ_DIR_PATH . 'classes/WMMPQ_Main.php';
}

/**
 * Class would be available as a function
 * Note: Attach the version number as well
 */
function WMMPQ(){
	return WMMPQ_Main::instance('0.0.1a');
}
WMMPQ();