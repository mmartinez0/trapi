<?php
if ( ! defined( 'ABSPATH' ) ) 
	exit;

final class MIGUEL_activation {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    0.0.1
	 */
	public static function activate() {

		global $wpdb;
		$tables = array( 'trapi_request', 'trapi_response', 'trapi_member_subscription' );

		foreach ($tables as $table) {
			$filespec = MIGUEL_PLUGIN_DIR_SRC . 'scripts/sql/' . $table . '.sql';
			$sql = str_replace( 'trapi_', $wpdb->prefix . 'trapi_', file_get_contents( $filespec ) );
			$wpdb->query( $sql );
		} 
	}
}
