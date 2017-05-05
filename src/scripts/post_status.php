<?php
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once($wp_load);

$rows = $wpdb->get_results( 'SELECT DISTINCT(post_status) post_status FROM wp_posts' );?>

<ul><?php
	foreach ($rows as $row) {
		echo "<li>$row->post_status</li>";
	}?>
</ul>
