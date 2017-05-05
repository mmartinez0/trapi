<?php 
if ( ! defined( 'ABSPATH' ) ) 
	exit;

class TRCWCFSvcLog {

	static public function create( $service, $payload ) {
	
		return new TRCWCFSvcLog( $service, $payload );
	}

	public function __construct( $service, $payload ) {

		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'trapi_request', 
			array(
				'service' => $service,
				'payload' => ( gettype($payload) === 'array' ? print_r( $payload, true ) : $payload )
			),
			array( '%s', '%s' ) 
		);

		$this->id = $wpdb->insert_id;
	}

	public function response( $success, $code, $payload ) {

		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'trapi_response', 
			array(
				'id' => $this->id,
				'success' => $success,
				'code' => $code,
				'payload' => ( gettype($payload) === 'object' ? print_r( $payload, true ) : $payload )
			),
			array( '%d', '%s', '%s', '%s' ) 
		);
	}

	public function request() {

		return $this;
	}

	public function failed( $code, $payload ) {

		$this->response( 'false', $code, $payload );
	}

	public function success( $code, $payload ) {

		$this->response( 'true', $code, $payload );
	}
}
