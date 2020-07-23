<?php

//Exit if accessed directly.
defined('ABSPATH') or exit;

class WMMPQ_Main {
	
	//Defined properties
    protected static $instance = null; //A single instance of the class
    protected $action;
    
	/**
	 * Cart instance.
	 *
	 * @var WC_Cart
	 */
    public $cart = null;
    
	/**
	 * Cart instance.
	 *
	 * @var WC_Product
	 */
	public $product = null;
    
    /**
     * Ensuring that only 1 instance of the class is loaded
     */
	public static function instance($version){
		if(is_null(self::$instance)){
			self::$instance = new self($version);
		}
		return self::$instance;
	}
    
    /**
     * Cloning is forbidden.
     */
	public function __clone() {
		$error = new WP_Error('forbidden', 'Cloning is forbidden.');
		return $error->get_error_message();
	}
    
    /**
     * Unserializing instances of this class is forbidden.
     */
	public function __wakeup() {
		$error = new WP_Error('forbidden', 'Unserializing instances of this class is forbidden.');
		return $error->get_error_message();
	}
    
    /**
     * Our constructor
     * @param string the version of the plugin
     */
	public function __construct(string $version){
        $this->version = $version;
        $this->include();
    }

    /**
     * Method to include the files
     */
    public function include(){

        //List of action hooks
        include_once WMMPQ_DIR_PATH . 'hooks/actions/woocommerce_product_options_inventory_product_data.php';
        include_once WMMPQ_DIR_PATH . 'hooks/actions/woocommerce_variation_options_pricing.php';
        include_once WMMPQ_DIR_PATH . 'hooks/actions/woocommerce_save_product_variation.php';
        include_once WMMPQ_DIR_PATH . 'hooks/actions/woocommerce_process_product_meta_simple.php';
        include_once WMMPQ_DIR_PATH . 'hooks/actions/woocommerce_process_product_meta_variable.php';

        //List of filter hooks
        include_once WMMPQ_DIR_PATH . 'hooks/filters/woocommerce_add_to_cart_validation.php';
        include_once WMMPQ_DIR_PATH . 'hooks/filters/woocommerce_update_cart_validation.php';

    }

    /**
     * Method to set post meta
     * @param int $post_id (Required) ID of the post object
     * @param string $meta_key (Required) meta key to save
     * @param mixed $meta_value (Required) Metadata value. Must be serializable if non-scalar.
     * @param mixed $prev_value (Optional) Previous value to check before updating.
     * @return bool
     */
    public function set_meta($post_id, $meta_key, $meta_value, $prev_value = ''){
        return update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
    }

    /**
     * Method to get post meta
     * @param int $post_id (Required) ID of the post object
     * @param string $meta_key (Optional) meta key to get
     * @param bool $single (Optional) If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
     * @return mixed
     */
    public function get_meta($post_id, $meta_key, $single = true){
        return get_post_meta($post_id, $meta_key, $single);
    }

    /**
     * Method to encode an entity into a valid json string
     * @param mixed $data the data entity to encode
     */
    public function json_encode($data = null){

        //Quick check
        if(is_null($data)){
            return null;
        }

        //Begin encoding
        $data = wp_json_encode($data);
        return function_exists( 'wc_esc_json' ) ? wc_esc_json( $data ) : _wp_specialchars( $data, ENT_QUOTES, 'UTF-8', true );

    }

