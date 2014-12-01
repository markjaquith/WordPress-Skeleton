<?php

/**
 * The concrete class for the calendar page.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View
 */
class Ai1ec_Calendar_Page extends Ai1ec_Base {

	/**
	 * @var Ai1ec_Memory_Utility Instance of memory to hold exact dates
	 */
	protected $_exact_dates = NULL;


	/**
	 * Public constructor
	 *
	 * @param Ai1ec_Registry_Object $registry The registry object
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		$this->_exact_dates = $registry->get( 'cache.memory' );
	}

	/**
	 * Get the content if the calendar page
	 *
	 * @param Ai1ec_Request_Parser $request
	 */
	public function get_content( Ai1ec_Request_Parser $request ) {


		// Get args for the current view; required to generate HTML for views
		// dropdown list, categories, tags, subscribe buttons, and of course the
		// view itself.
		$view_args  = $this->get_view_args_for_view( $request );

		try {
			$action   = $this->_registry->get( 'model.settings-view' )
				->get_configured( $view_args['action'] );
		} catch ( Ai1ec_Settings_Exception $exception ) {
			// short-circuit and return error message
			return '<div id="ai1ec-container"><div class="timely"><p>' .
				Ai1ec_I18n::__(
					'There was an error loading calendar. Please contact site administrator and inform him to configure calendar views.'
				) .
				'</p></div></div>';
		}
		$type       = $request->get( 'request_type' );

		// Add view-specific args to the current view args.
		$exact_date = $this->get_exact_date( $request );
		try {
			$view_obj = $this->_registry->get(
				'view.calendar.view.' . $action,
				$request
			);
		} catch ( Ai1ec_Bootstrap_Exception $exc ) {
			$this->_registry->get( 'notification.admin' )->store(
				sprintf(
						Ai1ec_I18n::__( 'Calendar was unable to initialize %s view and has reverted to Agenda view. Please check if you have installed the latest versions of calendar add-ons.' ),
						ucfirst( $action )
				),
				'error',
				0,
				array( Ai1ec_Notification_Admin::RCPT_ADMIN ),
				true
			);
			// don't disable calendar - just switch to agenda which should
			// always exists
			$action   = 'agenda';
			$view_obj = $this->_registry->get(
				'view.calendar.view.' . $action,
				$request
			);
		}
		$view_args  = $view_obj->get_extra_arguments( $view_args, $exact_date );

		// Get HTML for views dropdown list.
		$dropdown_args = $view_args;
		if (
			isset( $dropdown_args['time_limit'] ) &&
			false !== $exact_date
		) {
			$dropdown_args['exact_date'] = $exact_date;
		}
		$views_dropdown =
			$this->get_html_for_views_dropdown( $dropdown_args, $view_obj );
		// Add views dropdown markup to view args.
		$view_args['views_dropdown'] = $views_dropdown;

		// Get HTML for categories and for tags
		$taxonomy   = $this->_registry->get( 'view.calendar.taxonomy' );
		$categories = $taxonomy->get_html_for_categories(
			$view_args
		);
		$tags       = $taxonomy->get_html_for_tags(
			$view_args,
			true
		);

		// Get HTML for subscribe buttons.
		$subscribe_buttons =
			$this->get_html_for_subscribe_buttons( $view_args );
		// Get HTML for view itself.
		$view       = $view_obj->get_content( $view_args );

		$router = $this->_registry->get( 'routing.router' );
		$are_filters_set = $router->is_at_least_one_filter_set_in_request(
			$view_args
		);

		if (
			( $view_args['no_navigation'] || $type !== 'html' ) &&
			'jsonp' !== $type
		) {

			// send data both for json and jsonp as shortcodes are jsonp
			return array(
				'html'               => $view,
				'categories'         => $categories,
				'tags'               => $tags,
				'views_dropdown'     => $views_dropdown,
				'subscribe_buttons'  => $subscribe_buttons,
				'are_filters_set'    => $are_filters_set,
				'custom_filters'     => apply_filters(
					'ai1ec_custom_filters_html',
					'',
					$view_args,
					$request
				),
			);

		} else {
			$loader = $this->_registry->get( 'theme.loader' );
			$empty  = $loader->get_file( 'empty.twig', array(), false );

			// option to show filters in the super widget
			// Define new arguments for overall calendar view
			$filter_args = array(
				'views_dropdown'               => $views_dropdown,
				'categories'                   => $categories,
				'tags'                         => $tags,
				'contribution_buttons'         => apply_filters(
					'ai1ec_contribution_buttons',
					'',
					$type
				),
				'show_dropdowns'               => apply_filters(
					'ai1ec_show_dropdowns',
					true
				),
				'show_select2'                 => apply_filters(
					'ai1ec_show_select2',
					false
				),
				'span_for_select2'             => apply_filters(
					'ai1ec_span_for_select2',
					''
				),
				'authors'                      => apply_filters(
					'ai1ec_authors',
					''
				),
				'save_view_btngroup'           => apply_filters(
					'ai1ec_save_view_btngroup',
					$empty
				),
				'view_args'                    => $view_args,
				'request'                      => $request,
			);

			$filter_menu   = $loader->get_file(
				'filter-menu.twig',
				$filter_args,
				false
			)->get_content();
			// hide filters in the SW
			if ( 'true' !== $request->get( 'display_filters' ) && 'jsonp' === $type ) {
				$filter_menu = '';
			}

			$calendar_args = array(
				'version'                      => AI1EC_VERSION,
				'filter_menu'                  => $filter_menu,
				'view'                         => $view,
				'subscribe_buttons'            => $subscribe_buttons,
				'disable_standard_filter_menu' => apply_filters(
					'ai1ec_disable_standard_filter_menu',
					false
				),
			);

			$calendar = $loader->get_file( 'calendar.twig', $calendar_args, false );
			// if it's just html, only the calendar html must be returned.
			if ( 'html' === $type ) {
				return $calendar->get_content();
			}
			// send data both for json and jsonp as shortcodes are jsonp
			return array(
				'html'               => $calendar->get_content(),
				'categories'         => $categories,
				'tags'               => $tags,
				'views_dropdown'     => $views_dropdown,
				'subscribe_buttons'  => $subscribe_buttons,
				'are_filters_set'    => $are_filters_set,
			);
		}
	}

