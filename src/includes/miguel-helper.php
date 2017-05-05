<?php
if ( ! defined( 'ABSPATH' ) ) 
	exit;

final class MIGUEL_Helper {

	public static function get_post_value( $key, $default_value = '' ) {

		return isset( $_REQUEST[ $key ] ) ? trim( $_REQUEST[ $key ] ) : $default_value;
	}

	public static function get_post_int( $key, $default_value = 0 ) {

		return isset( $_REQUEST[ $key ] ) ? absint( $_REQUEST[ $key ] ) : $default_value;
	}
};

