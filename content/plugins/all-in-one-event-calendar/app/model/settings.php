<?php

/**
 * Model used for storing/retrieving plugin options.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Model
 */
class Ai1ec_Settings extends Ai1ec_App {

	/**
	 * @constant string Name of WordPress options key used to store settings.
	 */
	const WP_OPTION_KEY          = 'ai1ec_settings';

	/**
	 * @var array Map of value names and their representations.
	 */
	protected $_options          = array();

	/**
	 * @var bool Indicator for modified object state.
	 */
	protected $_updated          = false;

	/**
	 * @var array The core options of the plugin.
	 */
	protected $_standard_options;

	/**
	 * Register new option to be used.
	 *
	 * @param string $option   Name of option.
	 * @param mixed  $value    The value.
	 * @param string $type     Option type to be used for validation.
	 * @param string $renderer Name of class to render the option.
	 *
	 * @return Ai1ec_Settings Instance of self for chaining.
	 */
	public function register(
		$option,
		$value,
		$type,
		$renderer,
		$version = '2.0.0'
	) {
		if ( 'deprecated' === $type ) {
			unset( $this->_options[$option] );
		} else if (
			! isset( $this->_options[$option] ) ||
			! isset( $this->_options[$option]['version'] ) ||
			(string)$this->_options[$option]['version'] !== (string)$version
		) {
			$this->_options[$option] = array(
				'value'    => ( isset( $this->_options[$option] ) )
					? $this->_options[$option]['value']
					: $value,
				'type'     => $type,
				'legacy'   => false,
				'version'  => $version,
			);
			if ( null !== $renderer ) {
				$this->_options[$option]['renderer'] = $renderer;
			}
		}
		return $this;
	}

	/**
	 * Gets the options.
	 *
	 * @return array:
	 */
	public function get_options() {
		return $this->_options;
	}

	/**
	 * Get field options as registered.
	 *
	 * @param string $option Name of option field to describe.
	 *
	 * @return array|null Description or null if nothing is found.
	 */
	public function describe( $option ) {
		if ( ! isset( $this->_options[$option] ) ) {
			return null;
		}
		return $this->_options[$option];
	}

	/**
	 * Get value for option.
	 *
	 * @param string $option  Name of option to get value for.
	 * @param mixed  $default Value to return if option is not found.
	 *
	 * @return mixed Value or $default if none is found.
	 */
	public function get( $option, $default = null ) {
		// notice, that `null` is not treated as a value
		if ( ! isset( $this->_options[$option] ) ) {
			return $default;
		}
		return $this->_options[$option]['value'];
	}

	/**
	 * Set new value for previously initialized option.
	 *
	 * @param string $option Name of option to update.
	 * @param mixed  $value  Actual value to be used for option.
	 *
	 * @throws Ai1ec_Settings_Exception
	 *
	 * @return Ai1ec_Settings Instance of self for chaining.
	 */
	public function set( $option, $value ) {
		if ( ! isset( $this->_options[$option] ) ) {
			throw new Ai1ec_Settings_Exception(
				'Option "' . $option . '" was not registered'
			);
		}
		if ( 'array' === $this->_options[$option]['type'] ) {
			if (
				! is_array( $this->_options[$option]['value'] ) ||
				! is_array( $value ) ||
				$value != $this->_options[$option]['value']
			) {
				$this->_options[$option]['value'] = $value;
				$this->_change_update_status ( true );
			}
		} else if (
			(string)$value !== (string)$this->_options[$option]['value']
		) {
			$this->_options[$option]['value'] = $value;
			$this->_change_update_status ( true );
		}
		return $this;
	}

	/**
	 * Parse legacy values into new structure.
	 *
	 * @param mixed $values Expected legacy representation.
	 *
	 * @return array Parsed values representation, or input cast as array.
	 */
	protected function _parse_legacy( Ai1ec_Settings $values ) {
		$variables        = get_object_vars( $values );
		$default_tags_cat = array();
		$legacy           = array();
		foreach ( $variables as $key => $value ) {
			if ( 'default_categories' === $key ) {
				$default_tags_cat['categories'] = $value;
				continue;
			}
			if ( 'default_tags' === $key ) {
				$default_tags_cat['tags'] = $value;
				continue;
			}
			$type = 'string';
			if ( is_array( $value ) ) {
				$type = 'array';
			} elseif ( is_bool( $value ) ) {
				$type = 'bool';
			} elseif ( is_int( $value ) ) {
				$type = 'int';
			}
			if ( isset( $this->_options[$key] ) ) {
				$this->_options[$key]['value'] = $value;
			} else {
				$legacy[$key] = array(
					'value'    => $value,
					'type'     => $type,
					'legacy'   => true,
					'version'  => AI1EC_VERSION
				);
			}
		}
		$this->_options['default_tags_categories']['value'] = $default_tags_cat;
		$this->_options['legacy_options'] = $legacy;
	}

