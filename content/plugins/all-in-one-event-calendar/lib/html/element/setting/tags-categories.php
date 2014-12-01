<?php

/**
 * Renderer of settings page tags and categories option.
 *
 * @author       Time.ly Network, Inc.
 * @instantiator new
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Html
 */
class Ai1ec_Html_Setting_Tags_Categories extends Ai1ec_Html_Element_Settings {


	/* (non-PHPdoc)
	 * @see Ai1ec_Html_Element_Settings::render()
	 */
	public function render( $output = '' ) {
		$tags       = array();
		$categories = array();
		foreach ( array( 'tags', 'categories' ) as $type ) {
			$options = array(
				'taxonomy'     => 'events_' . $type,
				'hierarchical' => true,
			);
			${$type} = get_categories( $options );
		}
		if ( empty ( $tags ) && empty ( $categories ) ) {
			return '';
		}
		$args = array(
			'label' => $this->_args['renderer']['label'],
			'help'  => $this->_args['renderer']['help'],
		);
		$loader = $this->_registry->get( 'theme.loader' );
		if ( ! empty ( $tags ) ) {
			$args['tags'] = $this->_get_select_for_terms(
				'tags',
				Ai1ec_I18n::__( 'Tags' ),
				$tags
			);
		}
		$categories_html = '';
		if ( ! empty ( $categories ) ) {
			$args['categories'] = $this->_get_select_for_terms(
				'categories',
				Ai1ec_I18n::__( 'Categories' ),
				$categories
			);
		}
		return $loader->get_file( 'setting/tags-categories.twig', $args, true )
						->get_content();
	}

	/**
	 * Creates the multiselect for tags and categories
	 *
	 * @param string $type
	 * @param string $label
	 * @param array $terms
	 *
	 * @return string The html for the select
	 */
	protected function _get_select_for_terms( $type, $label, array $terms ) {
		$loader  = $this->_registry->get( 'theme.loader' );
		$options = array();
		foreach ( $terms as $term ) {
			$option = array(
				'value' => $term->term_id,
				'text'  => $term->name,
			);
			if ( isset( $this->_args['value'][$type] ) ) {
				if ( in_array( $term->term_id , $this->_args['value'][$type] ) ) {
					$option['args'] = array(
						'selected' => 'selected',
					);
				}
			}
			$options[] = $option;
		}
		$args = array(
			'id'         => $this->_args['id'] . '_default_' . $type,
			'name'       => $this->_args['id'] . '_default_' . $type . '[]',
			'label'      => $label,
			'options'    => $options,
			'stacked'    => true,
			'attributes' => array(
				'class'    => 'ai1ec-form-control',
				'multiple' => 'multiple',
				// for Widget creator
				'data-id'  => 'tags' === $type ? 'tag_ids' : 'cat_ids',
			),
		);
		return $loader->get_file( 'setting/select.twig', $args, true )
						->get_content();
	}

}
