<?php

class MediaLoader {

	public function __construct() {

		$this->field_names = array(
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
			'Media_Mobile_FileName',
			'file_name_URL'
		);

		$this->videos = $this->load_media_file();

		echo count($this->videos) . ' videos...<br/>';
	}

	public function load_media_file() {

		$render = isset($_GET['render']) ? $_GET['render'] : null;
		$content = file_get_contents(dirname( __FILE__ ) . '/../TRC_Media_Library_Dat/media_view.dat');
		$rows = explode("\n", $content);
		$j = count($rows);
		$result = array();

		for($i = 0; $i<$j; $i++) {
			$row = $rows[$i];
			
			if( strlen($row) == 0 )
				continue;

			$fields = explode("\t", $row);

			$buffer = array();

			foreach ($this->field_names as $key => $field_name) {
				$buffer[$field_name] = wp_check_invalid_utf8( $fields[$key], true );
			}

			$buffer['media_attributes'] = null;

			$video = (object)$buffer;

			$video->duration = (empty($video->Duration_Min) ? '' : $video->Duration_Min) . ':' . (empty($video->Duration_Sec) ? '' : $video->Duration_Sec);

			$result[$video->id] = $video;
		}

		return $result;
	}

	public function find_video( $id ) {
		return $this->videos[ $id ];
	}
}

