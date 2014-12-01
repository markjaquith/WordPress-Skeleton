<?php
/****** SETTINGS PAGE ******/
if ( !class_exists( 'EventOrganiser_Admin_Page' ) ){
    require_once(EVENT_ORGANISER_DIR.'classes/class-eventorganiser-admin-page.php' );
}
/**
 * @ignore
 */
class EventOrganiser_Settings_Page extends EventOrganiser_Admin_Page{

	static $eventorganiser_roles;

	/**
	 * Initialises the tabs.
	 */
	function setup_tabs(){
		
		/**
		 * Filters an array of tabs that appear in *Settings > Event Organiser*
		 *
		 * Allows extensions to add additional tabs. The array is indexed by a 
		 * unique tab identifier, with the tab label as the value.
		 *
		 * @param array $tabs Array of tabs to display on the settings page.
		 */
		$tabs = apply_filters( 
			'eventorganiser_settings_tabs', 
			array(
				'general' => __( 'General', 'eventorganiser' ),
				'permissions' => __( 'Permissions', 'eventorganiser' ),
				'permalinks' => __( 'Permalinks', 'eventorganiser' ),
				'imexport' => __( 'Import', 'eventorganiser' ).'/'.__( 'Export', 'eventorganiser' ),
			)
		);
		return $tabs;
	}

	function set_constants(){
		$this->hook = 'options-general.php';
		$this->title = __( 'Event Organiser Settings', 'eventorganiser' );
		$this->menu = __( 'Event Organiser', 'eventorganiser' );
		$this->permissions = 'manage_options';
		$this->slug = 'event-settings';
		
		self::$eventorganiser_roles = array(
				'edit_events' => __( 'Edit Events', 'eventorganiser' ),
				'publish_events' => __( 'Publish Events', 'eventorganiser' ),
				'delete_events' => __( 'Delete Events', 'eventorganiser' ),
				'edit_others_events' => __( 'Edit Others\' Events', 'eventorganiser' ),
				'delete_others_events' => __( 'Delete Other\'s Events', 'eventorganiser' ),
				'read_private_events' => __( 'Read Private Events', 'eventorganiser' ),
				'manage_venues' => __( 'Manage Venues', 'eventorganiser' ),
				'manage_event_categories' => __( 'Manage Event Categories & Tags', 'eventorganiser' ),
		);
		$supports = eventorganiser_get_option( 'supports' );
		if( !in_array( 'event-venue', $supports ) ){
			unset( self::$eventorganiser_roles['manage_venues'] );
		}
	}

	function admin_init_actions(){
		
		register_setting( 'eventorganiser_options', 'eventorganiser_options', array( $this, 'validate' ) );
		
		//Initialise the tab array
		$this->tabs = $this->setup_tabs();
		
		foreach ( $this->tabs as $tab_id => $label ){
			//Add sections to each tabbed page
			switch ( $tab_id){
				case 'general':
					register_setting( 'eventorganiser_'.$tab_id, 'eventorganiser_options', array( $this, 'validate' ) );
					add_settings_section( $tab_id.'_licence', __( 'Add-on Licence keys', 'eventorganiser' ), array( $this, 'display_licence_keys' ), 'eventorganiser_'.$tab_id );
					add_settings_section( $tab_id,__( 'General', 'eventorganiser' ), '__return_false',  'eventorganiser_'.$tab_id);
					add_settings_section( $tab_id.'_templates',__( 'Templates', 'eventorganiser' ), '__return_false',  'eventorganiser_'.$tab_id);
					break;
				case 'permissions':
					register_setting( 'eventorganiser_'.$tab_id, 'eventorganiser_options', array( $this, 'validate' ) );
					add_settings_section( $tab_id, '',array( $this, 'display_permissions' ),  'eventorganiser_'.$tab_id);
					break;
				case 'permalinks':
					register_setting( 'eventorganiser_'.$tab_id, 'eventorganiser_options', array( $this, 'validate' ) );
					add_settings_section( $tab_id, '',array( $this, 'display_permalinks' ),  'eventorganiser_'.$tab_id);
					break;
				case 'imexport':
					register_setting( 'eventorganiser_'.$tab_id, 'eventorganiser_options', array( $this, 'validate' ) );
					add_settings_section( $tab_id, '',array( $this, 'display_imexport' ),  'eventorganiser_'.$tab_id );
					break;
			}
			
			/**
			 * Triggered after a settings tab has been registered.
			 * 
			 * The `$tab_id` in the hook name is the tab identifier 
			 * corresponding to the appropriate tabl.
			 * 
			 * Use this hook to register settings on a particular tab
			 * using `'eventorganiser_'.$tab_id` as the fourth argument
			 * of `add_settings_section()`.
			 * 
			 * @link https://codex.wordpress.org/Function_Reference/add_settings_section `add_settings_section()` codex
			 * @param string $tab_id Tab identifier.
			 */
			do_action("eventorganiser_register_tab_{$tab_id}", $tab_id );
		}
	}
	
