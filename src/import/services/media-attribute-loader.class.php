<?php

class MediaAttributeLoader {

	public function __construct( $attributeLoader, $mediaLoader ) {

		$this->attributeLoader = $attributeLoader;
		$this->mediaLoader = $mediaLoader;

		$this->field_names = array(
			'id',
			'media_id',
			'user_id',
			'attribute_id',
			'attr_relevance',
			'additional_desc',
			'DateCreated',
			'media_order'
		);

		$this->list = array();
		$this->media_attributes = $this->load_file();

		echo count($this->list) . ' total media_attributes...<br/>';
		echo count($this->media_attributes) . ' media_attributes...<br/>';
	}

	public function load_file() {

		$content = file_get_contents(dirname( __FILE__ ) . '/../TRC_Media_Library_Dat/media_attribute.dat');
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
				$buffer[$field_name] = $fields[$key];
			}
			$buffer['attribute'] = null;
			$buffer['video'] = null;

			$media_attribute = (object)$buffer;

			$this->list[] = $media_attribute;

			$this->attributeLoader->find_attribute_and_bind_media_attribute( $media_attribute );

			if( ! isset($result[$media_attribute->media_id]) )
				$result[$media_attribute->media_id] = array();

			$video = $this->mediaLoader->find_video( $media_attribute->media_id );

			$media_attribute->video = $video;

			$video->media_attributes[] = $media_attribute;

			$result[$media_attribute->media_id][] = $media_attribute;
		}

		return $result;
	}
}
