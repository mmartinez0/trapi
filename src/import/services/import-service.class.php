<?php

class ImportService {

	public function __construct() {

		$this->user_id = get_current_user_id();

		$upload_dir_info = wp_upload_dir();

		define( 'TRAPI_UPLOAD_DIR',	$upload_dir_info['basedir'] . '/trapi/import/previewfile/' );

		if( ! is_dir(TRAPI_UPLOAD_DIR) )
			mkdir(TRAPI_UPLOAD_DIR, 0777, true);

		$this->members_only_term = get_term_by('slug', 'members-only', 'access');

		if( empty($this->members_only_term) )
			die('Members Only term not found in taxonomy Access Levels');

		//die( print_r($this->members_only_term, true));
	}

	public function import( $row ) {

		$post_id = $this->add_post_video( $row );

		//$this->add_to_members_only( $post_id );

		//$this->fix_stuff( $post_id, $row );

		$thumbnail_id = $this->set_fatured_image( $post_id, $this->download_images( $row ) );

		if( ! empty($thumbnail_id) )
			$this->save_metadata( $post_id, '_thumbnail_id', $thumbnail_id );

		return $post_id;
	}

	private function add_post_video( $row ) {

		global $wpdb;

		$post_id = $wpdb->get_var( sprintf("SELECT post_id FROM %spostmeta WHERE meta_key = 'video_id' AND meta_value = %s", $wpdb->prefix, $row->id) );

		if( ! empty( $post_id) ) {

			echo '<div style="color: purple;">Post already exist for this video...</div>';
			return $this->save_video_taxonomy( $row, $post_id );
		}
		
		//
		// https://codex.wordpress.org/Function_Reference/wp_insert_post
		//
		$post = array(
		  	'post_type'		=> 'video',
		  	'post_status'   => 'publish',
			'post_title'    => $row->primary_desc,
		  	'post_content'  => $row->media_ext_desc,
		  	'post_author'   => $this->user_id
		);

		$post_id = wp_insert_post( $post, true );

		if ( is_wp_error( $post_id ) ){

			die( print_r($post_id, true) );
		}

		if( empty($post_id) )
			die('Failed to insert post');

		$this->save_metadata( $post_id, 'video_id', $row->id );
		$this->save_metadata( $post_id, 'video_file_url', $row->media_filename );
		$this->save_metadata( $post_id, 'mobile_video_file_url', $row->Media_Mobile_FileName );
		$this->save_metadata( $post_id, 'buy_now_url', $row->Buy_Now_Url );
		$this->save_metadata( $post_id, 'player_presenter', $row->presenter );
		$this->save_metadata( $post_id, 'video_length', $row->duration );

		//$this->save_metadata( $post_id, 'credit_video_id', '' );
		//$this->save_metadata( $post_id, 'credit_video_price', 0 );
		//$this->save_metadata( $post_id, 'featured', )
	
		echo '<div style="color: green;">New video post ' . $post_id . '...</div>';

		return $this->save_video_taxonomy( $row, $post_id );
	}

	private function save_metadata( $post_id, $key, $new_value ) {

		$current_value = get_post_meta( $post_id, $key, true );

		if( ! empty($current_value) ) {

			if( $current_value !== $new_value )
				update_post_meta( $post_id, $key, $new_value );

			return;
		}

		add_post_meta( $post_id, $key, $new_value );
	}

	private function download_images( $row ) {

		return (object)array(
			//'thumbnailfile' => $this->download_image($row->thumbnailfile),
			'previewfile' => $this->download_image($row->previewfile)
		);
	}

	private function download_image( $filename ) {

		$result = (object)array(
			'filespec' => TRAPI_UPLOAD_DIR . $filename,
			'url' => empty($filename) ? null : TRAPI_CACHE_URL . $filename
		);

		if( empty($result->url) ){

			echo '<div><span style="background-color: black; color: yellow;">Invalid previewfile</span></div>';
			return $result;
		}

		//echo "<div>$filespec</div>";

		if( is_file($result->filespec) ){

			echo "<div>Image already exist: $result->filespec</div>";
			return $result;
		}


		echo "<div>downloading $result->url...</div>";

		file_put_contents( $result->filespec, file_get_contents($result->url) );
		
		if( ! is_file($result->filespec) )
			die("failed to download $result->url into $result->filespec");

		return $result;
	}