	/**
	 * Render the HTML for the `subscribe' buttons.
	 *
	 * @param array $view_args Args to pass.
	 *
	 * @return string Rendered HTML to include in output.
	 */
	public function get_html_for_subscribe_buttons( array $view_args ) {
		$turn_off_subscribe = $this->_registry->get( 'model.settings' )
			->get( 'turn_off_subscription_buttons' );
		if ( $turn_off_subscribe ) {
			return '';
		}
		$args = array(
			'url_args'                => '',
			'is_filtered'             => false,
			'export_url'              => AI1EC_EXPORT_URL,
			'export_url_no_html'      => AI1EC_EXPORT_URL . '&no_html=true',
			'text_filtered'           => __( 'Subscribe to filtered calendar', AI1EC_PLUGIN_NAME ),
			'text_subscribe'          => __( 'Subscribe', AI1EC_PLUGIN_NAME ),
			'text'                    => $this->_registry
				->get( 'view.calendar.subscribe-button' )
				->get_labels(),
		);
		if ( ! empty( $view_args['cat_ids'] ) ) {
			$args['url_args'] .= '&ai1ec_cat_ids=' .
				implode( ',', $view_args['cat_ids'] );
			$args['is_filtered'] = true;
		}
		if ( ! empty( $view_args['tag_ids'] ) ) {
			$args['url_args']  .= '&ai1ec_tag_ids=' .
				implode( ',', $view_args['tag_ids'] );
			$args['is_filtered'] = true;
		}
		if ( ! empty( $view_args['post_ids'] ) ) {
			$args['url_args']  .= '&ai1ec_post_ids=' .
				implode( ',', $view_args['post_ids'] );
			$args['is_filtered'] = true;
		}
		$args = apply_filters(
			'ai1ec_subscribe_buttons_arguments',
			$args,
			$view_args
		);
		$localization = $this->_registry->get( 'p28n.wpml' );
		if (
			NULL !== ( $use_lang = $localization->get_language() )
		) {
			$args['url_args'] .= '&lang=' . $use_lang;
		}
		$subscribe = $this->_registry->get( 'theme.loader' )
			->get_file( 'subscribe-buttons.twig', $args, false );
		return $subscribe->get_content();
	}

