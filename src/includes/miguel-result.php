<?php
if ( ! defined( 'ABSPATH' ) ) 
	exit;

final class MIGUEL_Result {

	static public function create() {
		return new MIGUEL_Result();
	}

	public function __construct() {

		$this->data_field_name = 'data';
		$this->success = true;
		$this->error = (object)array( 'code' => '', 'message' => '' );
		$this->data = null;
	}

	public function failed() {

		$this->success = false;
		return $this;
	}

	public function setError( $code, $message ) {

		return $this->set_error( $code, $message );
	}

	public function set_error( $code, $message ) {

		$this->error->code = $code;
		$this->error->message = $message;
		return $this;
	}

	public function with_data_field_name( $data_field_name ){

		$this->data_field_name = $data_field_name;
		return $this;
	}

	public function return_json( $data = null ) {

		if( $this->data == null && $data != null )
			$this->data = $data;
		
		$response = array( 'success' => $this->success );

		if( $this->success )
			$response[$this->data_field_name] = $this->data;
		else
			$response['errors'] = array( $this->error );

		header('Content-Type: application/json');

		die( json_encode( $response ) );
	}
}