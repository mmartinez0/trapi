<html>
	<head>
		<link rel="stylesheet" type="text/css" href="CSSTableGenerator.css"/>
	    <link href="http://vjs.zencdn.net/5.4.4/video-js.css" rel="stylesheet">

	    <!-- If you'd like to support IE8 -->
	    <script src="http://vjs.zencdn.net/ie8/1.1.1/videojs-ie8.min.js"></script>
	</head>
	<body>

<?php
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $parse_uri[0].'wp-load.php';
require_once($wp_load);

require( 'include.php' );

class MediaImporter {

    function show_player_1( $video_id, $video, $poster ) {
      	$ext = pathinfo($video, PATHINFO_EXTENSION);
      	$trapi_video = TRAPI_CACHE_URL . $video;
      	$trapi_poster = TRAPI_CACHE_URL . $poster;
		$format = '<video id="video-%d" class="video-js" controls preload="auto" width="640" height="264" poster="%s" data-setup="{}"><source src="%s" type="video/%s"/></video>';
		return sprintf( $format, $video_id, $trapi_poster, $trapi_video, $ext );
    }

	public function __construct() {

		$this->media_fields = array(
						'id',
						'media_type_id',
						'primary_desc',
						'media_ext_desc',
						'media_status',
						'media_filename',
						'media_preview',
						'reviewed',
						'presenter',
						'thumbnailfile',
						'previewfile',
						'Format',
						'FileSize',
						'Duration_Min',
						'Duration_Sec',
						'Duration',
						'Width',
						'Height',
						'DisplayAspectRatio',
						'FrameRate',
						'BitRate',
						'Channels',
						'SamplingRate',
						'Resolution',
						'Audio_Format',
						'Premium',
						'Buy_Now_Url',
						'Word_Indexed',
						'comp_clip',
						'comp_reason',
						'Media_Mobile_FileName'
		);

		$this->load_media_file();

	}

	public function load_media_file() {

		$render = isset($_GET['render']) ? $_GET['render'] : null;
		$content = file_get_contents(dirname( __FILE__ ) . '/TRC_Media_Library_Dat/media_view.dat');
		$rows = explode("\n", $content);
		$j = count($rows);
		echo  "$j Rows...<br/>";
		for($i = 0; $i<$j; $i++) {
			$row = $rows[$i];

			if( strlen($row) == 0 )
				continue;

			$fields = explode("\t", $row);
			$field_count = count($fields);
			echo ($i+1) . "<br/>";?>

			<table class="CSSTableGenerator">
				<thead>
					<tr><?php
						foreach ($this->media_fields as $key => $value) {
							echo "<th>$value</th>";
						}?>
					</tr>
				</thead>
				<tbody>
					<tr><?php
						foreach ($this->media_fields as $key => $field_name) {

							$td = $fields[$key];

							if( $field_name == 'thumbnailfile' || $field_name == 'previewfile' ) {

								if( empty($fields[$key]))
									continue;

								if( ! empty($render) ) {

									$td = $fields[$key] . '<br/><img src="' . TRAPI_CACHE_URL . $fields[$key] . '"/>';
								}
								else {

									$td = '<a target="_blank" href="image.php?src=' . urlencode($fields[$key]) . '">' . $fields[$key] . '</a>';
								}
							}

							if( $field_name == 'media_filename' || $field_name == 'Media_Mobile_FileName' ) {

								$td = '<a target="_blank" href="player.php?video=' . urlencode($fields[$key]) . '&poster='. urlencode($fields[10]) .'">' . $fields[$key] . '</a>';

								if( ! empty($render) ) {

									$td += '<br/>' . $this->show_player_1( $fields[0], $fields[$key], $fields[10] );
								}
							}

							echo "<td>$td</td>";
						}?>
					</tr>
				</tbody>

			</table><?php
			//break;
		}

	}
}

new MediaImporter();?>
    <script src="http://vjs.zencdn.net/5.4.4/video.js"></script>
	</body>
</html>
