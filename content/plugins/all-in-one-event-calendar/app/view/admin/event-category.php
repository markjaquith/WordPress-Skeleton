<?php

/**
 * Event category admin view snippets renderer.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View.Admin
 */
class Ai1ec_View_Admin_EventCategory extends Ai1ec_Base {

	/**
	 * Inserts Color element at index 2 of columns array
	 *
	 * @param array $columns Array with event_category columns
	 *
	 * @return array Array with event_category columns where Color is inserted
	 * at index 2
	 */
	public function manage_event_categories_columns( $columns ) {
		wp_enqueue_media();
		$this->_registry->get( 'css.admin' )
			->process_enqueue( array(
				array( 'style', 'bootstrap.min.css' )
			) );
		return array_splice( $columns, 0, 3 ) + // get only first element
			// insert at index 2
			array( 'cat_color' => __( 'Color', AI1EC_PLUGIN_NAME ) ) +
			// insert at index 3
			array( 'cat_image' => __( 'Image', AI1EC_PLUGIN_NAME ) ) +
			// insert rest of elements at the back
			array_splice( $columns, 0 );
	}

	/**
	 * Returns the color or image of the event category.
	 *
	 * That will be displayed on event category lists page in the backend.
	 *
	 * @param $not_set
	 * @param $column_name
	 * @param $term_id
	 * @internal param array $columns Array with event_category columns
	 *
	 * @return array Array with event_category columns where Color is inserted
	 * at index 2
	 */
	public function manage_events_categories_custom_column(
		$not_set,
		$column_name,
		$term_id
	) {
		switch ( $column_name ) {
			case 'cat_color':
				return $this->_registry->get( 'view.event.taxonomy' )
					->get_category_color_square( $term_id );
			case 'cat_image':
				return $this->_registry->get( 'view.event.taxonomy' )
					->get_category_image_square( $term_id );
		}
	}

	/**
	 * Hook to process event categories creation
	 *
	 * @param $term_id
	 *
	 * @return void Method does not return.
	 */
	public function created_events_categories( $term_id ) {
		$this->edited_events_categories( $term_id );
	}

	/**
	 * A callback method, triggered when `event_categories' are being edited.
	 *
	 * @param int $term_id ID of term (category) being edited.
	 *
	 * @return void Method does not return.
	 */
	public function edited_events_categories( $term_id ) {
		if ( isset( $_POST['_inline_edit'] ) ) {
			return;
		}

		$tag_color_value = '';
		if ( ! empty( $_POST['tag-color-value'] ) ) {
			$tag_color_value = (string)$_POST['tag-color-value'];
		}
		$tag_image_value = '';
		if ( ! empty( $_POST['ai1ec_category_image_url'] ) ) {
			$tag_image_value = (string)$_POST['ai1ec_category_image_url'];
		}
		if ( isset( $_POST['ai1ec_category_image_url_remove'] ) ) {
			$tag_image_value = null;
		}

		$db         = $this->_registry->get( 'dbi.dbi' );
		$table_name = $db->get_table_name( 'ai1ec_event_category_meta' );
		$term       = $db->get_row( $db->prepare(
			'SELECT term_id FROM ' . $table_name .
			' WHERE term_id = %d',
			$term_id
		) );

		if ( null === $term ) { // term does not exist, create it
			$db->insert(
				$table_name,
				array(
					'term_id'    => $term_id,
					'term_color' => $tag_color_value,
					'term_image' => $tag_image_value,
				),
				array(
					'%d',
					'%s',
					'%s',
				)
			);
		} else { // term exist, update it
			$db->update(
				$table_name,
				array(
					'term_color' => $tag_color_value,
					'term_image' => $tag_image_value
				),
				array( 'term_id' => $term_id ),
				array( '%s', '%s' ),
				array( '%d' )
			);
		}
	}

	/**
	 * Edit category form
	 *
	 * @param $term
	 *
	 * @return void
	 */
	public function events_categories_edit_form_fields( $term ) {

		$taxonomy = $this->_registry->get( 'model.taxonomy' );
		$color    = $taxonomy->get_category_color( $term->term_id );
		$image    = $taxonomy->get_category_image( $term->term_id );

		$style = '';
		$clr   = '';

		if ( null !== $color ) {
			$style = 'style="background-color: ' . $color . '"';
			$clr   = $color;
		}

		$args = array(
			'style'       => $style,
			'color'       => $clr,
			'label'       => Ai1ec_I18n::__( 'Category Color' ),
			'description' => Ai1ec_I18n::__(
				'Events in this category will be identified by this color'
			),
			'edit'        => true,
		);

		$loader = $this->_registry->get( 'theme.loader' );
		$loader->get_file(
			'setting/categories-color-picker.twig',
			$args,
			true
		)->render();

		$style = 'style="display:none"';

		if ( null !== $image ) {
			$style = '';
		}

		// Category image
		$args  = array(
			'image_src'    => $image,
			'image_style'  => $style,
			'section_name' => __( 'Category Image', AI1EC_PLUGIN_NAME ),
			'label'        => __( 'Add Image', AI1EC_PLUGIN_NAME ),
			'remove_label' => __( 'Remove Image', AI1EC_PLUGIN_NAME ),
			'description'  => __(
				'Assign an optional image to the category. Recommended size: square, minimum 400&times;400 pixels.',
				AI1EC_PLUGIN_NAME
			),
			'edit'         => true,
		);

		$loader->get_file(
			'setting/categories-image.twig',
			$args,
			true
		)->render();
	}

	/**
	 * Add category form
	 *
	 * @return void
	 */
	public function events_categories_add_form_fields() {

		$loader = $this->_registry->get( 'theme.loader' );

		// Category color
		$args  = array(
			'color'        => '',
			'style'        => '',
			'label'        => __( 'Category Color', AI1EC_PLUGIN_NAME ),
			'remove_label' => __( 'Remove Image', AI1EC_PLUGIN_NAME ),
			'description'  => __(
				'Events in this category will be identified by this color',
				AI1EC_PLUGIN_NAME
			),
			'edit'        => false
		);

		$file   = $loader->get_file(
			'setting/categories-color-picker.twig',
			$args,
			true
		);

		$file->render();

		// Category image
		$args  = array(
			'image_src'    => '',
			'image_style'  => 'style="display:none"',
			'section_name' => __( 'Category Image', AI1EC_PLUGIN_NAME ),
			'label'        => __( 'Add Image', AI1EC_PLUGIN_NAME),
			'description'  => __( 'Assign an optional image to the category. Recommended size: square, minimum 400&times;400 pixels.', AI1EC_PLUGIN_NAME ),
			'edit'         => false,
		);

		$file   = $loader->get_file(
			'setting/categories-image.twig',
			$args,
			true
		);

		$file->render();
	}

}