	private function set_fatured_image( $post_id, $images ) {

		global $wpdb;

		if( empty($images->previewfile->url) )
			return 0;

		$filespec = $images->previewfile->filespec;
		$url = $images->previewfile->url;

		$post_title = preg_replace( '/\.[^.]+$/', '', basename($filespec) );

		$attachment_id = $wpdb->get_var( sprintf("SELECT id FROM %sposts WHERE post_type = 'attachment' AND post_title = '%s'", $wpdb->prefix, $post_title) );

		if( ! empty( $attachment_id ) ) {

			echo '<div style="color: purple;">Media attachment already exist...</div>';

			return $attachment_id;
		}

		$wp_filetype = wp_check_filetype(basename($filespec), null ); // Array ( [ext] => jpg [type] => image/jpeg ) 

	    $attachment = array(
		   'guid' => $url,
		   'post_mime_type' => $wp_filetype['type'],
		   'post_title' => $post_title,
		   'post_content' => '',
		   'post_status' => 'inherit',
		   'post_author' => $this->user_id
	    );

		require_once(ABSPATH . 'wp-admin/includes/image.php');

	    //die( print_r($attachment, true));

        //
        // wp_insert_attachment() inserts an attachment into the media library. 
        // wp_insert_attachment() should be used in conjunction with wp_update_attachment_metadata() and wp_generate_attachment_metadata(). 
        // Returns the ID of the entry created in the wp_posts table. 
        //
	    $attachment_id = wp_insert_attachment( $attachment, $filespec, $post_id );

        //
        // wp_generate_attachment_metadata() generates metadata for an image attachment. 
        // It also creates a thumbnail and other intermediate sizes of the image attachment 
        // based on the sizes defined on the Settings_Media_Screen. 
        //
        $attach_data = wp_generate_attachment_metadata( $attachment_id, $filespec );

        wp_update_attachment_metadata( $attachment_id, $attach_data );

        return $attachment_id;
	}

	public function import_taxonomia( $loader ) {

		//
		// https://generatewp.com/taxonomy/
		//
		$attribute_groups = $loader->attributeGroupLoader->attribute_groups;

		foreach ($attribute_groups as $attribute_group) { 
			$parent_term = term_exists( 'fruits', 'product' ); // array is returned if taxonomy is given
			$parent_term_id = $parent_term['term_id']; // get numeric term id
		}
	}

	private function save_video_taxonomy( $video, $post_id ) {

		if( ! empty($video->media_attributes) ) {
			foreach( $video->media_attributes as $media_attribute ) {
				$attribute = $media_attribute->attribute;

				$attribute_group = $attribute->attribute_group;
				$taxonomy = $attribute_group->slug;

				//echo "<hr/>wp_set_object_terms( $post_id, (int)$attribute->term_id, $taxonomy, true )<hr/>";

				$result = wp_set_object_terms( $post_id, (int)$attribute->term_id, $taxonomy, true );

				//print_r($result);
			}
		}
		return $post_id;	
	}

	private function add_to_members_only( $post_id ) {

		wp_set_object_terms( $post_id, (int)$this->members_only_term->term_id, 'access', true );
	}

	private function fix_stuff( $post_id, $row ) {

		//if( strlen($row->Buy_Now_Url) == 1 )
		//	$this->save_metadata( $post_id, 'buy_now_url', '' );

		//if( strlen($row->Media_Mobile_FileName) == 1 )
		//	$this->save_metadata( $post_id, 'mobile_video_file_url', '' );

		//$this->save_metadata( $post_id, 'media_type_id', $row->media_type_id );

		// 10 HTML
		// 6 PDF

		//if( $row->media_type_id == '6' || $row->media_type_id == '10' )
		//	die('10');

		//$this->save_metadata( $post_id, 'media_status', $row->media_status );
		
		//$html_content_url = '';
		//if( strlen($row->file_name_URL) > 2 )
		//	$html_content_url = $row->file_name_URL;
		//$this->save_metadata( $post_id, 'html_content_url', $html_content_url);

		//$buy_now_url = get_post_meta( $post_id, 'buy_now_url', true );

		//if( empty($buy_now_url) || strlen($buy_now_url) < 2 )
		//	$this->save_metadata( $post_id, 'buy_now_url', '');

	}
} 
