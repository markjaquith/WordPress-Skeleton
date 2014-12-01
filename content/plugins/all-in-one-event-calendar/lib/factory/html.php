<?php

/**
 * A factory class for html elements
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Factory
 */
class Ai1ec_Factory_Html extends Ai1ec_Base {

	/**
	 * @var boolean
	 */
	protected $pretty_permalinks_enabled = false;

	/**
	 * @var string
	 */
	protected $page;

	/**
	 * The contructor method.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry
	 ) {
		parent::__construct( $registry );
		$app = $registry->get( 'bootstrap.registry.application' );
		$this->page = $app->get( 'calendar_base_page' );
		$this->pretty_permalinks_enabled = $app->get( 'permalinks_enabled' );
	}

	/**
	 * Creates an instance of the class which generates href for links.
	 *
	 * @param array $args
	 * @param string $type
	 *
	 * @return Ai1ec_Href_Helper
	 */
	public function create_href_helper_instance( array $args, $type = 'normal' ) {
		$href = new Ai1ec_Html_Element_Href( $args, $this->page );
		$href->set_pretty_permalinks_enabled( $this->pretty_permalinks_enabled );
		switch ( $type ) {
			case 'category':
				$href->set_is_category( true );
				break;
			case 'tag':
				$href->set_is_tag( true );
				break;
			case 'author':
				$href->set_is_author( true );
				break;
			default:
				break;
		}
		return $href;
	}

	/**
	 * Create the html element used as the UI control for the datepicker button.
	 * The href must keep only active filters.
	 *
	 * @param array           $args         Populated args for the view
	 * @param int|string|null $initial_date The datepicker's initially set date
	 * @param string          $title        Title to display in datepicker button
	 * @param string          $title_short  Short names in title
	 * @return Ai1ec_Generic_Html_Tag
	 */
	public function create_datepicker_link(
		array $args, $initial_date = null, $title = '', $title_short = ''
	) {
		$settings    = $this->_registry->get( 'model.settings' );
		$date_system = $this->_registry->get( 'date.system' );

		$date_format_pattern = $date_system->get_date_pattern_by_key(
			$settings->get( 'input_date_format' )
		);

		if ( null === $initial_date ) {
			// If exact_date argument was provided, use its value to initialize
			// datepicker.
			if ( isset( $args['exact_date'] ) &&
				$args['exact_date'] !== false &&
				$args['exact_date'] !== null ) {
				$initial_date = $args['exact_date'];
			}
			// Else default to today's date.
			else {
				$initial_date = $date_system->current_time();
			}
		}
		// Convert initial date to formatted date if required.
		if ( Ai1ec_Validation_Utility::is_valid_time_stamp( $initial_date ) ) {
			$initial_date = $date_system->format_date(
				$initial_date,
				$settings->get( 'input_date_format' )
			);
		}

		$href_args = array(
			'action'     => $args['action'],
			'cat_ids'    => $args['cat_ids'],
			'tag_ids'    => $args['tag_ids'],
			'exact_date' => "__DATE__",
		);
		$data_href = $this->create_href_helper_instance( $href_args );

		$attributes = array(
			'data-date' => $initial_date,
			'data-date-format' => $date_format_pattern,
			'data-date-weekstart' => $settings->get( 'week_start_day' ),
			'href' => '#',
			'data-href' => $data_href->generate_href(),
			'data-lang' => str_replace( '_', '-', get_locale() ),
		);
		$loader = $this->_registry->get( 'theme.loader' );
		$file = $loader->get_file( 'date-icon.png' );

		$args = array(
			'attributes'  => $attributes,
			'data_type'   => $args['data_type'],
			'icon_url'    => $file->get_url(),
			'text_date'   => __( 'Choose a date using calendar', AI1EC_PLUGIN_NAME ),
			'title'       => $title,
			'title_short' => $title_short,
		);

		return $loader->get_file( 'datepicker_link.twig', $args );
	}

