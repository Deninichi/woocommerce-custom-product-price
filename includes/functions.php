<?php

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directl


	//Single product page view settings
	if ( ! function_exists( 'win_prod_woo_product_edit' ) ){
		function win_prod_woo_product_edit(){
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			add_action( 'woocommerce_single_product_summary', 'win_prod_woo_product_pricing_btn', 35 );
			add_action( 'woocommerce_single_product_summary', 'win_prod_woo_product_pricing_popup', 40 );

			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
		}
	}
	add_action( 'init', 'win_prod_woo_product_edit' );

	
	//Pricing button HTML code
	if ( ! function_exists( 'win_prod_woo_product_pricing_btn' ) ){
		function win_prod_woo_product_pricing_btn(){
			$html = '<button class="pricing-btn">Pricing</button>';

			echo $html;
		}
	}


	//Show popup with product price
	if ( ! function_exists( 'win_prod_woo_product_pricing_popup' ) ){
		function win_prod_woo_product_pricing_popup( ){
			global $product, $win_prod_dir_path;

			ob_start();
			
			include $win_prod_dir_path . '/templates/popup-view.php';

			echo ob_get_clean();
		}
	}


	//Calculate new price
	add_action( 'wp_ajax_calculate', 'win_prod_ajax_calculate' );
	add_action( 'wp_ajax_nopriv_calculate', 'win_prod_ajax_calculate' );
	
	if ( ! function_exists( 'win_prod_ajax_calculate' ) ){
		function win_prod_ajax_calculate(){

			$url = 'http://138.68.52.182:84/pricing_tiers.json';
			$json = file_get_contents($url);
			$pricing_tiers = json_decode($json, TRUE);

			$area_size = $_POST['width'] * $_POST['height'];
  
			foreach ($pricing_tiers["pricing_tiers"] as $key => $tier) {
				if ( ( $area_size >= $tier['min_size'] || $tier['min_size'] == null ) && ( $area_size < $tier['max_size'] || $tier['max_size'] == 0 )  )
					$price = $tier['price'];
			}

			$new_price = $area_size*$price;
			
			echo $new_price;

			exit();
		}
	}


	//Add to cart process
	add_action( 'wp_ajax_add_to_cart', 'win_prod_ajax_add_to_cart' );
	add_action( 'wp_ajax_nopriv_add_to_cart', 'win_prod_ajax_add_to_cart' );
	
	if ( ! function_exists( 'win_prod_ajax_add_to_cart' ) ){
		function win_prod_ajax_add_to_cart(){
			global $product, $woocommerce, $new_price;

			$product_id = $_POST['product_id'];
			$new_price = $_POST['new_price'];
			$width = $_POST['width'];
			$height = $_POST['height'];
			$found = false;

			if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->id == $product_id )
						$found = true;
				}
				// if product not found, add it
				if ( ! $found ){
					WC()->cart->add_to_cart( $product_id );
					echo '<p class="success">Product was added to cart. Click <a href="' . $woocommerce->cart->get_cart_url() . '">here</a> to see it.</p>';
				} else {
					echo '<p class="alert">Product already added to cart. Click <a href="' . $woocommerce->cart->get_cart_url() . '">here</a> to see it.</p>';
				}
			} else {
				// if no products in cart, add it
				WC()->cart->add_to_cart( $product_id );
				echo '<p class="success">Product was added to cart. Click <a href="' . $woocommerce->cart->get_cart_url() . '">here</a> to see it.</p>';
			}

			exit();
		}
	}


	//Send custom data in cart item
	add_filter('woocommerce_add_cart_item_data','win_prod_add_item_data',1,10);

	if ( ! function_exists( 'win_prod_add_item_data' ) ){
		function win_prod_add_item_data($cart_item_data, $product_id) {

		    global $woocommerce;
		    $new_value = array();
		    $new_value['_custom_options'] = array( 
		    			'new_price' => $_POST['new_price'],
		    			'width' => $_POST['width'],
		    			'height' => $_POST['height'],
		    );

		    if(empty($cart_item_data)) {
		        return $new_value;
		    } else {
		        return array_merge($cart_item_data, $new_value);
		    }
		}
	}


	add_filter('woocommerce_get_cart_item_from_session', 'win_prod_get_cart_items_from_session', 1, 3 );
	
	if ( ! function_exists( 'win_prod_get_cart_items_from_session' ) ){
		function win_prod_get_cart_items_from_session($item,$values,$key) {

		    if (array_key_exists( '_custom_options', $values ) ) {
		        $item['_custom_options'] = $values['_custom_options'];
		    }

		    return $item;
		}
	}


	//Show width and height for product in cart
	add_filter('woocommerce_cart_item_name','win_prod_add_usr_custom_session',1,3);
	
	if ( ! function_exists( 'win_prod_add_usr_custom_session' ) ){
		function win_prod_add_usr_custom_session($product_name, $values, $cart_item_key ) {

		    $return_string = $product_name . "<br />
		    <strong>Width: </strong>" . $values['_custom_options']['width'] . 
		    "<br /><strong>Height: </strong>" . $values['_custom_options']['height'];

		    return $return_string;

		}
	}


	//Show width and height in order
	add_action('woocommerce_add_order_item_meta','win_prod_add_values_to_order_item_meta',1,2);
	
	if ( ! function_exists( 'win_prod_add_values_to_order_item_meta' ) ){	
		function win_prod_add_values_to_order_item_meta($item_id, $values) {
		    global $woocommerce,$wpdb;

		    wc_add_order_item_meta($item_id,'width',$values['_custom_options']['width']);
		    wc_add_order_item_meta($item_id,'height',$values['_custom_options']['height']);

		}
	}


	//Add new calculated price to product in cart
	add_action( 'woocommerce_before_calculate_totals', 'win_prod_update_custom_price', 1, 1 );

	if ( ! function_exists( 'win_prod_update_custom_price' ) ){	
		function win_prod_update_custom_price( $cart_object ) {
		    foreach ( $cart_object->cart_contents as $cart_item_key => $value ) {       
		        $value['data']->set_price($value['_custom_options']['new_price']);
		    }
		}
	}