    /**
     * Method to check output error notice in product quantity vs min and max quantities
     * @param int $product_id
     * @param int $quantity
     * @param int $min
     * @param int $max
     * @return bool
     */
    public function validate_quantities($product_id, $quantity, $min, $max){

        //Defaults
        $product_name = '';

        //Get product
        $product = wc_get_product($product_id);

        //Check the product type
        if( $product->get_type() == 'simple' ){
            $product_name = $product->get_name();
        }
        if( $product->get_type() == 'variation' ){
            $product = wc_get_product( $product->get_parent_id() ); //Get parent product
            $product_name = $product->get_name();
        }

        //Get cart item quantity validation
        $_quantity = $this->validate_cart_item_quantity($product_id);

        //Check validation output initiated in the Product page
        if( $this->action == 'add' && $_quantity !== true && ($_quantity + $quantity) > $max ){
            wc_add_notice('Product Quantity of <b>' . $product_name . '</b> must be in the range of <b>' . $min . ' to ' . $max . '</b>. Your cart already contains <b>' . $_quantity . ' x ' . $product_name . '</b>. Please adjust accordingly.', 'error');
            return false;
        }

        //Validations initiated in the Cart page
        if( $this->action == 'update' && $_quantity !== true && $quantity > $max ){
            wc_add_notice('Product Quantity of <b>' . $product_name . '</b> must be in the range of <b>' . $min . ' to ' . $max . '</b>. Your cart already contains <b>' . $_quantity . ' x ' . $product_name . '</b>. Please adjust accordingly.', 'error');
            return false;
        }

        //Default validation
        if($quantity < $min || $quantity > $max){
            wc_add_notice('Product Quantity of <b>' . $product_name . '</b> must be in the range of <b>' . $min . ' to ' . $max . '</b>.', 'error');
            return false;
        }

        return true;

    }

    /**
     * Method for add to cart and update to cart validations
     * @param string $action either add or update string values
     * @param int $quantity
     * @param int $product_id
     * @param int $variation_id
     * @return bool
     */
    public function cart_validation($action, $quantity, $product_id, $variation_id = null){

        //Check action values
        if( !in_array( $action, ['add', 'update'] ) ){
            return true;
        }

        //Set action value
        $this->action = $action;

        //Get the product
        $product = wc_get_product( $product_id );

        //For simple products
        if($product->get_type() == 'simple'){

            //Get parent min and max settings
            $min = $this->get_meta($product_id, '_product_min_quantity');
            $max = $this->get_meta($product_id, '_product_max_quantity');

            //Begin checks
            if( ( $min > 0 && $max > 0 ) && $min < $max ){

                return $this->validate_quantities($product_id, $quantity, $min, $max);

            }

        }

        //For variable products
        if($product->get_type() == 'variable'){

            //Get parent min and max settings
            $min = $this->get_meta($product_id, '_product_min_quantity');
            $max = $this->get_meta($product_id, '_product_max_quantity');

            //Get variation id
            $variation_id = (is_null($variation_id)) ? sanitize_text_field( $_POST['variation_id'] ) : $variation_id;

            //Get variation min and max settings
            $variation_min = $this->get_meta($variation_id, '_variable_product_min_quantity');
            $variation_max = $this->get_meta($variation_id, '_variable_product_max_quantity');

            //Begin checks
            if( ( $variation_min > 0 && $variation_max > 0 ) && $variation_min < $variation_max ){

                return $this->validate_quantities($variation_id, $quantity, $variation_min, $variation_max);

            } elseif( ( $min > 0 && $max > 0 ) && $min < $max ){

                return $this->validate_quantities($variation_id, $quantity, $min, $max);

            } else {}

        }

        return true;

    }

    /**
     * Method to validate total quantity from a cart item content by a given product id
     * @param int $product_id
     * @return int|bool
     */
    public function validate_cart_item_quantity($product_id){

        //Get product
        $product = wc_get_product($product_id);

        //Get all the cart contents
        $cart_contents = WC()->cart->cart_contents;

        //Quick check
        if( !empty($cart_contents) ){

            //Begin looping through the items
            foreach($cart_contents as $cart_item){

                //Get product id based on product type
                $_product_id = ( $product->get_type() == 'variation' ) ? $cart_item['variation_id'] : $cart_item['product_id'];

                if($product_id == $_product_id){
                    return $cart_item['quantity'];
                }

            }

        }

        return true;

    }

}