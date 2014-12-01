<?php

/**
 * Calendar Widget class
 *
 * A widget that displays the next X upcoming events (similar to Agenda view).
 */
class Ai1ec_View_Admin_Widget extends Ai1ec_Embeddable {

	/**
	 * @var boolean
	 */
	protected $_css_loaded = false;

	/**
	 * @return string
	 */
	public function get_id() {
		return 'ai1ec_agenda_widget';
	}

	/**
	 * Register the widget class.
	 */
	public static function register_widget() {
		register_widget( 'Ai1ec_View_Admin_Widget' );
	}

	/**
	 * Constructor for widget.
	 */
	public function __construct() {

		parent::__construct(
			$this->get_id(),
			__( 'Upcoming Events', AI1EC_PLUGIN_NAME ),
			array(
				'description' => __( 'All-in-One Event Calendar: Lists upcoming events in Agenda view', AI1EC_PLUGIN_NAME ),
				'class' => 'ai1ec-agenda-widget',
			)
		);
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Embeddable::register_javascript_widget()
	 */
	public function register_javascript_widget( $id_base ) {
		$this->_registry->get( 'controller.javascript-widget' )
			->add_widget( $id_base, 'view.calendar.widget' );
	}
	/* (non-PHPdoc)
	 * @see Ai1ec_Embeddable::get_defaults()
	 */
	public function get_defaults() {
		return array(
			'title'                  => __( 'Upcoming Events', AI1EC_PLUGIN_NAME ),
			'events_seek_type'       => 'events',
			'events_per_page'        => 10,
			'days_per_page'          => 10,
			'show_subscribe_buttons' => true,
			'show_calendar_button'   => true,
			'hide_on_calendar_page'  => true,
			'limit_by_cat'           => false,
			'limit_by_tag'           => false,
			'cat_ids'                => array(),
			'tag_ids'                => array(),
			'link_for_days'          => true,
		);
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Embeddable::get_configurable_for_widget_creation()
	 */
	public function get_configurable_for_widget_creation() {
		$defaults = $this->get_js_widget_configurable_defaults();
		return array(
			'events_seek_type' => array(
				'renderer' => array(
					'class'   => 'select',
					'label'   => __(
						'Choose how to limit the upcoming events',
						AI1EC_PLUGIN_NAME
					),
					'options' => array(
						array(
							'text'  => __(
								'Events',
								AI1EC_PLUGIN_NAME
							),
							'value' => 'events'
						),
						array(
							'text'  => __(
								'Days',
								AI1EC_PLUGIN_NAME
							),
							'value' => 'days'
						),
					),
				),
				'value' => $defaults['events_seek_type']
			),
			'events_per_page' => array(
				'renderer' => array(
					'class'     => 'input',
					'label'     => Ai1ec_I18n::__( 'Number of events to show' ),
					'type'      => 'append',
					'append'    => 'events',
				),
				'value'  => $defaults['events_per_page'],
			),
			'days_per_page' => array(
				'renderer' => array(
					'class'     => 'input',
					'label'     => Ai1ec_I18n::__( 'Number of days to show' ),
					'type'      => 'append',
					'append'    => 'days',
				),
				'value'  => $defaults['days_per_page'],
			),
			'upcoming_widgets_default_tags_categories' => array(
				'renderer' => array(
					'class' => 'tags-categories',
					'label' => __(
						'Show events filtered for the following tags/categories',
						AI1EC_PLUGIN_NAME
					),
					'help'  => __(
						'To clear, hold &#8984;/<abbr class="initialism">CTRL</abbr> and click selection.',
						AI1EC_PLUGIN_NAME
					)
				),
				'value' => array(
					'categories' => array(),
					'tags'       => array(),
				),
			),
			'show_subscribe_buttons' => array(
				'renderer' => array(
					'class'     => 'checkbox',
					'label'     => Ai1ec_I18n::__( 'Show the subscribe button in the widget' ),
				),
				'value'  => $defaults['show_subscribe_buttons'],
			),
		);
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Calendar_View_Abstract::get_name()
	*/
	public function get_name() {
		return 'Upcoming Events';
	}

	/**
	 * Form function.
	 *
	 * Renders the widget's configuration form for the Manage Widgets page.
	 *
	 * @param  array $instance The data array for the widget instance being configured.
	 * @return void
	 */
	public function form( $instance ) {
		$default = $this->get_defaults();
		$instance = wp_parse_args( (array) $instance, $default );

		// Get available cats, tags, events to allow user to limit widget to certain categories
		$events_categories = get_terms( 'events_categories', array( 'orderby' => 'name', "hide_empty" => false ) );
		$events_tags       = get_terms( 'events_tags', array( 'orderby' => 'name', "hide_empty" => false ) );

		// Generate unique IDs and NAMEs of all needed form fields
		$fields = array(
			'title'                  => array('value'   => $instance['title']),
			'events_seek_type'       => array('value'   => $instance['events_seek_type']),
			'events_per_page'        => array('value'   => $instance['events_per_page']),
			'days_per_page'          => array('value'   => $instance['days_per_page']),
			'show_subscribe_buttons' => array('value'   => $instance['show_subscribe_buttons']),
			'show_calendar_button'   => array('value'   => $instance['show_calendar_button']),
			'hide_on_calendar_page'  => array('value'   => $instance['hide_on_calendar_page']),
			'limit_by_cat'           => array('value'   => $instance['limit_by_cat']),
			'limit_by_tag'           => array('value'   => $instance['limit_by_tag']),
			'cat_ids'          => array(
			                                  'value'   => (array)$instance['cat_ids'],
			                                  'options' => $events_categories
			                                 ),
			'tag_ids'          => array(
			                                  'value'   => (array)$instance['tag_ids'],
			                                  'options' => $events_tags
			                                 ),
		);
		foreach ( $fields as $field => $data ) {
			$fields[$field]['id']    = $this->get_field_id( $field );
			$fields[$field]['name']  = $this->get_field_name( $field );
			$fields[$field]['value'] = $data['value'];
			if ( isset($data['options']) ) {
				$fields[$field]['options'] = $data['options'];
			}
		}

		// Display theme
		$this->_registry->get( 'theme.loader' )->get_file(
			'agenda-widget-form.php',
			$fields,
			true
		)->render();
	}

	/**
	 * Update function.
	 *
	 * Called when a user submits the widget configuration form.
	 * The data should be validated and returned.
	 *
	 * @param  array $new_instance The new data that was submitted.
	 * @param  array $old_instance The widget's old data.
	 * @return array               The new data to save for this widget instance.
	 */
	public function update( $new_instance, $old_instance ) {
		// Save existing data as a base to modify with new data
		$instance = $old_instance;
		$instance['title']                  = strip_tags( $new_instance['title'] );
		$instance['events_per_page']        = Ai1ec_Primitive_Int::index(
			$new_instance['events_per_page'],
			1,
			1
		);
		$instance['days_per_page']          = Ai1ec_Primitive_Int::index(
			$new_instance['days_per_page'],
			1,
			1
		);
		$instance['events_seek_type']       = $this->_valid_seek_type(
			$new_instance['events_seek_type']
		);
		$instance['show_subscribe_buttons'] = isset( $new_instance['show_subscribe_buttons'] ) ? true : false;
		$instance['show_calendar_button']   = isset( $new_instance['show_calendar_button'] ) ? true : false;
		$instance['hide_on_calendar_page']  = isset( $new_instance['hide_on_calendar_page'] ) ? true : false;

		// For limits, set the limit to False if no IDs were selected, or set the respective IDs to empty if "limit by" was unchecked
		$instance['limit_by_cat'] = false;
		$instance['cat_ids'] = array();
		if ( isset( $new_instance['cat_ids'] ) && $new_instance['cat_ids'] != false ) {
			$instance['limit_by_cat'] = true;
		}
		if ( isset( $new_instance['limit_by_cat'] ) && $new_instance['limit_by_cat'] != false ) {
			$instance['limit_by_cat'] = true;
		}
		if ( isset( $new_instance['cat_ids'] ) && $instance['limit_by_cat'] === true ) {
			$instance['cat_ids'] = $new_instance['cat_ids'];
		}

		$instance['limit_by_tag'] = false;
		$instance['tag_ids'] = array();
		if ( isset( $new_instance['tag_ids'] ) && $new_instance['tag_ids'] != false ) {
			$instance['limit_by_tag'] = true;
		}
		if ( isset( $new_instance['limit_by_tag'] ) && $new_instance['limit_by_tag'] != false ) {
			$instance['limit_by_tag'] = true;
		}
		if ( isset( $new_instance['tag_ids'] ) && $instance['limit_by_tag'] === true ) {
			$instance['tag_ids'] = $new_instance['tag_ids'];
		}

		return $instance;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Embeddable::add_js()
	 */
	public function add_js() {
		$this->_registry->get( 'controller.javascript' )->add_link_to_render_js(
			Ai1ec_Javascript_Controller::LOAD_ONLY_FRONTEND_SCRIPTS,
			false
		);
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Embeddable::get_content()
	 */
	public function get_content( array $args_for_widget, $remote = false ) {
		$agenda     = $this->_registry->get(
			'view.calendar.view.agenda',
			$this->_registry->get( 'http.request.parser' )
		);
		$time       = $this->_registry->get( 'date.time' );
		$search     = $this->_registry->get( 'model.search' );
		$settings   = $this->_registry->get( 'model.settings' );
		$html       = $this->_registry->get( 'factory.html' );

		$is_calendar_page = is_page( $settings->get( 'calendar_page_id' ) );
		if ( $args_for_widget['hide_on_calendar_page'] &&
			$is_calendar_page ) {
			return;
		}

		// Add params to the subscribe_url for filtering by Limits (category, tag)
		$subscribe_filter  = '';
		if ( ! is_array( $args_for_widget['cat_ids'] ) ) {
			$args_for_widget['cat_ids'] = explode( ',', $args_for_widget['cat_ids'] );
		}

		if ( ! is_array( $args_for_widget['tag_ids'] ) ) {
			$args_for_widget['tag_ids'] = explode( ',', $args_for_widget['tag_ids'] );
		}
		$subscribe_filter .= $args_for_widget['cat_ids'] ? '&ai1ec_cat_ids=' . join( ',', $args_for_widget['cat_ids'] ) : '';
		$subscribe_filter .= $args_for_widget['tag_ids'] ? '&ai1ec_tag_ids=' . join( ',', $args_for_widget['tag_ids'] ) : '';

		// Get localized time
		$timestamp = $time->format_to_gmt();

		// Set $limit to the specified category/tag
		$limit = array(
			'cat_ids'   => $args_for_widget['cat_ids'],
			'tag_ids'   => $args_for_widget['tag_ids'],
		);

		// Get events, then classify into date array
		// JB: apply seek check here
		$seek_days  = ( 'days' === $args_for_widget['events_seek_type'] );
		$seek_count = $args_for_widget['events_per_page'];
		$last_day   = false;
		if ( $seek_days ) {
			$seek_count = $args_for_widget['days_per_page'] * 5;
			$last_day   = strtotime(
				'+' . $args_for_widget['days_per_page'] . ' days'
			);
		}

		$event_results = $search->get_events_relative_to(
			$timestamp,
			$seek_count,
			0,
			$limit
		);
		if ( $seek_days ) {
			foreach ( $event_results['events'] as $ek => $event ) {
				if ( $event->get( 'start' )->format() >= $last_day ) {
					unset( $event_results['events'][$ek] );
				}
			}
		}

		$dates                    = $agenda->get_agenda_like_date_array( $event_results['events'] );


		$args_for_widget['dates']                     = $dates;
		// load CSS just once for all widgets.
		// Do not load it on the calendar page as it's already loaded.
		if ( false === $this->_css_loaded && ! $is_calendar_page ) {
			if ( true === $remote ) {
				$args_for_widget['css'] = $this->_registry->get( 'css.frontend' )->get_compiled_css();
			}
			$this->_css_loaded = true;
		}
		$args_for_widget['show_location_in_title']    = $settings->get( 'show_location_in_title' );
		$args_for_widget['show_year_in_agenda_dates'] = $settings->get( 'show_year_in_agenda_dates' );
		$args_for_widget['calendar_url']              = $html->create_href_helper_instance( $limit )->generate_href();
		$args_for_widget['subscribe_url']             = AI1EC_EXPORT_URL . $subscribe_filter;
		$args_for_widget['subscribe_url_no_html']     = AI1EC_EXPORT_URL . '&no_html=true' . $subscribe_filter;
		$args_for_widget['text_upcoming_events']      = __( 'There are no upcoming events.', AI1EC_PLUGIN_NAME );
		$args_for_widget['text_all_day']              = __( 'all-day', AI1EC_PLUGIN_NAME );
		$args_for_widget['text_view_calendar']        = __( 'View Calendar', AI1EC_PLUGIN_NAME );
		$args_for_widget['text_edit']                 = __( 'Edit', AI1EC_PLUGIN_NAME );
		$args_for_widget['text_venue_separator']      = __( '@ %s', AI1EC_PLUGIN_NAME );
		$args_for_widget['text_subscribe_label']      = __( 'Add', AI1EC_PLUGIN_NAME );
		$args_for_widget['subscribe_buttons_text']    = $this->_registry
			->get( 'view.calendar.subscribe-button' )
			->get_labels();
		// Display theme
		return $this->_registry->get( 'theme.loader' )->get_file(
			'agenda-widget.twig',
			$args_for_widget
		)->get_content();
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Embeddable::get_js_widget_configurable_defaults()
	 */
	public function get_js_widget_configurable_defaults() {
		$def = $this->get_defaults();
		unset( $def['title'] );
		unset( $def['link_for_days'] );
		return $def;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Embeddable::javascript_widget()
	 */
	public function javascript_widget( $args ) {
		$args['show_calendar_button'] = false;
		$args['link_for_days']        = false;
		return parent::javascript_widget( $args );
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Embeddable::check_requirements()
	 */
	public function check_requirements() {
		return null;
	}

	/**
	 * _valid_seek_type method.
	 *
	 * Return valid seek type for given user input (selection).
	 *
	 * @param  string $value User selection for seek type
	 * @return string        Seek type to use
	 */
	protected function _valid_seek_type( $value ) {
		static $list = array( 'events', 'days' );
		if ( ! in_array( $value, $list ) ) {
			return (string)reset( $list );
		}
		return $value;
	}

}
