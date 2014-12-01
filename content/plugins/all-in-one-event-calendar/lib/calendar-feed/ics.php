<?php

/**
 * The class which handles ics feeds tab.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Calendar-feed
 */
class Ai1ecIcsConnectorPlugin extends Ai1ec_Connector_Plugin {

	/**
	 * @var string Name of cron hook.
	 */
	const HOOK_NAME = 'ai1ec_cron';

	const ICS_OPTION_DB_VERSION = 'ai1ec_ics_db_version';

	const ICS_DB_VERSION        = 107;

	/**
	 * @var array
	 *   title: The title of the tab and the title of the configuration section
	 *   id: The id used in the generation of the tab
	 */
	protected $variables = array(
		'id' => 'ics',
	);

	/**
	 * @var Ai1ec_Compatibility_Xguard Instance of execution guard.
	 */
	protected $_xguard   = null;

	public function get_tab_title() {
		return Ai1ec_I18n::__( 'ICS' );
	}

	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		// Handle schema changes.
		$this->_install_schema();
		// Install the CRON
		$this->install_cron();
		$this->_xguard = $registry->get( 'compatibility.xguard' );
	}

	/**
	 * update_ics_feed function
	 *
	 * Imports the selected iCalendar feed
	 *
	 * @return void
	 */
	public function update_ics_feed( $feed_id = false ) {
		$ajax = false;
		// if no feed is provided, we are using ajax
		if ( ! $feed_id ) {
			$ajax    = true;
			$feed_id = (int) $_REQUEST['ics_id'];
		}
		$cron_name = $this->_import_lock_name( $feed_id );
		$output    = array(
			'data' => array(
				'ics_id'  => $feed_id,
				'error'   => true,
				'message' => Ai1ec_I18n::__(
					'Another import process in progress. Please try again later.'
				),
			),
		);
		// hold import lock for 8 minutes
		if ( $this->_xguard->acquire( $cron_name, 480 ) ) {
			$output = $this->process_ics_feed_update( $ajax, $feed_id );
		}
		$this->_xguard->release( $cron_name );
		if ( true === $ajax ) {
			$render_json = $this->_registry->get(
				'http.response.render.strategy.json'
			);
			return $render_json->render( $output );
		}
		return $output;
	}

	/**
	 * Perform actual feed refresh.
	 *
	 * @param bool $ajax    True when handling AJAX feed.
	 * @param int  $feed_id ID of feed to process.
	 *
	 * @return array Output to return to user.
	 */
	public function process_ics_feed_update( $ajax, $feed_id ) {
		$db         = $this->_registry->get( 'dbi.dbi' );
		$table_name = $db->get_table_name( 'ai1ec_event_feeds' );
		$feed       = $db->get_row(
			$db->prepare(
				'SELECT * FROM ' . $table_name . ' WHERE feed_id = %d', $feed_id
			)
		);
		$output = array();
		if ( $feed ) {

			$count    = 0;
			$message  = false;
			// reimport the feed
			$response = wp_remote_get(
				$feed->feed_url,
				array( 'sslverify' => false, 'timeout' => 120 )
			);

			if (
				! is_wp_error( $response )             &&
				isset( $response['response'] )         &&
				isset( $response['response']['code'] ) &&
				$response['response']['code'] == 200   &&
				isset( $response['body'] )             &&
				! empty( $response['body'] )
			) {
				try {

					$import_export = $this->_registry->get(
						'controller.import-export'
					);

					$search       = $this->_registry->get( 'model.search' );
					$events_in_db = $search->get_event_ids_for_feed( $feed->feed_url );
					// flip the array. We will use keys to check events which are imported.
					$events_in_db = array_flip( $events_in_db );
					$args         = array();
					$args['events_in_db'] = $events_in_db;
					$args['feed'] = $feed;

					$args['comment_status'] = 'open';
					if (
						isset( $feed->comments_enabled ) &&
						$feed->comments_enabled < 1
					) {
						$args['comment_status'] = 'closed';
					}

					$args['do_show_map'] = 0;
					if (
						isset( $feed->map_display_enabled ) &&
						$feed->map_display_enabled > 0
					) {
						$args['do_show_map'] = 1;
					}
					$args['source'] = $response['body'];
					do_action( 'ai1ec_ics_before_import', $args );
					$result = $import_export->import_events( 'ics', $args );
					do_action( 'ai1ec_ics_after_import' );
					$count  = $result['count'];
					// we must flip again the array to iterate over it
					if ( 0 == $feed->keep_old_events ) {
						$events_to_delete = array_flip( $result['events_to_delete'] );
						foreach ( $events_to_delete as $event_id ) {
							wp_delete_post( $event_id, true );
						}
					}
				} catch ( Ai1ec_Parse_Exception $e ) {
					$message = "The provided feed didn't return valid ics data";
				} catch ( Ai1ec_Engine_Not_Set_Exception $e ) {
					$message = "ICS import is not supported on this install.";
				} catch ( Ai1ec_Event_Create_Exception $e ) {
					$message = $e->getMessage();
				}
			} else if ( is_wp_error( $response ) ) {
				$message = sprintf(
					__(
						'A system error has prevented calendar data from being fetched. Something is preventing the plugin from functioning correctly. This message should provide a clue: %s',
						AI1EC_PLUGIN_NAME
					),
					$response->get_error_message()
				);
			} else {
				$message = __(
					"Calendar data could not be fetched. If your URL is valid and contains an iCalendar resource, this is likely the result of a temporary server error and time may resolve this issue",
					AI1EC_PLUGIN_NAME
				);
			}


			if ( $count == 0 ) {
				// If results are 0, it could be result of a bad URL or other error, send a specific message
				$return_message = false === $message ?
					__( 'No events were found', AI1EC_PLUGIN_NAME ) :
					$message;
				$output['data'] = array(
						'error'   => true,
						'message' => $return_message
				);
			} else {
				$output['data'] = array(
						'error'       => false,
						'message'     => sprintf( _n( 'Imported %s event', 'Imported %s events', $count, AI1EC_PLUGIN_NAME ), $count ),
				);
			}
		} else {
			$output['data'] = array(
					'error' 	=> true,
					'message'	=> __( 'Invalid ICS feed ID', AI1EC_PLUGIN_NAME )
			);
		}
		$output['data']['ics_id'] = $feed_id;
		return $output;
	}

	/**
	 * Returns the translations array
	 *
	 * @return array
	 */
	private function get_translations() {
		$categories = isset( $_POST['ai1ec_categories'] ) ? $_POST['ai1ec_categories'] : array();
		foreach ( $categories as &$cat ) {
			$term = get_term( $cat, 'events_categories' );
			$cat = $term->name;
		}
		$translations = array(
			'[feed_url]'   => $_POST['ai1ec_calendar_url'],
			'[categories]' => implode( ', ' , $categories ),
			'[user_email]' => $_POST['ai1ec_submitter_email'],
			'[site_title]' => get_bloginfo( 'name' ),
			'[site_url]'   => site_url(),
			'[feeds_url]'  => admin_url( AI1EC_FEED_SETTINGS_BASE_URL . '#ics' ),
		);
		return $translations;
	}

	/**
	 * This function sets up the cron job for updating the events, and upgrades it if it is out of date.
	 *
	 * @return void
	 */
	private function install_cron() {
		$this->_registry->get( 'scheduling.utility' )->reschedule(
			self::HOOK_NAME,
			$this->_registry->get( 'model.settings' )->get( 'ics_cron_freq' ),
			AI1EC_CRON_VERSION
		);
	}

	/**
	 * Handles all the required steps to install / update the schema
	 */
	protected function _install_schema() {
		// If existing DB version is not consistent with current plugin's
		// version,
		// or does not exist, then create/update table structure using
		// dbDelta().
		$option = $this->_registry->get( 'model.option' );
		if ( $option->get( self::ICS_OPTION_DB_VERSION ) !=
			 self::ICS_DB_VERSION ) {
			$db = $this->_registry->get( 'dbi.dbi' );
			// ======================
			// = Create table feeds =
			// ======================
			$table_name = $db->get_table_name( 'ai1ec_event_feeds' );
			$sql = "CREATE TABLE $table_name (
					feed_id bigint(20) NOT NULL AUTO_INCREMENT,
					feed_url varchar(255) NOT NULL,
					feed_category varchar(255) NOT NULL,
					feed_tags varchar(255) NOT NULL,
					comments_enabled tinyint(1) NOT NULL DEFAULT '1',
					map_display_enabled tinyint(1) NOT NULL DEFAULT '0',
					keep_tags_categories tinyint(1) NOT NULL DEFAULT '0',
					keep_old_events tinyint(1) NOT NULL DEFAULT '0',
					PRIMARY KEY  (feed_id),
					UNIQUE KEY feed (feed_url)
					) CHARACTER SET utf8;";

			if ( $this->_registry->get( 'database.helper' )->apply_delta( $sql ) ) {
				$option->set( self::ICS_OPTION_DB_VERSION,
					self::ICS_DB_VERSION );
			} else {
				trigger_error( 'Failed to upgrade ICS DB schema',
					E_USER_WARNING );
			}
		}
	}

	/**
	 * Cron callback.
	 *
	 * (Re-)Import all ICS feeds.
	 *
	 * @wp_hook ai1ec_cron
	 *
	 * @return void
	 */
	public function cron() {
		$db = $this->_registry->get( 'dbi.dbi' );
		// Initializing custom post type and custom taxonomies
		$post_type = $this->_registry->get( 'post.custom-type' );
		$post_type->register();

		// =======================
		// = Select all feed IDs =
		// =======================
		$sql        = 'SELECT `feed_id` FROM ' .
			$db->get_table_name( 'ai1ec_event_feeds' );
		$feeds      = $db->get_col( $sql );

		// ===============================
		// = go over each iCalendar feed =
		// ===============================
		foreach ( $feeds as $feed_id ) {
			// update the feed
			$this->update_ics_feed( $feed_id );
		}
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see Ai1ec_Connector_Plugin::handle_feeds_page_post()
	 */
	public function handle_feeds_page_post() {
		$settings = $this->_registry->get( 'model.settings' );
		if ( isset( $_POST['ai1ec_save_settings'] ) ) {
			$settings->set( 'ics_cron_freq', $_REQUEST['cron_freq'] );
		}
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see Ai1ec_Connector_Plugin::render_tab_content()
	 */
	public function render_tab_content() {
		// Render the opening div
		$this->render_opening_div_of_tab();
		// Render the body of the tab
		$settings = $this->_registry->get( 'model.settings' );

		$factory = $this->_registry->get(
			'factory.html'
		);
		$select2_cats = $factory->create_select2_multiselect(
			array(
				'name' => 'ai1ec_feed_category[]',
				'id' => 'ai1ec_feed_category',
				'use_id' => true,
				'type' => 'category',
				'placeholder' => __(
					'Categories (optional)',
					AI1EC_PLUGIN_NAME
				)
			),
			get_terms(
				'events_categories',
				array(
					'hide_empty' => false
				)
			)
		);
		$select2_tags = $factory->create_select2_input(
			array( 'id' => 'ai1ec_feed_tags')
		);
		$modal = $this->_registry->get(
			'html.element.legacy.bootstrap.modal',
			esc_html__(
				"Do you want to keep the events imported from the calendar or remove them?",
				AI1EC_PLUGIN_NAME
			)
		);
		$modal->set_header_text(
			esc_html__( 'Removing ICS Feed', AI1EC_PLUGIN_NAME )
		);
		$modal->set_keep_button_text(
			esc_html__( 'Keep Events', AI1EC_PLUGIN_NAME )
		);
		$modal->set_delete_button_text(
			esc_html__( 'Remove Events', AI1EC_PLUGIN_NAME )
		);
		$modal->set_id( 'ai1ec-ics-modal' );
		$loader    = $this->_registry->get( 'theme.loader' );
		$cron_freq = $loader->get_file(
			'cron_freq.php',
			array( 'cron_freq' => $settings->get( 'ics_cron_freq' ) ),
			true
		);
		$args = array(
			'cron_freq'        => $cron_freq->get_content(),
			'event_categories' => $select2_cats,
			'event_tags'       => $select2_tags,
			'feed_rows'        => $this->_get_feed_rows(),
			'modal'            => $modal,
		);

		$display_feeds = $loader->get_file(
			'plugins/ics/display_feeds.php',
			$args,
			true
		);
		$display_feeds->render();
		$this->render_closing_div_of_tab();
	}

	/**
	 * get_feed_rows function
	 *
	 * Creates feed rows to display on settings page
	 *
	 * @return String feed rows
	 **/
	protected function _get_feed_rows() {
		// Select all added feeds
		$rows = $this->_registry->get( 'dbi.dbi' )->select(
			'ai1ec_event_feeds',
			array(
				'feed_id',
				'feed_url',
				'feed_category',
				'feed_tags',
				'comments_enabled',
				'map_display_enabled',
				'keep_tags_categories',
				'keep_old_events',
			)
		);

		$html         = '';
		$theme_loader = $this->_registry->get( 'theme.loader' );

		foreach ( $rows as $row ) {
			$feed_categories = explode( ',', $row->feed_category );
			$categories      = array();

			foreach ( $feed_categories as $cat_id ) {
				$feed_category = get_term(
					$cat_id,
					'events_categories'
				);
				if ( $feed_category && ! is_wp_error( $feed_category ) ) {
					$categories[] = $feed_category->name;
				}
			}
			unset( $feed_categories );

			$args          = array(
				'feed_url'            => $row->feed_url,
				'event_category'      => implode( ', ', $categories ),
				'tags'                => stripslashes(
					str_replace( ',', ', ', $row->feed_tags )
				),
				'feed_id'             => $row->feed_id,
				'comments_enabled'    => (bool) intval(
						$row->comments_enabled
				),
				'map_display_enabled' => (bool) intval(
						$row->map_display_enabled
				),
				'keep_tags_categories' => (bool) intval(
						$row->keep_tags_categories
				),
				'keep_old_events'      => (bool) intval(
						$row->keep_old_events
				),
			);
			$html .= $theme_loader->get_file( 'feed_row.php', $args, true )
				->get_content();
		}


		return $html;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see Ai1ec_Connector_Plugin::display_admin_notices()
	 */
	public function display_admin_notices() {
		return;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see Ai1ec_Connector_Plugin::run_uninstall_procedures()
	 */
	public function run_uninstall_procedures() {
		// Delete tables
		$dbi        = $this->_registry->get( 'dbi.dbi' );
		$table_name = $dbi->get_table_name( 'ai1ec_event_feeds' );
		$dbi->query( 'DROP TABLE IF EXISTS ' . $table_name );
		// Delete scheduled tasks
		$this->_registry->get( 'scheduling.utility' )
			->delete( self::HOOK_NAME );
		// Delete options
		delete_option( self::ICS_DB_VERSION );
		delete_option( self::ICS_OPTION_DB_VERSION );
	}

	/**
	 * add_ics_feed function
	 *
	 * Adds submitted ics feed to the database
	 *
	 * @return string JSON output
	 *
	 */
	public function add_ics_feed() {
		check_ajax_referer( 'ai1ec_ics_feed_nonce', 'nonce' );
		$db = $this->_registry->get( 'dbi.dbi' );
		$table_name = $db->get_table_name( 'ai1ec_event_feeds' );

		$feed_categories = empty( $_REQUEST['feed_category'] ) ? '' : implode(
			',', $_REQUEST['feed_category'] );
		$entry = array( 'feed_url' => $_REQUEST['feed_url'],
			'feed_category'        => $feed_categories,
			'feed_tags'            => $_REQUEST['feed_tags'],
			'comments_enabled'     => Ai1ec_Primitive_Int::db_bool(
				$_REQUEST['comments_enabled'] ),
			'map_display_enabled'  => Ai1ec_Primitive_Int::db_bool(
				$_REQUEST['map_display_enabled'] ),
			'keep_tags_categories' => Ai1ec_Primitive_Int::db_bool(
				$_REQUEST['keep_tags_categories'] ),
			'keep_old_events' => Ai1ec_Primitive_Int::db_bool(
				$_REQUEST['keep_old_events'] )
		);
		$entry = apply_filters( 'ai1ec_ics_feed_entry', $entry );
		if ( is_wp_error( $entry ) ) {
			$output = array(
				'error'   => true,
				'message' => $entry->get_error_message()
			);
			$json_strategy = $this->_registry->get(
				'http.response.render.strategy.json'
			);
			return $json_strategy->render( array( 'data' => $output ) );
		}

		$format     = array( '%s', '%s', '%s', '%d', '%d', '%d', '%d' );
		$res        = $db->insert( $table_name, $entry, $format );
		$feed_id    = $db->get_insert_id();
		$categories = array();

		if ( ! empty( $_REQUEST['feed_category'] ) ) {
			foreach ( $_REQUEST['feed_category'] as $cat_id ) {
				$feed_category = get_term( $cat_id, 'events_categories' );
				$categories[]  = $feed_category->name;
			}
		}

		$args = array(
			'feed_url'             => $_REQUEST['feed_url'],
			'event_category'       => implode( ', ', $categories ),
			'tags'                 => str_replace(
				',',
				', ',
				$_REQUEST['feed_tags']
			),
			'feed_id'              => $feed_id,
			'comments_enabled'     => (bool) intval(
				$_REQUEST['comments_enabled']
			),
			'map_display_enabled'  => (bool) intval(
				$_REQUEST['map_display_enabled']
			),
			'events'               => 0,
			'keep_tags_categories' => (bool) intval(
				$_REQUEST['keep_tags_categories']
			),
			'keep_old_events' => (bool) intval(
				$_REQUEST['keep_old_events']
			)
		);
		$loader = $this->_registry->get( 'theme.loader' );
		// display added feed row
		$file = $loader->get_file( 'feed_row.php', $args, true );

		$output = $file->get_content();

		$output = array(
			'error' => false,
			'message' => stripslashes( $output )
		);
		$json_strategy = $this->_registry->get(
			'http.response.render.strategy.json'
		);
		return $json_strategy->render( array( 'data' => $output ) );
	}

	/**
	 * Delete feeds and events
	 */
	public function delete_feeds_and_events() {
		$remove_events = $_POST['remove_events'] === 'true' ? true : false;
		$ics_id = isset( $_POST['ics_id'] ) ? (int) $_REQUEST['ics_id'] : 0;
		if ( $remove_events ) {
			$output = $this->flush_ics_feed( true, false );
			if ( $output['error'] === false ) {
				$this->delete_ics_feed( false, $ics_id );
			}
			$json_strategy = $this->_registry->get(
				'http.response.render.strategy.json'
			);
			return $json_strategy->render( array( 'data' => $output ) );
		} else {
			$this->delete_ics_feed( true, $ics_id );
		}
		exit();
	}

	/**
	 * Deletes all event posts that are from that selected feed
	 *
	 * @param bool        $ajax     When true data is output using json_response
	 * @param bool|string $feed_url Feed URL
	 *
	 * @return void
	 */
	public function flush_ics_feed( $ajax = true, $feed_url = false ) {
		$db         = $this->_registry->get( 'dbi.dbi' );
		$ics_id     = 0;
		if ( isset( $_REQUEST['ics_id'] ) ) {
			$ics_id = (int) $_REQUEST['ics_id'];
		}
		$table_name = $db->get_table_name( 'ai1ec_event_feeds' );
		if ( false === $feed_url ) {
			$feed_url = $db->get_var(
				$db->prepare(
					'SELECT feed_url FROM ' . $table_name .
						' WHERE feed_id = %d',
					$ics_id
				)
			);
		}
		if ( $feed_url ) {
			$table_name = $db->get_table_name( 'ai1ec_events' );
			$sql        = 'SELECT `post_id` FROM ' . $table_name .
				' WHERE `ical_feed_url` = %s';
			$events     = $db->get_col( $db->prepare( $sql, $feed_url ) );
			$total      = count( $events );
			foreach ( $events as $event_id ) {
				// delete post (this will trigger deletion of cached events, and
				// remove the event from events table)
				wp_delete_post( $event_id, true );
			}
			$output = array(
				'error'   => false,
				'message' => sprintf(
					Ai1ec_I18n::__( 'Deleted %d events' ),
					$total
				),
				'count'   => $total,
			);
		} else {
			$output = array(
				'error'   => true,
				'message' => Ai1ec_I18n::__( 'Invalid ICS feed ID' ),
			);
		}
		if ( $ajax ) {
			$output['ics_id'] = $ics_id;
			return $output;
		}
	}
	/**
	 * delete_ics_feed function
	 *
	 * Deletes submitted ics feed id from the database
	 *
	 * @param bool $ajax When set to TRUE, the data is outputted using json_response
	 * @param bool|string $ics_id Feed URL
	 *
	 * @return String JSON output
	 **/
	public function delete_ics_feed( $ajax = TRUE, $ics_id = FALSE ) {
		$db = $this->_registry->get( 'dbi.dbi' );
		if ( $ics_id === FALSE ) {
			$ics_id = (int) $_REQUEST['ics_id'];
		}
		$table_name = $db->get_table_name( 'ai1ec_event_feeds' );
		$db->query( $db->prepare( "DELETE FROM {$table_name} WHERE feed_id = %d", $ics_id ) );
		$output = array(
			'error'   => false,
			'message' => __( 'Feed deleted', AI1EC_PLUGIN_NAME ),
			'ics_id'  => $ics_id,
		);
		if ( $ajax ) {
			$json_strategy = $this->_registry->get(
				'http.response.render.strategy.json'
			);
			return $json_strategy->render( array( 'data' => $output ) );
		}
	}

	/**
	 * Get name to use for import locking via xguard.
	 *
	 * @param int $feed_id ID of feed being imported.
	 *
	 * @return string Name to use in xguard.
	 */
	protected function _import_lock_name( $feed_id ) {
		return 'ics_import_' . (int)$feed_id;
	}

}