	function page_actions(){
		//Register options
		add_action( 'eventorganiser_event_settings_permalinks', 'flush_rewrite_rules' );

		foreach ( $this->tabs as $tab_id => $label ){
			//Add sections to each tabbed page
			$this->add_fields( $tab_id );
		}
		
		if( !empty( $_GET['export-settings'] ) && $_GET['export-settings'] == 1 && current_user_can( 'manage_options' ) ){
			$this->export_settings();
		}
	}


	/**
	 * This is called in the register_settings method.
	 * Once the tabs have been registered, and sections added to each tabbed page, we now add the fields for each section
	 * A section should have the form {tab_id}._{identifer} (e.g. general_main, or gateways_google). 
	 * 
	 * @param $tab_id - the string identifer for the tab (given in $this->tabs as the key).
	 * @uses add_settings_field
	 */
	function add_fields( $tab_id){

		switch( $tab_id){
			case 'general':
				
				/* General - main */
				add_settings_field( 'supports', __( 'Select which features events should support', 'eventorganiser' ), 'eventorganiser_checkbox_field', 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'supports',
						'checked' => eventorganiser_get_option( 'supports' ),
						'options' => array(
							'author' => __( 'Organiser', 'eventorganiser' ).' ( '.__( 'Author', 'eventorganiser' ).' )',
							'thumbnail' => __( 'Thumbnail' ),
							'excerpt' => __( 'Excerpt' ),
							'custom-fields' => __( 'Custom Fields' ),
							'comments' => __( 'Comments' ),
							'revisions' => __( 'Revisions' ),
							'eventtag' => __( 'Event Tags', 'eventorganiser' ),
						),
						'name' => 'eventorganiser_options[supports]'
				) );

				add_settings_field( 'showpast',  __("Show past events:", 'eventorganiser' ), 'eventorganiser_checkbox_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'showpast',
						'name' => 'eventorganiser_options[showpast]',
						'options' => 1,
						'checked' => eventorganiser_get_option( 'showpast' ),
						'help' => __("Display past events on calendars, event lists and archives (this can be over-ridden by shortcode attributes and widget options).", 'eventorganiser' )
				) );

