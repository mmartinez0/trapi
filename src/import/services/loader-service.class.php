<?php

require( 'attribute-group-loader.class.php' );
require( 'attribute-loader.class.php' );
require( 'media-attribute-loader.class.php' );
require( 'media-loader.class.php' );

class LoaderService {

	public function __construct( ) {

		$this->attributeGroupLoader = new AttributeGroupLoader();
		$this->attributeLoader = new AttributeLoader( $this->attributeGroupLoader );
		$this->mediaLoader = new MediaLoader();
		$this->mediaAttributeLoader = new MediaAttributeLoader( $this->attributeLoader, $this->mediaLoader );
	}

	private function register_taxonomy( $item ){

		$this->groups[$item->id] = $item;
	}

	public function print_taxonomia() {

		echo '<h1 class="taxonomia">Taxonomia</h1>';

		$attribute_groups = $this->attributeGroupLoader->attribute_groups;

		foreach ($attribute_groups as $attribute_group) { ?>
			<h1><?php echo $attribute_group->name; ?> | <?php echo $attribute_group->slug; ?></h1>
			<ul><?php 
				$this->print_attributes($attribute_group->slug, $attribute_group->attributes, 0, 0); ?>
			</ul><?php
		}
	}

	private function print_attributes( $taxonomy, $attributes, $attribute_parent_id, $parent_term_id ) {

		foreach ($attributes as $attribute) {

			if( $attribute->attribute_parent_id != $attribute_parent_id )
				continue;?>

			<li><?php

				echo "[$attribute->id] $attribute->attribute_desc ( taxonomy: $taxonomy )";
				$attribute->term_id = $this->insert_term( $attribute->attribute_desc, $taxonomy, $parent_term_id );
				echo " (term_id: $attribute->term_id )";

				if( ! empty($attribute->attributes) ){?>
					<ul><?php
						$this->print_attributes( $taxonomy, $attribute->attributes, $attribute->id, $attribute->term_id ); ?>
					</ul><?php
				}?>
			</li><?php
		}
	}

	static protected function count_properties( $a ) {
		if( empty($attribute->attributes) )
			return '';
		return (string)count($attribute->attributes);
	}

	static private function print_wp_taxonomies() {

		foreach (get_taxonomies() as $taxonomy) {
			echo "<div>$taxonomy</div>";
		}
	}

	static private function insert_term( $name, $taxonomy, $parent_term_id ) {

		$term = wp_insert_term( $name, $taxonomy, array('parent' => $parent_term_id) );
		if( is_wp_error($term) && $term->get_error_code() == 'term_exists' ){
			return (int)$term->get_error_data();
		}
		return (int)$term['term_id'];
	}
}