	/**
	 * This function generates the html for the view dropdowns.
	 *
	 * @param array                        $view_args Args passed to view
	 * @param Ai1ec_Calendar_View_Abstract $view      View object
	 */
	protected function get_html_for_views_dropdown(
		array $view_args,
		Ai1ec_Calendar_View_Abstract $view
	) {
		$settings        = $this->_registry->get( 'model.settings' );
		$available_views = array();
		$enabled_views   = (array)$settings->get( 'enabled_views', array() );
		$view_names      = array();
		$mode            = wp_is_mobile() ? '_mobile' : '';
		foreach ( $enabled_views as $key => $val ) {
			$view_names[$key] = translate_nooped_plural(
				$val['longname'],
				1
			);
			// Find out if view is enabled in requested mode (mobile or desktop). If
			// no mode-specific setting is available, fall back to desktop setting.
			$view_enabled = isset( $enabled_views[$key]['enabled' . $mode] ) ?
				$enabled_views[$key]['enabled' . $mode] :
				$enabled_views[$key]['enabled'];
			$values = array();
			$options = $view_args;
			if ( $view_enabled ) {
				if ( $view instanceof Ai1ec_Calendar_View_Agenda ) {
					if (
						isset( $options['exact_date'] ) &&
						! isset( $options['time_limit'] )
					) {
						$options['time_limit'] = $options['exact_date'];
					}
					unset( $options['exact_date'] );
				} else {
					unset( $options['time_limit'] );
				}
				unset( $options['month_offset'] );
				unset( $options['week_offset'] );
				unset( $options['oneday_offset'] );
				$options['action'] = $key;
				$values['desc'] = translate_nooped_plural(
					$val['longname'],
					1
				);
				if ( AI1EC_USE_FRONTEND_RENDERING ) {
					$options['request_format'] = 'json';
				}
				$href = $this->_registry->get( 'html.element.href', $options );
				$values['href'] = $href->generate_href();
				$available_views[$key] = $values;
			}
		};
		$args = array(
			'view_names'              => $view_names,
			'available_views'         => $available_views,
			'current_view'            => $view_args['action'],
			'data_type'               => $view_args['data_type'],
		);

		$views_dropdown = $this->_registry->get( 'theme.loader' )
			->get_file( 'views_dropdown.twig', $args, false );
		return $views_dropdown->get_content();
	}

	/**
	 * Get the exact date from request if available, or else from settings.
	 *
	 * @param Ai1ec_Abstract_Query settings
	 *
	 * @return boolean|int
	 */
	private function get_exact_date( Ai1ec_Abstract_Query $request ) {
		$settings = $this->_registry->get( 'model.settings' );

		// Preprocess exact_date.
		// Check to see if a date has been specified.
		$exact_date = $request->get( 'exact_date' );
		$use_key    = $exact_date;
		if ( null === ( $exact_date = $this->_exact_dates->get( $use_key ) ) ) {
			$exact_date = $use_key;
			// Let's check if we have a date
			if ( false !== $exact_date ) {
				// If it's not a timestamp
				if ( ! Ai1ec_Validation_Utility::is_valid_time_stamp( $exact_date ) ) {
					// Try to parse it
					$exact_date = $this->return_gmtime_from_exact_date( $exact_date );
					if ( false === $exact_date ) {
						return null;
					}
				}
			}
			// Last try, let's see if an exact date is set in settings.
			if ( false === $exact_date && $settings->get( 'exact_date' ) !== '' ) {
				$exact_date = $this->return_gmtime_from_exact_date(
					$settings->get( 'exact_date' )
				);
			}
			$this->_exact_dates->set( $use_key, $exact_date );
		}
		return $exact_date;
	}