				add_settings_field( 'addtomenu',  __("Add an 'events' link to the navigation menu:", 'eventorganiser' ), array( $this, 'menu_option' ), 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'addtomenu',
				) );

				add_settings_field( 'dateformat', __( 'Date Format:', 'eventorganiser' ), 'eventorganiser_select_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'dateformat',
						'selected' => eventorganiser_get_option( 'dateformat' ),
						'name' => 'eventorganiser_options[dateformat]',
						'options' => array(
							'd-m-Y' => __( 'dd-mm-yyyy', 'eventorganiser' ),
							'm-d-Y' => __( 'mm-dd-yyyy', 'eventorganiser' ),
							'Y-m-d' => __( 'yyyy-mm-dd', 'eventorganiser' ),
						),
						'help' => __("This alters the default format for inputting dates.", 'eventorganiser' ),
				) );

				add_settings_field( 'showpast',  __("Show past events:", 'eventorganiser' ), 'eventorganiser_checkbox_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'showpast',
						'name' => 'eventorganiser_options[showpast]',
						'options' => 1,
						'checked' => eventorganiser_get_option( 'showpast' ),
						'help' => __("Display past events on calendars, event lists and archives (this can be over-ridden by shortcode attributes and widget options).", 'eventorganiser' )
				) );

				add_settings_field( 'group_events',  __("Group occurrences", 'eventorganiser' ), 'eventorganiser_checkbox_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'group_events',
						'name' => 'eventorganiser_options[group_events]',
						'options' => 'series',
						'checked' => eventorganiser_get_option( 'group_events' ),
						'help' => __("If selected only one occurrence of an event will be displayed on event lists and archives (this can be over-ridden by shortcode attributes and widget options.", 'eventorganiser' )
				) );				

				add_settings_field( 'runningisnotpast',  __("Are current events past?", 'eventorganiser' ), 'eventorganiser_select_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'runningisnotpast',
						'name' => 'eventorganiser_options[runningisnotpast]',
						'selected' => eventorganiser_get_option( 'runningisnotpast',0),
						'options' => array(
							'0' => __( 'No', 'eventorganiser' ),
							'1' => __( 'Yes', 'eventorganiser' ),
						),
						'help' => __("If 'no' is selected, an occurrence of an event is only past when it has finished. Otherwise, an occurrence is considered 'past' as soon as it starts.", 'eventorganiser' ),
				) );

				add_settings_field( 'deleteexpired',  __("Delete expired events:", 'eventorganiser' ), 'eventorganiser_checkbox_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'deleteexpired',
						'name' => 'eventorganiser_options[deleteexpired]',
						'options' => 1,
						'checked' => eventorganiser_get_option( 'deleteexpired' ),
						'help' => __("If selected the event will be automatically trashed 24 hours after the last occurrence finishes.", 'eventorganiser' )
				) );

				add_settings_field( 'feed',  __("Enable events ICAL feed:", 'eventorganiser' ), 'eventorganiser_checkbox_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'feed',
						'name' => 'eventorganiser_options[feed]',
						'options' => 1,
						'checked' => eventorganiser_get_option( 'feed' ),
						'help' => sprintf(__( 'If selected, visitors can subscribe to your events with the url: %s', 'eventorganiser' ), '<code>'.eo_get_events_feed().'</code>' )
				) );

				add_settings_field( 'excludefromsearch',  __("Exclude events from searches:", 'eventorganiser' ), 'eventorganiser_checkbox_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'excludefromsearch',
						'name' => 'eventorganiser_options[excludefromsearch]',
						'options' => 1,
						'checked' => eventorganiser_get_option( 'excludefromsearch' ),
				) );

				add_settings_field( 'templates',  __("Enable templates:", 'eventorganiser' ), 'eventorganiser_checkbox_field' , 'eventorganiser_'.$tab_id, $tab_id.'_templates',
					array(
						'label_for' => 'templates',
						'name' => 'eventorganiser_options[templates]',
						'options' => 1,
						'checked' => eventorganiser_get_option( 'templates' ),
						'help' => __("For each of the pages, the corresponding template is used. To use your own template simply give it the same name and store in your theme folder. By default, if Event Organiser cannot find a template in your theme directory, it will use its own default template. To prevent this, uncheck this option. WordPress will then decide which template from your theme's folder to use.", 'eventorganiser' )
									. sprintf(
											"<p><strong> %s </strong><code>archive-event.php</code></p>
											<p><strong> %s </strong><code>single-event.php</code></p>
											<p><strong> %s </strong><code>taxonomy-event-venue.php</code></p>
											<p><strong> %s </strong><code>taxonomy-event-category.php</code></p>",
												__("Events archives:", 'eventorganiser' ),
												__("Event page:", 'eventorganiser' ),
												 __("Venue page:", 'eventorganiser' ),
												__("Events Category page:", 'eventorganiser' )
										)
									.sprintf( 
										__( "For more information see documentation <a href='%s'>on editing the templates</a>", 'eventorganiser' ),
										'http://docs.wp-event-organiser.com/theme-integration'
									)
					) );
				
				add_settings_field( 'disable_css',  __("Disable CSS:", 'eventorganiser' ), 'eventorganiser_checkbox_field' , 'eventorganiser_'.$tab_id, $tab_id.'_templates',
					array(
						'label_for' => 'disable_css',
						'name' => 'eventorganiser_options[disable_css]',
						'options' => 1,
						'checked' => eventorganiser_get_option( 'disable_css' ),
						'help' => __( 'Check this option to prevent any stylesheets from Event Organiser being loaded on the front-end', 'eventorganiser' )
				));
				break;


			case 'permissions':
				break;

			case 'permalinks':
				add_settings_field( 'prettyurl',  __("Enable event pretty permalinks:", 'eventorganiser' ), 'eventorganiser_checkbox_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'prettyurl',
						'name' => 'eventorganiser_options[prettyurl]',
						'options' => 1,
						'checked' => eventorganiser_get_option( 'prettyurl' ),
						'help' => __("If you have pretty permalinks enabled, select to have pretty premalinks for events.", 'eventorganiser' )
				) );

				$home_url = home_url();
				add_settings_field( 'url_event', __("Event (single)", 'eventorganiser' ), 'eventorganiser_text_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'url_event',
						'name' => 'eventorganiser_options[url_event]',
						'class' => 'regular-text',
						'value' => eventorganiser_get_option( 'url_event' ),
						'help' => "<code>{$home_url}/<strong>".eventorganiser_get_option( 'url_event' )."</strong>/[event_slug]</code>"
				) );

				add_settings_field( 'url_events', __("Events page", 'eventorganiser' ), 'eventorganiser_text_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'url_events',
						'name' => 'eventorganiser_options[url_events]',
						'class' => 'regular-text',
						'value' => eventorganiser_get_option( 'url_events' ),
						'help' => "<code>{$home_url}/<strong>".eventorganiser_get_option( 'url_events' )."</strong></code>"
				) );
				
				$now = new DateTime();

				$base_url = eventorganiser_get_option( 'url_events' )."/<strong>".eventorganiser_get_option( 'url_on' )."</strong>";
				
				add_settings_field( 'url_on', __("Event (date archive)", 'eventorganiser' ), 'eventorganiser_text_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 	'url_on',
						'name' 		=> 	'eventorganiser_options[url_on]',
						'class' => 'regular-text',
						'value' 	=> 	eventorganiser_get_option( 'url_on' ),
						'help' 		=> 	"<code>{$home_url}/{$base_url}/{$now->format('Y')}</code> ".__('Year archive', 'eventorganiser').'<br>'
										."<code>{$home_url}/{$base_url}/{$now->format('Y/m')}</code>".__('Month archive', 'eventorganiser').'<br>'
										."<code>{$home_url}/{$base_url}/{$now->format('Y/m/d')}</code>".__('Day archive', 'eventorganiser')
					) );
	
				$supports = eventorganiser_get_option( 'supports' );
				if( in_array( 'event-venue', $supports ) ){
					add_settings_field( 'url_venue', __("Venues", 'eventorganiser' ), 'eventorganiser_text_field' , 'eventorganiser_'.$tab_id, $tab_id,
						array(
							'label_for' => 'url_venue',
							'name' => 'eventorganiser_options[url_venue]',
							'class' => 'regular-text',
							'value' => eventorganiser_get_option( 'url_venue' ),
							'help' => "<code>{$home_url}/<strong>".eventorganiser_get_option( 'url_venue' )."</strong>/[venue_slug]</code>"
					) );
				}

				add_settings_field( 'url_cat', __("Event Categories", 'eventorganiser' ), 'eventorganiser_text_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'url_cat',
						'name' => 'eventorganiser_options[url_cat]',
						'class' => 'regular-text',
						'value' => eventorganiser_get_option( 'url_cat' ),
						'help' => "<code>{$home_url}/<strong>".eventorganiser_get_option( 'url_cat' )."</strong>/[event_cat_slug]</code>"
				) );

				add_settings_field( 'url_tag', __("Event Tags", 'eventorganiser' ), 'eventorganiser_text_field' , 'eventorganiser_'.$tab_id, $tab_id,
					array(
						'label_for' => 'url_tag',
						'name' => 'eventorganiser_options[url_tag]',
						'class' => 'regular-text',
						'value' => eventorganiser_get_option( 'url_tag' ),
						'help' => "<label><code>{$home_url}/<strong>".eventorganiser_get_option( 'url_tag' )."</strong>/[event_tag_slug]</code></label>"
				) );

				break;
		}
	}


	function validate( $option ){
		/* Backwards compatibility: all EO options are in one row, but on seperate pages. Merge with existing options*/

		if (  isset( $option['tab'] )  ){
			$tab = $option['tab'];
			unset( $option['tab'] );
		} else {
			$tab = false;
		}
		
		$clean = array();
		
		switch ( $tab ){
			case 'general':
				$checkboxes  = array( 'showpast', 'templates', 'excludefromsearch', 'deleteexpired', 'feed', 'group_events', 'disable_css' );
				$text = array( 'navtitle', 'dateformat', 'runningisnotpast', 'addtomenu' );

				foreach ( $checkboxes as $cb ){

					//Empty checkboxes send no data..
					$value = isset( $option[$cb] ) ? $option[$cb] : 0;
				
					$clean[$cb] = $this->validate_checkbox( $value );
				}

				foreach ( $text as $txt ){
					if ( !isset( $option[$txt] ) )
						continue;
				
					$clean[$txt] = $this->validate_text( $option[$txt] );
				}

				//Group events is handled differently
				$clean['group_events'] = ( !empty( $clean['group_events'] ) ? 'series' : '' );

				//Post type supports
				$clean['supports'] = (isset( $option['supports'] ) ? array_map( 'esc_html',$option['supports'] ) : array() );
				$clean['supports'] = array_merge( $clean['supports'],array( 'title', 'editor' ) );

				//Navigation menu - $addtomenu int 0 if no menu, menu databse ID otherwise
				$clean['menu_item_db_id'] = $this->update_nav_menu( $clean['addtomenu'], $clean['navtitle'] );

				if( $clean['deleteexpired'] && !eventorganiser_get_next_cron_time( 'eventorganiser_delete_expired' ) ){
					eventorganiser_cron_jobs();
				}elseif( !$clean['deleteexpired'] ){
					eventorganiser_clear_cron_jobs();
				}
			break;


			case 'permalinks':
				$permalinks = array( 'url_event', 'url_events', 'url_venue', 'url_cat', 'url_tag', 'url_on' );
				
				foreach ( $permalinks as $permalink ){
					if ( !isset( $option[$permalink] ) )
						continue;
			
					$value = $this->validate_permalink( $option[$permalink] );

					if ( !empty( $value) )
						$clean[$permalink] = $value;
				}
			
				$clean['prettyurl'] = isset( $option['prettyurl'] ) ? $this->validate_checkbox( $option['prettyurl'] ) : 0;
			break;


			case 'permissions':
				//Permissions
				$permissions = (isset( $option['permissions'] ) ? $option['permissions'] : array() );
				$this->update_roles( $permissions );
			break;
			
			default:
				$keys = array( 'hide_addon_page' );
				foreach( $keys as $key ){
					if( !isset( $option[$key] ) )
						continue;
					
					$clean[$key] = (int) $option[$key];
					
				}
			
			break;
		}
		

		$existing_options = get_option( 'eventorganiser_options', array() );
		$clean = array_merge( $existing_options, $clean );
		return $clean;
	}

	function validate_checkbox( $value ){
		return ( !empty( $value ) ? 1 : 0 );
	}

	function validate_text( $value ){
		return ( !empty( $value ) ? esc_html( $value ) : false );
	}

	function validate_permalink( $value ){
		return trim( str_replace( 'http://', '', esc_url_raw( $value ) ), '/' );
		return $value;
	}

	/**
	 *
 	 *@param $menu_databse_id int 0 for no menu, 1 for 'fallback', term ID for menu otherwise
	 *
	*/
	function update_nav_menu( $menu_id, $menu_item_title ){

		//Get existing menu item
		$menu_item_db_id = (int) eventorganiser_get_option( 'menu_item_db_id' );
	
		//Validate exiting menu item ID
		if ( !is_nav_menu_item( $menu_item_db_id ) ){
			$menu_item_db_id = 0;
		}

		//If Menu is not selected, or 'page list' fallback is, and we have an 'events' item added to some menu, remove it
		if ( (empty( $menu_id ) || $menu_id == '1' ) && is_nav_menu_item( $menu_item_db_id ) ){
			//Remove menu item
			wp_delete_post( $menu_item_db_id, true );
			$menu_item_db_id = 0;
		}

		//If the $menu is an int > 1, we are adding/updating an item (post type) so that it has term set to $menu_id
		if ( ( !empty( $menu_id ) && $menu_id != '1' ) ){
			$menu_item_data = array();
			
			//Validate menu ID
			$menu_obj = wp_get_nav_menu_object( $menu_id );
			$menu_id = ( $menu_obj ? $menu_obj->term_id : 0 );

			//Set status
			$status = ( $menu_id == 0 ? '' : 'publish' );

			$menu_item_data = array(	
				'menu-item-title' => $menu_item_title,
				'menu-item-url' => get_post_type_archive_link( 'event' ),
				'menu-item-object' => 'event',
				'menu-item-status' => $status,
				'menu-item-type' => 'post_type_archive',
			);
			
			//If we're updating preserve parent and position
			if( is_nav_menu_item( $menu_item_db_id ) ){
				$menu_item = wp_setup_nav_menu_item( get_post( $menu_item_db_id ) );
				$menu_item_data += array(
					'menu-item-parent-id' => $menu_item->menu_item_parent,
					'menu-item-position' => $menu_item->menu_order,
				);
			}

			//Update menu item (post type) to have taxonom term $menu_id
			$menu_item_db_id = wp_update_nav_menu_item( $menu_id, $menu_item_db_id,$menu_item_data );
		}

		//Return the menu item (post type) ID. 0 For no item added, or item removed.
		return $menu_item_db_id;
	}


	function update_roles( $permissions ){
		global $wp_roles,$EO_Errors;

		foreach ( get_editable_roles() as $role_name => $display_name ):
			$role = $wp_roles->get_role( $role_name );
			//Don't edit the administrator
			if ( $role_name == 'administrator' )
				continue;

			//Foreach custom role, add or remove option.
			foreach ( self::$eventorganiser_roles as $eo_role => $eo_role_display ):
				if ( isset( $permissions[$role_name][$eo_role] ) && $permissions[$role_name][$eo_role] == 1 ){
					$role->add_cap( $eo_role );		
				} else {
					$role->remove_cap( $eo_role );		
				}
			endforeach; //End foreach $eventRoles
		endforeach; //End foreach $editable_roles
	}



	function display(){
		?>
    		<div class="wrap">  
      
			<?php screen_icon( 'options-general' ); ?>
		        <?php 

				$active_tab = ( isset( $_GET[ 'tab' ] ) &&  isset( $this->tabs[$_GET[ 'tab' ]] ) ? $_GET[ 'tab' ] : 'general' );
				$page = $this->slug;

				echo '<h2 class="nav-tab-wrapper">';

					foreach ( $this->tabs as $tab_id => $label ){
				          	printf(
							'<a href="?page=%s&tab=%s" class="nav-tab %s">%s</a>',
							$page,
							$tab_id,
							( $active_tab == $tab_id ? 'nav-tab-active' : '' ),
							esc_html( $label )
						);
					}
				echo '</h2>';
				?>

				<?php if ( 'imexport' != $active_tab ){
					echo '<form method="post" action="options.php">';
						settings_fields( 'eventorganiser_'.$active_tab );
						do_settings_sections( 'eventorganiser_'.$active_tab ); 
	
						/**
						 * @ignore
						 */
						do_action( 'eventorganiser_event_settings_'.$active_tab );
						//Tab identifier - so we know which tab we are validating. See $this->validate().
						printf( '<input type="hidden" name="eventorganiser_options[tab]" value="%s" />', esc_attr( $active_tab ) );
						submit_button(); 
				        echo '</form>';
				} else {
					/**
					 * @ignore
					 * The import/export tab is handled differently not using `add_settings_section()`.
					 * This tab may be move too Tools.
					 */
					do_action( 'eventorganiser_event_settings_imexport' ); 
				}
				?>
          
		</div><!-- /.wrap -->  

	<?php
	}
	
	function display_licence_keys(){
		global $wp_settings_fields;
		$page = 'eventorganiser_general';
		$section_id = 'general_licence';
		$addon_link = esc_url( admin_url( 'edit.php?post_type=event&page=eo-addons' ) );

		if( is_multisite() ){
			printf( "<p>For multisites, license keys should be entered on the <a href='%s'>Network Settings</a> page.</p>", network_admin_url( 'settings.php' ) );
		}else{
			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section_id] ) ){
				echo "<p> You do not have any add-ons activated. You can view the <a href='".$addon_link."'>available add-ons here</a>.</p>";
			}else{ 
				echo "<p> Below are the add-ons you have activated. You can find a full list of <a href='".$addon_link."'>available add-ons here</a>. </p>";
			}	
		} 
	}
	
	
	function display_permissions(){
		global $wp_roles;
		
		?>
		<h4> <?php _e( 'Event management permissions', 'eventorganiser' ); ?></h4>
		<p> <?php _e( 'Set permissions for events and venue management', 'eventorganiser' ); ?> </p>
		
		<table class="widefat fixed posts">
			<thead>
			<tr>
				<th><?php _e( 'Role', 'eventorganiser' ); ?></th>
				<?php foreach ( self::$eventorganiser_roles as $eo_role => $eo_role_display ): ?>
					<th><?php echo esc_html( $eo_role_display );?></th>
				<?php endforeach; ?> 
			</tr>
			</thead>		
		<tbody id="the-list">
			<?php
			$array_index = 0;
			foreach ( get_editable_roles() as $role_name => $display_name ):
				$role = $wp_roles->get_role( $role_name ); 
				$role_name = isset( $wp_roles->role_names[$role_name] ) ? translate_user_role( $wp_roles->role_names[$role_name] ) : __( 'None' );

				printf( '<tr %s>', $array_index == 0 ? 'class="alternate"' : '' );
					printf( '<td> %s </td>',esc_html( $role_name ) );

					foreach ( self::$eventorganiser_roles as $eo_role => $eo_role_display ):
						printf(
							'<td>
								<input type="checkbox" name="eventorganiser_options[permissions][%s][%s]" value="1" %s %s  />
							</td>',
							esc_attr( $role->name ),
							esc_attr( $eo_role ),
							checked( '1', $role->has_cap( $eo_role ), false ),
							disabled( $role->name, 'administrator', false ) 
						);
					endforeach; //End foreach $eventRoles 
				echo '</tr>';
	
				$array_index = ( $array_index + 1) % 2;
			endforeach; //End foreach $editable_role ?>
		</tbody>
		</table>
	<?php
	}


	function display_permalinks(){
		printf(
			'<p> %s </p> <p> %s %s </p>',
			esc_html__( 'Choose a custom permalink structure for events, venues, event categories and event tags.', 'eventorganiser' ),
			esc_html__( 'Please note to enable these structures you must first have pretty permalinks enabled on WordPress in Settings > Permalinks.', 'eventorganiser' ),
			esc_html__( "You may also need to go to WordPress Settings > Permalinks and click 'Save Changes' before any changes will take effect.", 'eventorganiser' )
		);
	}
	
	function export_settings(){
		
		$options = array();
		$options['event-organiser'] = eventorganiser_get_option( false );
		
		/**
		 * Settings to include in an export.
		 *
		 * These options are included in both the settings export on the settings
		 * page and the also printed in the system information file. By default
		 * they inlude Event Organiser's options, but others can be added.
		 *
		 * The filtered value should be a (2+ dimensional) array, indexed by plugin/
		 * extension name.
		 *
		 * @param array $options Array of user settings, indexed by plug-in/extension.
		 */
		$options = apply_filters( 'eventorganiser_export_settings', $options );
		
		$filename = 'event-organiser-settings-'.get_bloginfo( 'name' ).'.json'; 
		$filename = sanitize_file_name( $filename );
		
		header('Content-disposition: attachment; filename=' . $filename );
		header('Content-type: application/json');
		echo json_encode( $options );
		exit();
	}

	function menu_option(){

		$menus = get_terms( 'nav_menu' );?>
			<select  name="eventorganiser_options[addtomenu]">
				<option  <?php selected( 0,eventorganiser_get_option( 'addtomenu' ) );?> value="0"><?php _e( 'Do not add to menu', 'eventorganiser' ); ?> </option>
			<?php foreach ( $menus as $menu ): ?>
				<option  <?php selected( $menu->slug,eventorganiser_get_option( 'addtomenu' ) );?> value="<?php echo $menu->slug; ?>"><?php echo $menu->name;?> </option>
			<?php endforeach; ?>
				<option  <?php selected( 1, eventorganiser_get_option( 'addtomenu' ) );?> value="1"><?php _e( 'Page list (fallback)', 'eventorganiser' ); ?></option>
			</select>

			<?php printf( '<input type="hidden" name ="eventorganiser_options[menu_item_db_id]" value="%d" />',eventorganiser_get_option( 'menu_item_db_id' ) ); ?>
			<?php printf( '<input type="text" name="eventorganiser_options[navtitle]" value="%s" />',eventorganiser_get_option( 'navtitle' ) ); 
	}
}
$settings_page = new EventOrganiser_Settings_Page();
?>
