<?php
if ( ! defined( 'ABSPATH' ) ) 
	exit;

require_once( dirname(__FILE__) . '/includes/miguel-logger.php' );
require_once( dirname(__FILE__) . '/includes/miguel-result.php' );
require_once( dirname(__FILE__) . '/includes/miguel-helper.php' );

final class MIGUEL_Plugin {

	/**
	 * MIGUEL_Plugin::load() is called on 'plugins_loaded'
	 */
	public static function load() {
	
		global $miguel;
		$miguel = new MIGUEL_Plugin();
	}

	/**
	 * Constructor is called on 'plugins_loaded'
	 */
	public function __construct() {
		$this->logger = new MIGUEL_Logger( $this );
		$this->plugin =	$this->bootstrap();
	}

	private function bootstrap(){

		if( defined('DOING_AJAX') && DOING_AJAX ){
			//die('DOING_AJAX');
			require_once( MIGUEL_PLUGIN_DIR_SRC . 'miguel-ajax.php' );
			return MIGUEL_Ajax::load( $this );
		}

		if( is_admin()){
			//die('is_admin');
			require_once( MIGUEL_PLUGIN_DIR_SRC . 'admin/miguel-admin.php' );
			return MIGUEL_Admin::load( $this );
		}

		//die('public');
		require_once( MIGUEL_PLUGIN_DIR_SRC . 'miguel-public.php' );
		return MIGUEL_Public::load( $this );
	}

	public function register($key, $item){
		return $this->ioc->register($key, $item);
	}

	public function resolve( $key ){
		return $this->ioc->resolve($key);
	}
}

function MIGUEL_load(){
}

