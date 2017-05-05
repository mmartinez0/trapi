<?php
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once($wp_load);

function print_video( $video_id ) {	

	global $wpdb;?>
	
	<div style="margin-left: 2em; border-left: 1px solid #ccc;">
		<h2>_kgflashmediaplayer-video-id <?php echo $video_id; ?></h2><?php

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT meta_id, meta_key, meta_value FROM wp_postmeta WHERE post_id = %d and meta_key = '_kgflashmediaplayer-externalurl'" , $video_id ) ); 
		foreach ( $results as $result ) {
			echo $result->meta_value;
		}?>
	</div><?php
}

function print_thumbnail( $thumbnail_id ) {	

	global $wpdb;?>
	
	<div style="margin-left: 2em; border-left: 1px solid #ccc; margin-bottom: 2em;">
		<h2>thumbnail <?php echo $thumbnail_id; ?></h2><?php

		$sqlStmt = "SELECT * FROM wp_posts WHERE ID = '$thumbnail_id'";

		foreach( $wpdb->get_results( $sqlStmt ) as $thumbnail ) {
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT meta_id, meta_key, meta_value FROM wp_postmeta WHERE post_id = %d and meta_key in ('_wp_attached_file', '_kgflashmediaplayer-video-id')" , $thumbnail->ID ) ); 
			foreach ( $results as $result ) {
				if( $result->meta_key == '_wp_attached_file' ){?>
					<div style="margin-left: 2em;">
						<h2>_wp_attached_file <?php echo $result->meta_value; ?></h2>
						<img src="/wp-content/uploads/<?php echo $result->meta_value; ?>"/>
					</div><?php
				}

				if( $result->meta_key == '_kgflashmediaplayer-video-id' ){
					print_video( $result->meta_value );
				}
			}
		}?>
	</div><?php
}


function print_post_type() {

	global $wpdb;

	$post_type = 'video';

	if( ! empty($_GET['post_type']) )
		$post_type = $_GET['post_type'];

	$sqlStmt = "SELECT * FROM wp_posts WHERE post_type = '$post_type'";

	echo "<h1>$post_type</h1>";

	foreach( $wpdb->get_results( $sqlStmt ) as $post) {

		$post = WP_Post::get_instance( $post->ID );

		echo $post->ID . ' | ' . $post->post_parent . ' | ' . $post->post_type . ' | ' . $post->post_name . ' | ' . $post->post_title;
		echo '<br/>';

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT meta_id, meta_key, meta_value FROM wp_postmeta WHERE post_id = %d" , $post->ID ) ); 
		foreach ( $results as $result ) {
			echo "<div style=\"padding-left:1em\"><strong>$result->meta_key</strong> $result->meta_value</div>";

			if( $result->meta_key === '_thumbnail_id' ){

				print_thumbnail( $result->meta_value );
			}
		}

		//echo 'credit_video_id ' . get_post_meta( $post->ID, 'credit_video_id', true );
		//echo '<br/>';

		$term_list = wp_get_post_terms( $post->ID, 'access', array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'names' ) );
		print_r( $term_list );
		echo '<hr/>';
	}
}

print_post_type();