	/**
	 * Decomposes an 'exact_date' parameter into month, day, year components based
	 * on date pattern defined in settings (assumed to be in local time zone),
	 * then returns a timestamp in GMT.
	 *
	 * @param  string     $exact_date 'exact_date' parameter passed to a view
	 * @return bool|int               false if argument not provided or invalid,
	 *                                else UNIX timestamp in GMT
	 */
	private function return_gmtime_from_exact_date( $exact_date ) {
		$input_format = $this->_registry->get( 'model.settings' )
			->get( 'input_date_format' );

		$date = Ai1ec_Validation_Utility::format_as_iso(
			$exact_date,
			$input_format
		);
		if ( false === $date ) {
			$exact_date = false;
		} else {
			$exact_date = $this->_registry->get(
				'date.time',
				$date,
				'sys.default'
			)->format_to_gmt();
			if ( $exact_date < 0 ) {
				return false;
			}
		}
		return $exact_date;
	}

	/**
	 * Returns the correct data attribute to use in views
	 *
	 * @param string $type
	 */
	private function return_data_type_for_request_type( $type ) {
		$data_type = 'data-type="json"';
		if ( $type === 'jsonp' ) {
			$data_type = 'data-type="jsonp"';
		}
		return $data_type;
	}

	/**
	 * Get the parameters for the view from the request object
	 *
	 * @param Ai1ec_Abstract_Query $request
	 *
	 * @return array
	 */
	protected function get_view_args_for_view( Ai1ec_Abstract_Query $request ) {
		$settings = $this->_registry->get( 'model.settings' );
		// Define arguments for specific calendar sub-view (month, agenda, etc.)
		// Preprocess action.
		// Allow action w/ or w/o ai1ec_ prefix. Remove ai1ec_ if provided.
		$action = $request->get( 'action' );

		if ( 0 === strncmp( $action, 'ai1ec_', 6 ) ) {
			$action = substr( $action, 6 );
		}
		$view_args = $request->get_dict(
			apply_filters(
				'ai1ec_view_args_for_view',
				array(
					'post_ids',
					'auth_ids',
					'cat_ids',
					'tag_ids',
					'events_limit',
				)
			)
		);
		$add_defaults = array(
			'cat_ids' => 'categories',
			'tag_ids' => 'tags',
		);
		foreach ( $add_defaults as $query => $default ) {
			if ( empty( $view_args[$query] ) ) {
				$setting = $settings->get( 'default_tags_categories' );
				if ( isset( $setting[$default] ) ) {
					$view_args[$query] = $setting[$default];
				}
			}
		}

		$type = $request->get( 'request_type' );

		$view_args['data_type'] = $this->return_data_type_for_request_type(
			$type
		);

		$view_args['request_format'] = $request->get( 'request_format' );
		$exact_date = $this->get_exact_date( $request );

		$view_args['no_navigation'] = $request
			->get( 'no_navigation' ) === 'true';

		// Find out which view of the calendar page was requested, and render it
		// accordingly.
		$view_args['action'] = $action;

		$view_args['request'] = $request;
		$view_args            = apply_filters(
			'ai1ec_view_args_array',
			$view_args
		);
		if ( null === $exact_date ) {
			$href = $this->_registry->get( 'html.element.href', $view_args )
				->generate_href();
			return Ai1ec_Http_Response_Helper::redirect( $href, 307 );

		}
		return $view_args;
	}
}
