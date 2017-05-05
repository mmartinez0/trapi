<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log','import.log');

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once($wp_load);

set_time_limit ( 2400 );

if( ! is_user_logged_in() )
	die('Please login');

require( 'include.php' );?>

<html>
	<head>
	</head><?php

		$loader = new LoaderService();
		$importService = new ImportService();

		$loader->print_taxonomia();

		echo '<h1 class="videos">Videos</h1>';

		$videos = $loader->mediaLoader->videos;
		$i = 0;
		foreach ($videos as $video ) {
			$i++;?>
			<div class="video" id="video-<?php echo $video->id; ?>">
				<h2 class="primary_desc"><?php echo '[' . $video->id . '] '. $video->primary_desc; ?></h2>
				<p class="row_no">Row # <?php echo $i; ?></p>
				<p class="media_ext_desc"><?php echo $video->media_ext_desc; ?></p>
			</div><?php
			$post_id = $importService->import( $video );?>
			<p class="post_id"><label>post_id</label>: <?php echo $post_id; ?></p><?php

			//break;
		}?>
	</body>
</html>
