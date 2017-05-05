<?php
/*
 * Plugin Name: Trapi
 * Plugin URI: http://miguelmartinez.com/wordpress/plugin/trapi
 * Description: This is plugin to integrate this site membership plugin with ...
 * Version: 0.0.1
 * Author: Miguel Martinez
 * Author URI: http://miguelmartinez.com/
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

define( 'MIGUEL_PLUGIN_DIRNAME', basename(dirname( __FILE__ )) );

define( 'MIGUEL_PLUGIN_DIR', dirname( __FILE__ ) . '/' );
define( 'MIGUEL_PLUGIN_DIR_SRC', MIGUEL_PLUGIN_DIR . 'src/' );
define( 'MIGUEL_PLUGIN_URL_SRC', plugins_url( MIGUEL_PLUGIN_DIRNAME . '/src/') );

define( 'MIGUEL_VERSION', '0.0.1' );
define( 'MIGUEL_DOMAIN', MIGUEL_PLUGIN_DIRNAME );

function MIGUEL_plugins_loaded(){
	require( MIGUEL_PLUGIN_DIR_SRC . 'miguel-plugin.php' );
	MIGUEL_Plugin::load();
}
add_action( 'plugins_loaded', 'MIGUEL_plugins_loaded' );

function MIGUEL_activation_hook(){
	require( MIGUEL_PLUGIN_DIR_SRC . 'miguel-activation.php' );
	MIGUEL_activation::activate();
}
register_activation_hook( __FILE__, 'MIGUEL_activation_hook' );

function MIGUEL_deactivation_hook(){
	require( MIGUEL_PLUGIN_DIR_SRC . 'miguel-deactivation.php' );
	MIGUEL_deactivation::deactivate();
}
register_deactivation_hook( __FILE__, 'MIGUEL_deactivation_hook' );
