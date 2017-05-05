<?php
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';

require_once($wp_load);


$result = MIGUEL_Result::create();

$sqlStmt = <<<EOQ
SELECT post.id, 
       post.post_author, 
       post.post_date, 
       post.post_title, 
       post.post_status,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'media_type_id' LIMIT 1) media_type_id,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'media_status' LIMIT 1) media_status,
       (SELECT meta_value FROM wp_postmeta WHERE post_id = post.id AND meta_key = 'player_presenter' LIMIT 1) presenter,
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

$posts = $wpdb->get_results( $sqlStmt );

$result->return_json( $posts );
