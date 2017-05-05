<?php
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once($wp_load);

$sqlStmt = <<<EOQ
SELECT DISTINCT(post_type) post_type FROM wp_posts
EOQ;

$rows = $wpdb->get_results( $sqlStmt );

foreach ($rows as $row) {?>
	<a _target="new" href="list_videos.php?post_type=<?php echo $row->post_type; ?>"><?php echo $row->post_type; ?></a><br/><?php
}