	/**
	 * Write object representation to persistence layer.
	 *
	 * Upon successful write to persistence layer the objects internal
	 * state {@see self::$_updated} is updated respectively.
	 *
	 * @return bool Success.
	 */
	public function persist() {
		$success = $this->_registry->get( 'model.option' )
			->set( self::WP_OPTION_KEY, $this->_options );
		if ( $success ) {
			$this->_change_update_status( false );
		}
		return $success;
	}

	/**
	 * Remove an option if is set.
	 *
	 * @param string $option
	 */
	public function remove_option( $option ) {
		if ( isset( $this->_options[$option] ) ) {
			unset( $this->_options[$option] );
			$this->_change_update_status( true );
		}
	}


	/**
	 * Do things needed on every plugin upgrade.
	 */
	public function perform_upgrade_actions() {
		$option = $this->_registry->get( 'model.option' );
		$option->set( 'ai1ec_force_flush_rewrite_rules',      true, true );
		$option->set( 'ai1ec_invalidate_css_cache',           true, true );
		$option->set( Ai1ec_Theme_Loader::OPTION_FORCE_CLEAN, true, true );
	}

	/**
	 * Hide an option by unsetting it's renderer
	 *
	 * @param string $option
	 */
	public function hide_option( $option ) {
		if ( isset( $this->_options[$option] ) ) {
			unset( $this->_options[$option]['renderer'] );
			$this->_change_update_status( true );
		}
	}

	/**
	 * Show an option by setting it's renderer
	 *
	 * @param string $option
	 */
	public function show_option( $option, array $renderer ) {
		if ( isset( $this->_options[$option] ) ) {
			$this->_options[$option]['renderer'] = $renderer;
			$this->_change_update_status( true );
		}
	}

	/**
	 * Check object state and update it's database representation as needed.
	 *
	 * @return void Destructor does not return.
	 */
	public function shutdown() {
		if ( $this->_updated ) {
			$this->persist();
		}
	}

	/**
	 * Initiate options map from storage.
	 *
	 * @return void Return from this method is ignored.
	 */
	protected function _initialize() {
		$this->_set_standard_values();
		$values       = $this->_registry->get( 'model.option' )
			->get( self::WP_OPTION_KEY, array() );
		$this->_change_update_status( false );
		$test_version = false;
		if ( is_array( $values ) ) { // always assign existing values, if any
			$this->_options = $values;
			if ( isset( $values['calendar_page_id'] ) ) {
				$test_version = $values['calendar_page_id']['version'];
			}
		}
		$upgrade = false;
		if ( // process meta updates changes
			empty( $values ) || (
				false !== $test_version &&
				AI1EC_VERSION !== $test_version
			)
		) {
			$this->_register_standard_values();
			$this->_update_name_translations();
			$this->_change_update_status( true );
			$upgrade = true;
		} else if ( $values instanceof Ai1ec_Settings ) { // process legacy
			$this->_register_standard_values();
			$this->_parse_legacy( $values );
			$this->_change_update_status( true );
			$upgrade = true;
		}
		if ( true === $upgrade ) {
			$this->perform_upgrade_actions();
		}
		$this->_registry->get( 'controller.shutdown' )->register(
			array( $this, 'shutdown' )
		);
	}

