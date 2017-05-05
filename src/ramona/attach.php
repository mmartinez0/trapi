<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log','poster.log');

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once($wp_load);

if( ! is_user_logged_in() )
	die();

$user_id = get_current_user_id();

if( user_can( $user_id, 'manage_options' ) == false )
	die( "I don't think so." );

final class MIGUEL_MediaHandleUpload {

	public function __construct() {

	}

	public function handle() {

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$result = MIGUEL_Result::create();

		$attachment_id = media_handle_upload( 'datafile', 0 );

		if( is_wp_error( $attachment_id ) )
			$result->failed()->set_error( 'media_handle_upload', print_r($attachment_id, true) );

		$data = (object)(array( 'thumbnail_id' => $attachment_id, 'thumbnail' => null ));

		$data->thumbnail = wp_get_attachment_metadata( $attachment_id );

		$result->return_json( $data );
	}
}

$hadler = new MIGUEL_MediaHandleUpload();
$hadler->handle();

