<?php

class AttributeLoader {

	public function __construct( $attributeGroupLoader ) {

		$this->attributeGroupLoader = $attributeGroupLoader;

		$this->field_names = array(
			'id',
			'attribute_group_id',
			'attribute_code',
			'sub_attribute_code',
			'attribute_desc',
			'attribute_parent_id',
			'attribute_order',
			'attribute_status',
			'views',
			'lastviewed',
			'show',
		);

		$this->attributes = $this->load_file();

		echo count($this->attributes) . ' attributes...<br/>';

		//
		// Build tree of attributes
		//
		foreach ($this->attributes as $id => $attribute) {

			if( empty($attribute->attribute_parent_id) )
				continue;

			if( ! isset($this->attributes[$attribute->attribute_parent_id]) )
				continue;

			$attribute_parent = $this->attributes[$attribute->attribute_parent_id];

			if( empty($attribute_parent->attributes) )
				$attribute_parent->attributes = array();
			
			$attribute_parent->attributes[] = $attribute;
		}
	}

	public function load_file() {

		$content = file_get_contents(dirname( __FILE__ ) . '/../TRC_Media_Library_Dat/attribute.dat');
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
			$buffer['attribute_group'] = null;
			$buffer['media_attributes'] = array();
			$buffer['attributes'] = null;
			$buffer['term_id'] = 0;

			$attribute = $this->attributeGroupLoader->find_group_and_bind_attribute( (object)$buffer );

			$result[$attribute->id] = $attribute;
		}

		return $result;
	}

	public function find_attribute_and_bind_media_attribute( $media_attribute ) {

		$attribute = $this->find( $media_attribute->attribute_id );
		$attribute->media_attributes[] = $media_attribute;
		$media_attribute->attribute = $attribute;

		return $media_attribute;
	}

	public function find( $id ) {

		return $this->attributes[ $id ];
	}
}
