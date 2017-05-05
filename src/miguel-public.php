<?php
if ( ! defined( 'ABSPATH' ) ) 
	exit;

final class MIGUEL_Public {

	public static function load( $plugin ) {

		$admin = new MIGUEL_Public( $plugin );
		$admin->plugins_loaded();
		return $admin;
	}

	public function __construct( $plugin ) {

		$this->plugin = $plugin; 
	}

	public function plugins_loaded() {

		add_action( 'wp_authenticate', array( $this, 'wp_authenticate' ) ); // wp-includes:user.php:wp_signon():56:

		add_shortcode( 'trapi_subscriptions', array( $this, 'trapi_subscriptions_shortcode' ), 20 );

		add_filter( 'trapi_authorize_user_to_video', array( $this, 'authorize_user_to_video' ) );
		add_filter( 'trapi_video_bookmark', array( $this, 'video_bookmark_filter' ) );
		add_filter( 'trapi_video_bookmarks', array( $this, 'video_bookmarks_filter' ) );
	}

	public function wp_authenticate( $user_login ) {

		$login_name = sanitize_user( MIGUEL_Helper::get_post_value( 'log' ) );
		$password = MIGUEL_Helper::get_post_value( 'pwd' );

		if( empty( $login_name ) || empty( $password ) )
			return;

		require_once( MIGUEL_PLUGIN_DIR_SRC . 'includes/authentication-service.php' );

		AuthenticationService::create( $login_name, $password );
	}

	public function trapi_subscriptions_shortcode( $atts = array(), $content = '' ) {

		if( ! is_user_logged_in() )
			return '<p>Please login or register</p>';

		require_once( MIGUEL_PLUGIN_DIR_SRC . 'includes/subscriptions-shortcode.php' );

		SubscriptionsShortcode::shortcode( $atts, $content );
	}

	public function authorize_user_to_video( $post_id ){

		$post = WP_Post::get_instance( $post_id );

		if( $post->post_type !== 'video' )
			return (object)array( 'authorized' => false, 'access_level' => 'Need Video' );

		$access_level = MIGUEL_Public::get_video_access_level( $post_id );

		if( empty( $access_level ) )
			return (object)array( 'authorized' => false, 'access_level' => 'Need Level' );

		if( $access_level === 'Free' )
			return (object)array( 'authorized' => true, 'access_level' => $access_level );

		//
		// From this point on, the user must be logged in...
		//
		if( ! is_user_logged_in() )
			return (object)array( 'authorized' => false, 'access_level' => $access_level );

		if( user_can( get_current_user_id(), 'manage_options' ) ) // Administrator
			return (object)array( 'authorized' => true, 'access_level' => 'Free' );

		$member_id = get_user_meta( get_current_user_id(), 'MemberID', true );

		if( empty( $member_id ) )
			return (object)array( 'authorized' => false, 'access_level' => 'Need MemberID' ); // we need a member_id to continue...
		
		global $wpdb;
		
		$table = $wpdb->prefix . 'trapi_member_subscription';

		if( $access_level === 'Members Only' ) {

			$sqlStmt = "SELECT COUNT(*) FROM $table WHERE subscription_type = 'Site' AND member_id = %d";

			$authorized = $wpdb->get_var( $wpdb->prepare( $sqlStmt, $member_id ) ) > 0;

			return (object)array( 'authorized' => $authorized, 'access_level' => $access_level, 'member_id' => $member_id );
		}

		if( $access_level !== 'Pay Per Video' )
			return (object)array( 'authorized' => false, 'access_level' => 'Invalid Level', 'member_id' => $member_id, 'invalid_level' => $access_level );

		//
		// Pay Per Video
		//
		$credit_video_id = get_post_meta( $post_id, 'credit_video_id', true );

		if( empty( $credit_video_id ) )
			return (object)array( 'authorized' => false, 'access_level' => $access_level, 'member_id' => $member_id, 'credit_video_id' => null ); // We need the TRAPI video ID...

		$sqlstmt = "SELECT COUNT(*) FROM $table WHERE subscription_type = 'ALaCarteItem' AND member_id = %d AND item_code = %s";

		$authorized = $wpdb->get_var( $wpdb->prepare( $sqlStmt, $member_id, $credit_video_id ) ) > 0;

		return (object)array( 'authorized' => $authorized, 'access_level' => $access_level, 'member_id' => $member_id, 'credit_video_id' => $credit_video_id );
	}

	static public function get_video_access_level( $post_id ) {

		$term_list = wp_get_post_terms( $post_id, 'access', array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'names' ) );

		if( is_wp_error( $term_list ) || count( $term_list ) === 0 )
			return null;

		return $term_list[0];
	}

	public function video_bookmark_filter( $post_id ) {

		global $wpdb;

		$bookmark = MIGUEL_Helper::get_post_value( 'bookmark' );
		$user_id = get_current_user_id();

		if( empty( $user_id ) )
			die('Please login');

		$bookmark_id = $wpdb->get_var( "SELECT id FROM wp_posts WHERE post_author = $user_id AND post_type = 'bookmark' AND post_title = 'video' AND post_parent = $post_id" );

		if( empty($bookmark) || empty($post_id) || ($bookmark !== 'add' && $bookmark !== 'remove') )
			return $bookmark_id;
		
		if( $bookmark === 'add' ){

			if( empty($bookmark_id) ) {

				$post = array(
					'post_author'   => $user_id,
					'post_content'	=> '',
					'post_title'	=> 'video',
				  	'post_status'   => 'publish',
				  	'post_type'		=> 'bookmark',
				  	'post_parent'	=> $post_id
				);

				$bookmark_id = wp_insert_post( $post, true );

				if ( is_wp_error( $bookmark_id ) )
					$bookmark_id = 0; // TODO: log the error somewhere
			}
		}
		else {

			wp_delete_post( $bookmark_id, true );

			$bookmark_id = 0;
		}

		return $bookmark_id;
	}

	public function video_bookmarks_filter( $user_id ) {

		global $wpdb;

		$result = array();

		if( empty( $user_id ) )
			return $result;

$sqlStmt = <<<EOQ
SELECT post.id, 
	   post.post_title,
	   (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'player_presenter' LIMIT 1) presenter,
	   (SELECT post_modified FROM wp_posts WHERE id = post.id LIMIT 1) post_modified,
	   post.guid url 
  FROM wp_posts post 
 WHERE id IN (SELECT post_parent FROM wp_posts WHERE post_author = $user_id AND post_type = 'bookmark' AND post_title = 'video')
ORDER BY id
EOQ;
		
		return $wpdb->get_results( $sqlStmt );
	}
}