	/**
	 * Creates a select2 Multiselect.
	 *
	 * @param array $args      The arguments for the select.
	 * @param array $options   The options of the select
	 * @param array $view_args The args used in the front end.
	 *
	 * @return Ai1ec_File_Twig
	 *
	 * @staticvar $cached_flips    Maps of taxonomy identifiers.
	 * @staticvar $checkable_types Map of types and taxonomy identifiers.
	 */
	public function create_select2_multiselect(
		array $args,
		array $options,
		array $view_args = null
	) {
		// if no data is present and we are in the frontend, return a blank
		// element.
		if ( empty( $options ) && null !== $view_args ) {
			return $this->_registry->get( 'html.element.legacy.blank' );
		}
		static $cached_flips    = array();

		static $checkable_types = array(
			'category' => 'cat_ids',
			'tag'      => 'tag_ids',
			'author'   => 'auth_ids',
		);

		$use_id         = isset( $args['use_id'] );
		$options_to_add = array();
		foreach ( $options as $term ) {
			$option_arguments = array();
			$color            = false;
			if ( $args['type'] === 'category' ) {
				$color = $this->_registry->get( 'model.taxonomy' )
					->get_category_color( $term->term_id );
			}
			if ( $color ) {
				$option_arguments['data-color'] = $color;
			}
			if ( null !== $view_args ) {
				// create the href for ajax loading
				$href = $this->create_href_helper_instance(
					$view_args,
					$args['type']
				);
				$href->set_term_id( $term->term_id );
				$option_arguments['data-href'] = $href->generate_href();
				// check if the option is selected
				$type_to_check = '';
				// first let's check the correct type
				if ( isset( $checkable_types[$args['type']] ) ) {
					$type_to_check = $checkable_types[$args['type']];
				}
				// let's flip the array. Just once for performance sake,
				// the categories doesn't change in the same request
				if ( ! isset( $cached_flips[$type_to_check] ) ) {
					$cached_flips[$type_to_check] = array_flip(
						$view_args[$type_to_check]
					);
				}
				if ( isset( $cached_flips[$type_to_check][$term->term_id] ) ) {
					$option_arguments['selected'] = 'selected';
				}
			}
			if ( true === $use_id ) {
				$options_to_add[] = array(
					'text'  => $term->name,
					'value' => $term->term_id,
					'args'  => $option_arguments,
				);
			} else {
				$options_to_add[] = array(
					'text'  => $term->name,
					'value' => $term->name,
					'args'  => $option_arguments,
				);
			}
		}
		$select2_args = array(
			'multiple'         => 'multiple',
			'data-placeholder' => $args['placeholder'],
			'class'            => 'ai1ec-select2-multiselect-selector span12'
		);
		$container_class = false;
		if ( isset( $args['type'] ) ) {
			$container_class = 'ai1ec-' . $args['type'] . '-filter';
		}
		$loader  = $this->_registry->get( 'theme.loader' );
		$select2 = $loader->get_file(
			'select2_multiselect.twig',
			array(
				'name'            => $args['name'],
				'id'              => $args['id'],
				'container_class' => $container_class,
				'select2_args'    => $select2_args,
				'options'         => $options_to_add,
			),
			true
		);
		return $select2;
	}

	/**
	 * Creates a select2 input.
	 *
	 * @param array $args The arguments of the input.
	 *
	 * @return Ai1ec_File_Twig
	 */
	public function create_select2_input( array $args ) {
		if( ! isset ( $args['name'] ) ) {
			$args['name'] = $args['id'];
		}
		// Get tags.
		$tags = get_terms(
			'events_tags',
			array(
				'orderby' => 'name',
				'hide_empty' => 0,
			)
		);

		// Build tags array to pass as JSON.
		$tags_json = array();
		foreach ( $tags as $term ) {
			$tags_json[] = $term->name;
		}
		$tags_json = json_encode( $tags_json );
		$tags_json = _wp_specialchars( $tags_json, 'single', 'UTF-8' );
		$loader =$this->_registry->get( 'theme.loader' );
		$select2_args = array(
			'data-placeholder' => __( 'Tags (optional)', AI1EC_PLUGIN_NAME ),
			'class'            => 'ai1ec-tags-selector span12',
			'data-ai1ec-tags'  => $tags_json
		);
		$select2 = $loader->get_file(
			'select2_input.twig',
			array(
				'name'            => $args['name'],
				'id'              => $args['id'],
				'select2_args'    => $select2_args,

			),
			true
		);
		return $select2;
	}
}