	/**
	 * Set the standard values for the options of the core plugin.
	 *
	 */
	protected function _set_standard_values() {
		$this->_standard_options = array(
			'ai1ec_db_version' => array(
				'type' => 'int',
				'default'  => false,
			),
			'feeds_page' => array(
				'type' => 'string',
				'default'  => false,
			),
			'settings_page' => array(
				'type' => 'string',
				'default'  => false,
			),
			'less_variables_page' => array(
				'type' => 'string',
				'default'  => false,
			),
			'input_date_format' => array(
				'type' => 'string',
				'default'  => 'd/m/yyyy',
			),
			'plugins_options' => array(
				'type' => 'array',
				'default'  => array(),
			),
			'show_tracking_popup' => array(
				'type'    => 'bool',
				'default' => true,
			),
			'calendar_page_id' => array(
				'type' => 'mixed',
				'renderer' => array(
					'class' => 'calendar-page-selector',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__( 'Calendar page' )
				),
				'default'  => false,
			),
			'week_start_day' => array(
				'type' => 'int',
				'renderer' => array(
					'class'   => 'select',
					'tab'     => 'viewing-events',
					'item'    => 'viewing-events',
					'label'   => Ai1ec_I18n::__( 'Week starts on' ),
					'options' => 'get_weekdays',
				),
				'default'  => $this->_registry->get( 'model.option' )->get(
					'start_of_week'
				),
			),
			'enabled_views' => array(
				'type' => 'array',
				'renderer' => array(
					'class' => 'enabled-views',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__( 'Available views' ),
				),
				'default'  => array(
					'agenda' => array(
						'enabled'        => true,
						'default'        => true,
						'enabled_mobile' => true,
						'default_mobile' => true,
						'longname'       => _n_noop(
							'Agenda',
							'Agenda',
							AI1EC_PLUGIN_NAME
						),
					),
					'oneday' => array(
						'enabled'        => true,
						'default'        => false,
						'enabled_mobile' => true,
						'default_mobile' => false,
						'longname'       => _n_noop(
							'Day',
							'Day',
							AI1EC_PLUGIN_NAME
						),
					),
					'month' => array(
						'enabled'        => true,
						'default'        => false,
						'enabled_mobile' => true,
						'default_mobile' => false,
						'longname'       => _n_noop(
							'Month',
							'Month',
							AI1EC_PLUGIN_NAME
						),
					),
					'week' => array(
						'enabled'        => true,
						'default'        => false,
						'enabled_mobile' => true,
						'default_mobile' => false,
						'longname'       => _n_noop(
							'Week',
							'Week',
							AI1EC_PLUGIN_NAME
						),
					),
				),
			),
			'timezone_string' => array(
				'type' => 'wp_option',
				'renderer' => array(
					'class'     => 'select',
					'tab'       => 'viewing-events',
					'item'      => 'viewing-events',
					'label'     => Ai1ec_I18n::__( 'Timezone' ),
					'options'   => 'Ai1ec_Date_Timezone:get_timezones',
				),
				'default'  => $this->_registry->get( 'model.option' )->get(
					'timezone_string'
				),
			),
			'default_tags_categories' => array(
				'type' => 'array',
				'renderer' => array(
					'class' => 'tags-categories',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__( 'Preselected calendar filters' ),
					'help'  => Ai1ec_I18n::__(
						'To clear, hold &#8984;/<abbr class="initialism">CTRL</abbr> and click selection.'
					)
				),
				'default'  => array(
					'categories' => array(),
					'tags' => array(),
				),
			),
			'exact_date' => array(
				'type' => 'string',
				'renderer' => array(
					'class' => 'input',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__( 'Default calendar start date (optional)' ),
					'type'  => 'date',
				),
				'default'  => '',
			),
			'agenda_events_per_page' => array(
				'type' => 'int',
				'renderer' => array(
					'class'     => 'input',
					'tab'       => 'viewing-events',
					'item'      => 'viewing-events',
					'label'     => Ai1ec_I18n::__( 'Agenda pages show at most' ),
					'type'      => 'append',
					'append'    => 'events',
					'validator' => 'numeric',
				),
				'default'  => 10,
			),
			'week_view_starts_at' => array(
				'type' => 'int',
				'renderer' => array(
					'class'     => 'input',
					'tab'       => 'viewing-events',
					'item'      => 'viewing-events',
					'label'     => Ai1ec_I18n::__( 'Week/Day view starts at' ),
					'type'      => 'append',
					'append'    => 'hrs',
					'validator' => 'numeric',
				),
				'default'  => 8,
			),
			'week_view_ends_at' => array(
				'type' => 'int',
				'renderer' => array(
					'class'     => 'input',
					'tab'       => 'viewing-events',
					'item'      => 'viewing-events',
					'label'     => Ai1ec_I18n::__( 'Week/Day view ends at' ),
					'type'      => 'append',
					'append'    => 'hrs',
					'validator' => 'numeric',
				),
				'default'  => 24,
			),
			'month_word_wrap' => array(
				'type'     => 'bool',
				'renderer' => array(
					'class'  => 'checkbox',
					'tab'    => 'viewing-events',
					'item'   => 'viewing-events',
					'label'  => Ai1ec_I18n::__(
						'<strong>Word-wrap event stubs</strong> in Month view'
					),
					'help'  => Ai1ec_I18n::__(
						'Only applies to events that span a single day.'
					),
				),
				'default'  => false,
			),
			'agenda_include_entire_last_day' => array(
				'type'     => 'bool',
				'renderer' => array(
					'class'  => 'checkbox',
					'tab'    => 'viewing-events',
					'item'   => 'viewing-events',
					'label'  => Ai1ec_I18n::__(
						'In <span class="ai1ec-tooltip-toggle"
						data-original-title="These include Agenda view,
						the Upcoming Events widget, and some extended views.">
						Agenda-like views</span>, <strong>include all events
						from last day shown</strong>'
					)
				),
				'default'  => false,
			),
			'agenda_events_expanded' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__(
						'Keep all events <strong>expanded</strong> in Agenda view'
					)
				),
				'default'  => false,
			),
			'show_year_in_agenda_dates' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__(
						'<strong>Show year</strong> in calendar date labels'
					)
				),
				'default'  => false,
			),
			'show_location_in_title' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__(
						'<strong>Show location in event titles</strong> in calendar views'
					)
				),
				'default'  => true,
			),
			'exclude_from_search' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__(
						'<strong>Exclude</strong> events from search results'
					)
				),
				'default'  => false,
			),
			'turn_off_subscription_buttons' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__(
						'Hide <strong>Subscribe</strong>/<strong>Add to Calendar</strong> buttons in calendar and single event views '
					)
				),
				'default'  => false,
			),
			'hide_maps_until_clicked' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__(
						' Hide <strong>Google Maps</strong> until clicked'
					)
				),
				'default'  => false,
			),
			'affix_filter_menu' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__(
						' <strong>Affix filter menu</strong> to top of window when it scrolls out of view'
					),
					'help'  => Ai1ec_I18n::__(
						'Only applies to first visible calendar found on the page.'
					),
				),
				'default'  => false,
			),
			'affix_vertical_offset_md' => array(
				'type' => 'int',
				'renderer' => array(
					'class'     => 'input',
					'tab'       => 'viewing-events',
					'item'      => 'viewing-events',
					'label'     => Ai1ec_I18n::__( 'Offset affixed filter bar vertically by' ),
					'type'      => 'append',
					'append'    => 'pixels',
					'validator' => 'numeric',
				),
				'default'  => 0,
			),
			'affix_vertical_offset_lg' => array(
				'type' => 'int',
				'renderer' => array(
					'class'     => 'input',
					'tab'       => 'viewing-events',
					'item'      => 'viewing-events',
					'label'     =>
						'<i class="ai1ec-fa ai1ec-fa-lg ai1ec-fa-fw ai1ec-fa-desktop"></i> ' .
						Ai1ec_I18n::__( 'Wide screens only (&#8805; 1200px)' ),
					'type'      => 'append',
					'append'    => 'pixels',
					'validator' => 'numeric',
				),
				'default'  => 0,
			),
			'affix_vertical_offset_sm' => array(
				'type' => 'int',
				'renderer' => array(
					'class'     => 'input',
					'tab'       => 'viewing-events',
					'item'      => 'viewing-events',
					'label'     =>
						'<i class="ai1ec-fa ai1ec-fa-lg ai1ec-fa-fw ai1ec-fa-tablet"></i> ' .
						Ai1ec_I18n::__( 'Tablets only (< 980px)' ),
					'type'      => 'append',
					'append'    => 'pixels',
					'validator' => 'numeric',
				),
				'default'  => 0,
			),
			'affix_vertical_offset_xs' => array(
				'type' => 'int',
				'renderer' => array(
					'class'     => 'input',
					'tab'       => 'viewing-events',
					'item'      => 'viewing-events',
					'label'     =>
						'<i class="ai1ec-fa ai1ec-fa-lg ai1ec-fa-fw ai1ec-fa-mobile"></i> ' .
						Ai1ec_I18n::__( 'Phones only (< 768px)' ),
					'type'      => 'append',
					'append'    => 'pixels',
					'validator' => 'numeric',
				),
				'default'  => 0,
			),
			'strict_compatibility_content_filtering' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__(
						'Strict compatibility content filtering'
					),
				),
				'default'  => false,
			),
			'hide_featured_image' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'viewing-events',
					'item'  => 'viewing-events',
					'label' => Ai1ec_I18n::__(
						' <strong>Hide featured image</strong> from event details page'
					),
					'help'  => Ai1ec_I18n::__(
						"Select this option if your theme already displays each post's featured image."
					),
				),
				'default'  => false,
			),
			'input_date_format' => array(
				'type' => 'string',
				'renderer' => array(
					'class'   => 'select',
					'tab'     => 'editing-events',
					'label'   => Ai1ec_I18n::__(
						'Input dates in this format'
					),
					'options' => array(
						array(
							'text' => Ai1ec_I18n::__( 'Default (d/m/yyyy)' ),
							'value' => 'def'
					 	),
						array(
							'text' => Ai1ec_I18n::__( 'US (m/d/yyyy)' ),
							'value' => 'us'
						),
						array(
							'text' => Ai1ec_I18n::__( 'ISO 8601 (yyyy-m-d)' ),
							'value' => 'iso'
						),
						array(
							'text' => Ai1ec_I18n::__( 'Dotted (m.d.yyyy)' ),
							'value' => 'dot'
					 	),
					),
				),
				'default'  => 'def',
			),
			'input_24h_time' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'editing-events',
					'label' => Ai1ec_I18n::__(
						' Use <strong>24h time</strong> in time pickers'
					)
				),
				'default'  => false,
			),
			'disable_autocompletion' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'editing-events',
					'label' => Ai1ec_I18n::__(
						'<strong>Disable address autocomplete</strong> function'
					)
				),
				'default'  => false,
			),
			'geo_region_biasing' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'editing-events',
					'label' => Ai1ec_I18n::__(
						'Use the configured <strong>region</strong> (WordPress locale) to bias the address autocomplete function '
					)
				),
				'default'  => false,
			),
			'show_publish_button' => array(
				'type'     => 'deprecated',
				'renderer' => null,
				'default'  => false,
			),
			'show_create_event_button' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'editing-events',
					'label' => Ai1ec_I18n::__(
						' Show the old <strong>Post Your Event</strong> button above the calendar to privileged users'
					),
					'help'  => Ai1ec_I18n::__(
						'Install the <a target="_blank" href="http://time.ly/">Interactive Frontend Extension</a> for the <strong>frontend Post Your Event form</strong>.'
					),
				),
				'default'  => false,
			),
			'embedding' => array(
				'type' => 'html',
				'renderer' => array(
					'class' => 'html',
					'tab'   => 'advanced',
					'item'  => 'embedded-views',
				),
				'default'  => null,
			),
			'calendar_css_selector' => array(
				'type' => 'string',
				'renderer' => array(
					'class' => 'input',
					'tab'   => 'advanced',
					'item'  => 'advanced',
					'label' => Ai1ec_I18n::__( 'Move calendar into this DOM element' ),
					'type'  => 'normal',
					'help'  => Ai1ec_I18n::__(
						'Optional. Use this JavaScript-based shortcut to place the
						calendar a DOM element other than the usual page content container
						if you are unable to create an appropriate page template
						 for the calendar page. To use, enter a
						<a target="_blank" href="http://api.jquery.com/category/selectors/">
						jQuery selector</a> that evaluates to a single DOM element.
						Any existing markup found within the target will be replaced
						by the calendar.'
					),
				),
				'default'  => '',
			),
			'skip_in_the_loop_check' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'advanced',
					'item'  => 'advanced',
					'label' => Ai1ec_I18n::__(
						'<strong>Skip <tt>in_the_loop()</tt> check </strong> that protects against multiple calendar output'
					),
					'help'  => Ai1ec_I18n::__(
						'Try enabling this option if your calendar does not appear on the calendar page. It is needed for compatibility with a small number of themes that call <tt>the_content()</tt> from outside of The Loop. Leave disabled otherwise.'
					),
				),
				'default'  => false,
			),
			'disable_gzip_compression' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'advanced',
					'item'  => 'advanced',
					'label' => Ai1ec_I18n::__(
						'Disable <strong>gzip</strong> compression.'
					),
					'help'  => Ai1ec_I18n::__(
						'Use this option if calendar is unresponsive. <a href="http://support.time.ly/disable-gzip-compression/">Read more</a> about the issue. (From version 2.1 onwards, gzip is disabled by default for maximum compatibility.)'
					),
				),
				'default'  => true,
			),
			'render_css_as_link' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'advanced',
					'item'  => 'advanced',
					'label' => Ai1ec_I18n::__(
						'<strong>Link CSS</strong> in <code>&lt;head&gt;</code> section when file cache is unavailable.'
					),
					'help'  => Ai1ec_I18n::__(
						'Use this option if file cache is unavailable and you would prefer to serve CSS as a link rather than have it output inline.'
					),
				),
				'default'  => false,
			),
			'edit_robots_txt' => array(
				'type' => 'string',
				'renderer' => array(
					'class'    => 'textarea',
					'tab'      => 'advanced',
					'item'     => 'advanced',
					'label'    => Ai1ec_I18n::__( 'Current <strong>robots.txt</strong> on this site' ),
					'type'     => 'normal',
					'rows'     => 6,
					'readonly' => 'readonly',
					'help'     => Ai1ec_I18n::__(
						'The Robot Exclusion Standard, also known as the Robots Exclusion Protocol or
						<code><a href="http://en.wikipedia.org/wiki/Robots.txt" target="_blank">robots.txt</a></code>
						protocol, is a convention for cooperating web crawlers and other web robots
						about accessing all or part of a website that is otherwise publicly viewable.
						You can change it manually by editing <code>robots.txt</code> in your root WordPress directory.'
					),
				),
				'default'  => '',
			),
			'allow_statistics' => array(
				'type' => 'bool',
				'renderer' => array(
					'class' => 'checkbox',
					'tab'   => 'advanced',
					'item'  => 'advanced',
					'label' => sprintf(
						Ai1ec_I18n::__(
							'<strong>Publicize, promote, and share my events</strong> marked as public on the Timely network. (<a href="%s" target="_blank">Learn more &#187;</a>)'
						),
						'http://time.ly/event-search-calendar'
					),
				),
				'default'  => false,
			),
			'legacy_options' => array(
				'type'     => 'legacy_options',
				'default'  => null,
			),
			'ics_cron_freq' => array(
				'type'    => 'string',
				'default' => 'hourly',
			),
			'twig_cache' => array(
				'type' => 'string',
				'renderer' => array(
					'class' => 'cache',
					'tab'   => 'advanced',
					'item'  => 'cache',
					'label' => sprintf(
						Ai1ec_I18n::__(
							'Templates cache improves site performance'
						)
					),
				),
				'default' => '',
			)
		);
	}

	/**
	 * Register the standard setting values.
	 *
	 * @return void Method doesn't return.
	 */
	protected function _register_standard_values() {
		foreach ( $this->_standard_options as $key => $option ) {
			$renderer = null;
			$value    = $option['default'];
			if ( isset( $option['renderer'] ) ) {
				$renderer = $option['renderer'];
			}
			$this->register(
				$key,
				$value,
				$option['type'],
				$renderer,
				AI1EC_VERSION
			);
		}
	}

	/**
	 * Update translated strings, after introduction of `_noop` functions.
	 *
	 * @return void
	 */
	protected function _update_name_translations() {
		$translations = $this->_standard_options['enabled_views']['default'];
		$current      = $this->get( 'enabled_views' );
		foreach ( $current as $key => $view ) {
			if ( isset( $translations[$key] ) ) {
				$current[$key]['longname'] = $translations[$key]['longname'];
			}
		}
		$this->set( 'enabled_views', $current );
	}

	/**
	 * Change `updated` flag value.
	 *
	 * @param bool $new_status Status to change to.
	 *
	 * @return bool Previous status flag value.
	 */
	protected function _change_update_status( $new_status ) {
		$previous = $this->_updated;
		$this->_updated = (bool)$new_status;
		return $previous;
	}

}