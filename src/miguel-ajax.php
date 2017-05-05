<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

final class MIGUEL_Ajax {

	public static function load( $plugin ) {
		$admin = new MIGUEL_Ajax( $plugin );
		$admin->plugins_loaded();
		return $admin;
	}

	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	public function plugins_loaded(){

		add_action('init', array( $this, 'init' ) );
	}

	function init() {
		//
		// http://miguelmartinez.com/wp-admin/admin-ajax.php?action=saveMemberVideoCredit&post_id=1
		//
		add_action( 'wp_ajax_saveMemberVideoCredit', array( $this, 'save_member_video_credit' ) );
		add_action( 'wp_ajax_nopriv_saveMemberVideoCredit', array( $this, 'please_login' ) );

		add_action( 'wp_ajax_getAllVideos', array( $this, 'get_all_videos' ) );
		add_action( 'wp_ajax_nopriv_getAllVideos', array( $this, 'please_login' ) );

		add_action( 'wp_ajax_getVideo', array( $this, 'get_video' ) );
		add_action( 'wp_ajax_nopriv_getVideo', array( $this, 'please_login' ) );

		add_action( 'wp_ajax_bookmarkVideo', array( $this, 'bookmark_video' ) );
		add_action( 'wp_ajax_nopriv_getVideo', array( $this, 'please_login' ) );
	}

	function save_member_video_credit( ) {

		$result = MIGUEL_Result::create();

		$data = array(

			'current_user_id' => get_current_user_id(),
			'post_id' => MIGUEL_Helper::get_post_int( 'post_id' )
		);

		$credit_video_id = get_post_meta( $data['post_id'], 'credit_video_id', true );

		if( empty( $credit_video_id ) )
			$result->failed()->set_error('credit_video_id', sprintf('Post %s does not have a credit_video_id value', $data['post_id']) )->return_json();

		$result->return_json( (object)$data );
	}

	function please_login() {

		MIGUEL_Result::create()->failed()->set_error('session', 'Please login')->return_json();
	}

	function query_videos() {

		global $wpdb;

$sqlStmt = <<<EOQ
SELECT post.id,
       -- post.post_author,
       post.post_date,
       post.post_modified,
       post.post_title,
       post.post_status,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'media_type_id' LIMIT 1) media_type_id,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'media_status' LIMIT 1) media_status,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'player_presenter' LIMIT 1) presenter,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'previewfile' LIMIT 1) previewfile,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = '_thumbnail_id' LIMIT 1) thumbnail_id,
       (
			SELECT t.name
			  FROM wp_term_taxonomy tt INNER JOIN wp_term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
				INNER JOIN wp_terms t ON t.term_id = tt.term_id
			WHERE tt.taxonomy = 'access'
              AND tr.object_id = post.id
              LIMIT 1
	   ) access_level
  FROM wp_posts post
 WHERE post.post_type = 'video'
  AND post.post_status IN ('publish', 'draft')
ORDER BY post.id DESC
EOQ;

		return $wpdb->get_results( $sqlStmt );
	}

	//
	// curl -X GET -i http://local.wordpress.dev/trapi/v1/user/sqa-Austin@lifesize.com/lookup
	// curl -X POST -H "Content-Type: application/json" -i http://local.wordpress.dev/wp-admin/admin-ajax.php -d '{ "action": "getVideos" }'
	// curl -X POST -i http://local.wordpress.dev/wp-admin/admin-ajax.php -d '{ "action": "getVideos" }'
	// curl -X GET -i http://local.wordpress.dev/wp-admin/admin-ajax.php?action=getVideos
	//
	function get_all_videos() {

		$result = MIGUEL_Result::create();

		$result->return_json( $this->query_videos() );
	}

	function get_video() {

		global $wpdb;

		$result = MIGUEL_Result::create();

		$post_id = null;

		if( ! empty($_GET['post_id']) )
			$post_id = $_GET['post_id'];

		if( empty($post_id) )
			$result->return_json( array() );

$sqlStmt = <<<EOQ
SELECT post.id,
       post.post_author,
       post.post_date,
       post.post_content,
       post.post_title,
       post.post_status,
       post.post_name,
       post.post_modified,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'media_type_id' LIMIT 1) media_type_id,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'video_file_url' LIMIT 1) video_file_url,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'mobile_video_file_url' LIMIT 1) mobile_video_file_url,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'previewfile' LIMIT 1) previewfile,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'html_content_url' LIMIT 1) html_content_url,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'file_url' LIMIT 1) file_url,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'buy_now_url' LIMIT 1) buy_now_url,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'player_presenter' LIMIT 1) player_presenter,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'video_length' LIMIT 1) video_length,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'credit_video_id' LIMIT 1) credit_video_id,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'credit_video_price' LIMIT 1) credit_video_price,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'member_price' LIMIT 1) member_price,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'featured' LIMIT 1) featured,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'media_status' LIMIT 1) media_status,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = '_thumbnail_id' LIMIT 1) thumbnail_id,
       NULL thumbnail
  FROM wp_posts post
 WHERE post.id = $post_id
EOQ;

		$post = $wpdb->get_row( $sqlStmt );

		if( empty($post) )
			$result->return_json( array() );

		if( !empty( $post->thumbnail_id ) ) {
			$post->thumbnail = get_post_meta( $post->thumbnail_id, '_wp_attachment_metadata', true );
		}

		$result->return_json( $post );
	}

	function bookmark_video( $post_parent, $user_id ) {

		global $wpdb;

		$result = MIGUEL_Result::create();

		$post_id = $wpdb->get_var( "SELECT id FROM wp_posts WHERE post_parent = $post_parent" );

		if( $post_id > 0 )
			$result->return_json( (object)array('bookmark_id' => $post_id, 'warning' => 'Already bookmarked') );

		//
		// https://codex.wordpress.org/Function_Reference/wp_insert_post
		//
		$post = array(
			'post_author'   => $user_id,
			'post_content'	=> '',
			'post_title'	=> 'video',
		  	'post_status'   => 'publish',
		  	'post_type'		=> 'bookmark',
		  	'post_parent'	=> $post_parent
		);

		$post_id = wp_insert_post( $post, true );

		if ( is_wp_error( $post_id ) )
			$result->failed()->set_error('create_bookmark', print_r($post_id, true) )->return_json();

		$result->return_json( (object)array('bookmark_id' => $post_id) );
	}
}
