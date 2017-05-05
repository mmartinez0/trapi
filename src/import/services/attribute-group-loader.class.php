<?php

class AttributeGroupLoader {

	public function __construct() {

		$this->attribute_groups = array(
			(object)array('taxonomy_id' => 0, 'id' =>  '1', 'name' => 'Media Types', 					'taxonomy' => 'media', 			'single' => 'Media Type',					'attributes' => array(), 'slug' => ''),
			(object)array('taxonomy_id' => 0, 'id' =>  '2', 'name' => 'Specific Shots', 				'taxonomy' => 'specific', 		'single' => 'Specific Shot', 				'attributes' => array(), 'slug' => ''),
			(object)array('taxonomy_id' => 0, 'id' => '13', 'name' => 'General Performance Components', 'taxonomy' => 'performance', 	'single' => 'General Performance Component','attributes' => array(), 'slug' => ''),
			(object)array('taxonomy_id' => 0, 'id' => '16', 'name' => 'Drill/Lesson', 					'taxonomy' => 'drill', 			'single' => 'Drill/Lesson', 				'attributes' => array(), 'slug' => ''),
			(object)array('taxonomy_id' => 0, 'id' => '23', 'name' => 'Shot Anatomy (High-Speed Film)', 'taxonomy' => 'anatomy', 		'single' => 'Shot Anatomy', 				'attributes' => array(), 'slug' => ''),
			(object)array('taxonomy_id' => 0, 'id' => '24', 'name' => 'Demographics',					'taxonomy' => 'demographics', 	'single' => 'Demographic', 					'attributes' => array(), 'slug' => '')
		);

		foreach ($this->attribute_groups as $attribute_group) {
			$attribute_group->slug = sanitize_title($attribute_group->name);
		}

		echo count($this->attribute_groups) . ' attribute_groups...<br/>';
	}

	public function find_group_and_bind_attribute( $attribute ) {

		$attribute_group = $this->find( $attribute->attribute_group_id );

		$attribute_group->attributes[] = $attribute;
		$attribute->attribute_group = $attribute_group;
		
		return $attribute;
	}

	private function find( $id ) {

		foreach ($this->attribute_groups as $attribute_group) {
			if( $attribute_group->id == $id )
				return $attribute_group;
		}

		return null;
	}
}
