<?php
/*
Plugin Name: Window Product
Description: Window Product - plugin for test task.
Author: Denis Nichik
Version: 1.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directl

define("WINPPROD_PLUGIN_ID", "win_prod");
define("WINPPROD_PLUGIN_NAME", "Window Product");

$win_prod_dir_path = dirname( __FILE__ );

require_once $win_prod_dir_path . '/includes/functions.php';

add_action('wp_enqueue_scripts', 'win_prod_site_scripts');

function win_prod_site_scripts( $hook ) {

	wp_register_style( 'win_prod_style', plugins_url( 'css/win-prod-style.css', __FILE__) ); 
	wp_enqueue_style( 'win_prod_style' );
     
    wp_register_script('win_prod_scripts', plugins_url( 'js/win-prod-scripts.js', __FILE__), array('jquery'));
    wp_enqueue_script( 'win_prod_scripts');
    wp_localize_script( 'win_prod_scripts', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}