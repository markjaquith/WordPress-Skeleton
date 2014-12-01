<?php

/**
 * Registers the event taxonomies: event-venue, event-category and optinally event-tag
 * Hooked onto init
 *
 * @ignore
 * @access private
 * @since 1.0
 */
function eventorganiser_create_event_taxonomies() {

	if( !eventorganiser_get_option( 'prettyurl' ) ){
		$cat_rewrite = $tag_rewrite = $venue_rewrite = false;

	}else{
		$cat_slug = trim(eventorganiser_get_option('url_cat','events/category'), "/");
		$cat_rewrite = array( 'slug' => $cat_slug, 'with_front' => false );

		$tag_slug = trim(eventorganiser_get_option('url_tag','events/tag'), "/");
		$tag_rewrite = array( 'slug' => $tag_slug, 'with_front' => false );

		$venue_slug = trim(eventorganiser_get_option('url_venue','events/venue'), "/");
		$venue_rewrite = array( 'slug' => $venue_slug, 'with_front' => false );
	}
	
	//Register event venue taxonomy
	$supports = eventorganiser_get_option( 'supports' );
	if( in_array( 'event-venue', $supports ) ){
		$venue_labels = array(
			'name'                       => __( 'Venues','eventorganiser' ),
    		'singular_name'              => __( 'Venue', 'eventorganiser' ),
    		'search_items'               => __( 'Search Venues', 'eventorganiser' ),
	    	'all_items'                  => __( 'All Venues', 'eventorganiser' ),
			'view_item'                  => __( 'View Venue', 'eventorganiser' ),
			'edit_item'                  => __( 'Edit Venue', 'eventorganiser' ),
			'update_item'                => __( 'Update Venue', 'eventorganiser' ),
			'add_new_item'               => __( 'Add New Venue', 'eventorganiser' ),
			'new_item_name'              => __( 'New Venue Name', 'eventorganiser' ),
			'not_found'                  => __( 'No venues found', 'eventorganiser' ),
			'add_or_remove_items'        => __( 'Add or remove venues', 'eventorganiser' ),
			'separate_items_with_commas' => __( 'Separate venues with commas', 'eventorganiser' ),
			//non-default wp labels
			'venue_location'             => __( 'Venue Location', 'eventorganiser' ),
			'view_all_items'             => __( 'View all venues', 'eventorganiser' ),
			'singular_name_colon'        => __( 'Venue:', 'eventorganiser' ),
			'no_item'                    => __( 'No Venue', 'eventorganiser' ),
  		); 		

  		$event_venue_args = array(
			'hierarchical'          => false,
			'labels'                => $venue_labels,
			'public'                => true,
			'show_in_nav_menus'     => true,
			'show_ui'               => false,//Use custom UI
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
  			'rewrite'               => $venue_rewrite,
			'capabilities'          => array(
				'manage_terms' => 'manage_venues',
				'edit_terms'   => 'manage_venues',
				'delete_terms' => 'manage_venues',
				'assign_terms' => 'edit_events'
  			),
  		);
		
	}else{
		$event_venue_args = false;		
	}
	/**
	 * Filters the event venue taxonomy properties.
	 * 
	 * Allows you to change the properties and labels of the event venue taxonomy. You can 
	 * return `false` to prevent the taxonomy from registering. All labels include those
	 * supported by `register_taxonomy()`, as well as additional strings:
	 * 
	 * * venue_location - Venue location metabox title
	 * * view_all_items - Used in drop-down filters for venues 
	 * * singular_name_colon - Same as singular_name but with a colon.
	 * * no_item - Venue selection, when opting no to select a venue for the event.
	 *
	 * @param array|bool $event_venue_args Settings passed to `register_taxonomy()` in the third argument.
	 *                                     Does not register the taxonomy if set to false.
	 */
	$event_venue_args = apply_filters( 'eventorganiser_register_taxonomy_event-venue', $event_venue_args );
	if( $event_venue_args ){
		register_taxonomy( 'event-venue',array('event'), $event_venue_args );	
	}
	
	//Register event category taxonomy
	$category_labels = array(
		'name'              => __( 'Event Categories', 'eventorganiser' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Categories' ),
		'all_items'         => __( 'All Categories' ),
		'parent_item'       => __( 'Parent Category' ),
		'parent_item_colon' => __( 'Parent Category:' ),
		'edit_item'         => __( 'Edit Category' ), 
		'update_item'       => __( 'Update Category' ),
		'add_new_item'      => __( 'Add New Category' ),
		'new_item_name'     => __( 'New Category Name' ),
		'not_found'         => __( 'No categories found' ),
		'menu_name'         => __( 'Categories' ),
		//Non-wp default labels
		'view_all_items'             => __( 'View all categories', 'eventorganiser' ),
  	); 	
  	
  	$event_category_args = array(
		'hierarchical'          => true,
		'labels'                => $category_labels,
		'show_ui'               => true,
    	'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
  		'rewrite'               => $cat_rewrite,
  		'public'                => true,
		'capabilities' => array(
			'manage_terms' => 'manage_event_categories',
			'edit_terms'   => 'manage_event_categories',
			'delete_terms' => 'manage_event_categories',
			'assign_terms' => 'edit_events'
  		),
  	);
  	
  	/**
	 * Filters the event category taxonomy properties.
	 * 
	 * Allows you to change the properties and labels of the event venue taxonomy. You can 
	 * return `false` to prevent the taxonomy from registering. All labels include those
	 * supported by `register_taxonomy()`, as well as additional strings:
	 * 
	 * * view_all_items - Used in drop-down filters for venues 
	 *
	 * @param array|bool $event_category_args Settings passed to `register_taxonomy()` in the third argument.
	 *                                        Does not register the taxonomy if set to false.
	 */
	$event_category_args = apply_filters( 'eventorganiser_register_taxonomy_event-category', $event_category_args );
	
	if( $event_category_args ){
		register_taxonomy( 'event-category',array('event'), $event_category_args );	
	}

	//Register event tag taxonomy
	if( eventorganiser_get_option('eventtag') ){

		$tag_labels = array(
			'name'                       => __( 'Event Tags', 'eventorganiser' ),
			'singular_name'              => _x( 'Tag', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Tags' ),
			'all_items'                  => __( 'All Tags' ),
			'popular_items'              => __( 'Popular Tags' ),
			'edit_item'                  => __( 'Edit Tag' ),
			'update_item'                => __( 'Update Tag' ),
			'add_new_item'               => __( 'Add New Tag' ),
			'new_item_name'              => __( 'New Tag Name' ),
			'not_found'                  => __( 'No tags found' ),
			'choose_from_most_used'      => __( 'Choose from the most used tags' ),
			'menu_name'                  => __( 'Tags' ),
			'add_or_remove_items'        => __( 'Add or remove tags' ),
			'separate_items_with_commas' => __( 'Separate tags with commas' ),
			//Non-wp default labels
			'view_all_items'             => __( 'View all tags', 'eventorganiser' ),
  		); 	

  		$event_tag_args = array(
			'hierarchical'          => false,
			'labels'                => $tag_labels,
			'show_ui'               => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
  			'public'                => true,
			'rewrite'               => $tag_rewrite,
			'capabilities' => array(
				'manage_terms' => 'manage_event_categories',
				'edit_terms'   => 'manage_event_categories',
				'delete_terms' => 'manage_event_categories',
				'assign_terms' =>'edit_events'
  			),
  		);
  		
	}else{
		$event_tag_args = false;
	}
		
	/**
	 * Filters the event tag taxonomy properties.
	 * 
	 * Allows you to change the properties and labels of the event venue taxonomy. You can 
	 * return `false` to prevent the taxonomy from registering. All labels include those
	 * supported by `register_taxonomy()`, as well as additional strings:
	 * 
	 * * view_all_items - Used in drop-down filters for venues 
	 *
	 * @param array|bool $event_tag_args Settings passed to `register_taxonomy()` in the third argument.
	 *                                   Does not register the taxonomy if set to false.
	 */
	$event_tag_args = apply_filters( 'eventorganiser_register_taxonomy_event-tag', $event_tag_args );
	
	if( $event_tag_args ){
		register_taxonomy( 'event-tag',array( 'event' ), $event_tag_args );	
	}
  		
}
add_action( 'init', 'eventorganiser_create_event_taxonomies', 1 );


/**
 * Registers the event custom post type
 * Hooked onto init
 *
 * @ignore
 * @access private
 * @since 1.0
 */
function eventorganiser_cpt_register() {

  	$labels = array(
		'name' => __('Events','eventorganiser'),
		'singular_name' => __('Event','eventorganiser'),
		'add_new' => _x('Add New','post'),
		'add_new_item' => __('Add New Event','eventorganiser'),
		'edit_item' =>  __('Edit Event','eventorganiser'),
		'new_item' => __('New Event','eventorganiser'),
		'all_items' =>__('All events','eventorganiser'),
		'view_item' =>__('View Event','eventorganiser'),
		'search_items' =>__('Search events','eventorganiser'),
		'not_found' =>  __('No events found','eventorganiser'),
		'not_found_in_trash' =>  __('No events found in Trash','eventorganiser'),
		'parent_item_colon' => '',
		'menu_name' => __('Events','eventorganiser'),
  );

	$exclude_from_search = (eventorganiser_get_option('excludefromsearch')==0) ? false : true;

	if( !eventorganiser_get_option('prettyurl') ){
		$event_rewrite = false;
		$events_slug = true;
	}else{
		$event_slug = trim(eventorganiser_get_option('url_event','events/event'), "/");
		$events_slug = trim(eventorganiser_get_option('url_events','events/event'), "/");
		$on = trim(eventorganiser_get_option('url_on','on'), "/");
		$event_rewrite = array( 'slug' => $event_slug, 'with_front' => false,'feeds'=> true,'pages'=> true );

		/* Workaround for https://core.trac.wordpress.org/ticket/19871 */
		global $wp_rewrite;  
		$wp_rewrite->add_rewrite_tag('%event_ondate%','([0-9]{4}(?:/[0-9]{2}(?:/[0-9]{2})?)?)','post_type=event&ondate='); 
		add_permastruct('event_archive', $events_slug.'/'.$on.'/%event_ondate%', array( 'with_front' => false ) );
	}

/**
 * Filters the menu position.
 * 
 * This allows you to change where "Events" appears in the admin menu.
 * 
 * @link https://codex.wordpress.org/Function_Reference/register_post_type register_post_type codex. 
 * @param int $menu_position Menu position. Defaults to 5.
 */
$menu_position = apply_filters('eventorganiser_menu_position',5);
$args = array(
	'labels' => $labels,
	'public' => true,
	'publicly_queryable' => true,
	'exclude_from_search'=>$exclude_from_search,
	'show_ui' => true, 
	'show_in_menu' => true, 
	'query_var' => true,
	'capability_type' => 'event',
	'rewrite' => $event_rewrite,
	'capabilities' => array(
		'publish_posts' => 'publish_events',
		'edit_posts' => 'edit_events',
		'edit_others_posts' => 'edit_others_events',
		'delete_posts' => 'delete_events',
		'delete_others_posts' => 'delete_others_events',
		'read_private_posts' => 'read_private_events',
		'edit_post' => 'edit_event',
		'delete_post' => 'delete_event',
		'read_post' => 'read_event',
	),
	'has_archive' => $events_slug, 
	'hierarchical' => false,
	'menu_icon' => ( defined( 'MP6' ) && MP6 ? false : EVENT_ORGANISER_URL.'css/images/eoicon-16.png' ),
	'menu_position' => $menu_position,
	'supports' => eventorganiser_get_option('supports'),
  ); 

	/**
	 * Filters the settings used in `register_post_type()` for event post type.
	 * 
	 * @param array $args Settings passed to `register_post_type()` in the second argument.
	 */
	$args = apply_filters( 'eventorganiser_event_properties', $args );
	register_post_type( 'event', $args );
}
add_action('init', 'eventorganiser_cpt_register');


/**
 * Sets the messages that appear when an event is updated / saved.
 * Hooked onto post_updated_messages
 *
 * @ignore
 * @access private
 * @since 1.0
 */
function eventorganiser_messages( $messages ) {
	global $post, $post_ID;

	$messages['event'] = array(
    		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Event updated. <a href="%s">View event</a>','eventorganiser'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Event updated.','eventorganiser'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Event restored to revision from %s','eventorganiser'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Event published. <a href="%s">View event</a>','eventorganiser'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Event saved.'),
		8 => sprintf( __('Event submitted. <a target="_blank" href="%s">Preview event</a>','eventorganiser'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>','eventorganiser'),
		 // translators: Publish box date format, see https://php.net/date
      		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Event draft updated. <a target="_blank" href="%s">Preview event</a>','eventorganiser'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		20 => __('This event has been broken from a recurring event.','eventorganiser')
  	);
	return $messages;
}
add_filter('post_updated_messages', 'eventorganiser_messages');


/**
 * Maps meta capabilities to primitve ones for event post type
 *
 * @ignore
 * @access private
 * @since 1.0
 */
function eventorganiser_event_meta_cap( $caps, $cap, $user_id, $args ) {

	/* If editing, deleting, or reading a event, get the post and post type object. */
	if ( 'edit_event' == $cap || 'delete_event' == $cap || 'read_event' == $cap ) {
		$post = get_post( $args[0] );
		$post_type = get_post_type_object( $post->post_type );	

		/* Set an empty array for the caps. */
		$caps = array();
		if($post->post_type!='event')
			return $caps;
	}

	/* If editing a event, assign the required capability. */
	if ( 'edit_event' == $cap ) {
		if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->edit_posts;
		else
			$caps[] = $post_type->cap->edit_others_posts;
	}

	/* If deleting a event, assign the required capability. */
	elseif ( 'delete_event' == $cap ) {
		if (isset($post->post_author ) && $user_id == $post->post_author)
			$caps[] = $post_type->cap->delete_posts;
		else
			$caps[] = $post_type->cap->delete_others_posts;
	}

	/* If reading a private event, assign the required capability. */
	elseif ( 'read_event' == $cap ) {

		if ( 'private' != $post->post_status )
			$caps[] = 'read';
		elseif ( $user_id == $post->post_author )
			$caps[] = 'read';
		else
			$caps[] = $post_type->cap->read_private_posts;
	}

	/* Return the capabilities required by the user. */
	return $caps;
}
add_filter( 'map_meta_cap', 'eventorganiser_event_meta_cap', 10, 4 );


/**
 * Adds the Event Organiser icon to the page head
 * Hooked onto admin_head
 *
 * @ignore
 * @access private
 * @since 1.0
 */
function eventorganiser_plugin_header_image() {
        global $post_type;

	if ((isset($_GET['post_type']) && $_GET['post_type'] == 'event') || ($post_type == 'event')) : ?>
	<style>
	#icon-edit { background:transparent url('<?php echo EVENT_ORGANISER_URL.'/css/images/eoicon-32.png';?>') no-repeat; }		
        </style>
	<?php endif; 
}
add_action('admin_head', 'eventorganiser_plugin_header_image');

/**
 * With appropriate settings we add a menu item of 'post_type_archive' type. 
 * WP doesn't understand this so we set the url ourself - hooking just before its saved to db.
 * Hooked onto wp_update_nav_menu_item
 *
 * @ignore
 * @access private
 * @since 1.0
 */
function eventorganiser_update_nav_item($menu_id,$menu_item_db_id,$args){
	if($args['menu-item-type'] == 'post_type_archive' && $args['menu-item-object'] =='event'){
		$post_type = $args['menu-item-object'];
		$args['menu-item-url'] = get_post_type_archive_link($post_type);
		update_post_meta( $menu_item_db_id, '_menu_item_url', esc_url_raw($args['menu-item-url']) );
	}
}
add_action('wp_update_nav_menu_item','eventorganiser_update_nav_item',10,3);


/**
 * WP doesn't know when our custom menu item ('Events') is 'current'. We make it 'current', by
 * adding the appropriate classes if viewing an event, event archive or event taxonomy
 * Hooked onto wp_nav_menu_objects
 *
 * @ignore
 * @access private
 * @since 1.0
 */
function eventorganiser_make_item_current($items,$args){
	if(is_post_type_archive('event')|| is_singular('event')|| eo_is_event_taxonomy()){
		foreach ($items as $item){
			if('post_type_archive'!=$item->type || 'event'!=$item->object)
				continue;

			$item->classes[] = 'current-menu-item';
	
			$_anc_id = (int) $item->db_id;
			$active_ancestor_item_ids=array();
			while(( $_anc_id = get_post_meta( $_anc_id, '_menu_item_menu_item_parent', true ) ) &&
				! in_array( $_anc_id, $active_ancestor_item_ids )  ){
					$active_ancestor_item_ids[] = $_anc_id;
			}
		
			//Loop through ancestors and give them 'ancestor' or 'parent' class
			foreach ($items as $key=>$parent_item){
              	      $classes = (array) $parent_item->classes;

              	      //If menu item is the parent
              	      if ($parent_item->db_id == $item->menu_item_parent ) {
              	           $classes[] = 'current-menu-parent';
              	           $items[$key]->current_item_parent = true;
              	      }

              	      //If menu item is an ancestor
              	      if ( in_array(  intval( $parent_item->db_id ), $active_ancestor_item_ids ) ) {
              	           $classes[] = 'current-menu-ancestor';
              	           $items[$key]->current_item_ancestor = true;
              	      }

              	      $items[$key]->classes = array_unique( $classes );
			}
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'eventorganiser_make_item_current',10,2);


/**
 * If a menu isn't being used the above won't work. They're using wp_list_pages, so the
 * best we can do is append a link to the end of the list.
 * Hooked onto wp_list_pages
 *
 * @ignore
 * @access private
 * @since 1.0
 */
function eventorganiser_menu_link($items) {

	if( eventorganiser_get_option('addtomenu') != '1' )
		return $items;

	$class ='menu-item menu-item-type-event';

	if(is_post_type_archive('event')|| is_singular('event')|| eo_is_event_taxonomy())
		$class = 'current_page_item';
	
	$items .= sprintf('<li class="%s"><a href="%s" > %s </a></li>',
						$class,
						get_post_type_archive_link('event'),
						esc_html(eventorganiser_get_option('navtitle'))
					);
	return $items;
}
add_filter( 'wp_list_pages', 'eventorganiser_menu_link',10,1 );

/**
 * Contextual help for event pages
 *
 * @ignore
 * @access private
 * @since 1.0
 */
function eventorganiser_cpt_help_text($contextual_help, $screen_id, $screen) { 

	//The add_help_tab function for screen was introduced in WordPress 3.3. Add it only to event pages.
	if( ! method_exists($screen, 'add_help_tab') || ! in_array($screen_id, array('event','edit-event','event_page_venues','event_page_calendar')) )
		return $contextual_help;
	
	switch($screen_id):
		//Add help for event editing / creating page
		case ('event'):
			    $screen->add_help_tab( array(
			        'id'      => 'creating-events', 
			        'title'   => __('Creating events','eventorganiser'),
        			'content' => '<p>' . __('Creating events:','eventorganiser') . '</p>'.
			'<ul>' .
				'<li>' . __('The start date is the date the event starts. If the event is a reoccuring event, this is the start date of the first occurrence.','eventorganiser') . '</li>' .
				'<li>' . __('The end date is the date the event finishes. If the event is a reoccuring event, this is the end date of the first occurrence.','eventorganiser') . '</li>' .
				'<li>' . __('All dates and times must be entered in the specified format. This format can changed in the settings page.','eventorganiser') . '</li>' .
			'</ul>'
				));
			    $screen->add_help_tab( array(
			        'id'      => 'repeating-events',
			        'title'   => __('Repeating events','eventorganiser'),
        			'content' => '<p>' . __('To repeat an event according to some regular pattern, use the reoccurrence dropdown menu to select how the event is to repeat. Further options then appear, ','eventorganiser') . '</p>' .
			'<ul>' .
				'<li>' . __('Specify how regularly the event should repeat (default 1)','eventorganiser') . '</li>' .
				'<li>' . __('Choose the reoccurrence end date. No further occurrences are added after this date, but an occurrence that starts before may finish after this date.','eventorganiser') . '</li>' .
				'<li>' . __('If monthly reoccurrence is selected, select whether this should repeat on that date of the month (e.g. on the 24th) or on the day of the month (e.g. on the third Tuesday) ','eventorganiser') . '</li>' .
				'<li>' . __('If weekly reoccurrence is selected, select which days of the week the event should be repeated. If no days are selected, the day of the start date is used','eventorganiser') . '</li>' .
			'</ul>'
				));
			    $screen->add_help_tab( array(
			        'id'      => 'selecting-venues', 
			        'title'   => __('Selecting a venue','eventorganiser'),
        			'content' => '<p>' . __('Selecting a venue','eventorganiser') . '</p>' .
					'<ul>' .
						'<li>' . __('Use the venues input field to search for existing venues','eventorganiser') . '</li>' .
						'<li>' . __('Only pre-existing venues can be selected. To add a venue, go to the venues page.','eventorganiser') . '</li>' .
					'</ul>'
				));
			break;

		//Add help for event admin table page
		case ('edit-event'):

			$screen->add_help_tab( array(
				'id'=>'overview',
			        'title'   => __('Overview'),
				'content'=>'<p>' . __('This is the list of all saved events. Note that <strong> reoccurring events appear as a single row </strong> in the table and the start and end date refers to the first occurrence of that event.','eventorganiser') . '</p>' ));
			break;

		//Add help for venue admin table page
		case ('event_page_venues'):
			$contextual_help = 
			'<p>' . __("Hovering over a row in the venues list will display action links that allow you to manage that venue. You can perform the following actions:",'eventorganiser') . '</p>' .
			'<ul>' .
				'<li>' . __('Edit takes you to the editing screen for that venue. You can also reach that screen by clicking on the venue title.','eventorganiser') . '</li>' .
				'<li>' . __('Delete will permanently remove the venue','eventorganiser') . '</li>' .
				'<li>' . __("View will take you to the venue's page",'eventorganiser') . '</li>' .
			'</ul>';
			break;

		//Add help for calendar view
		case ('event_page_calendar'):
			$screen->add_help_tab( array(
				'id'=>'overview',
				'title'=>__('Overview'),
				'content'=>'<p>' . __("This page shows all (occurrances of) events. You can view the summary of an event by clicking on it. If you have the necessary permissions, a link to the event's edit page will appear also.",'eventorganiser'). '</p>' .
			'<p>' . __("By clicking the relevant tab, you can view events in Month, Week or Day mode. You can also filter the events by events by category and venue. The 'go to date' button allows you to quickly jump to a specific date.",'eventorganiser'). '</p>' 
			));
			$screen->add_help_tab( array(
				'id'=>'add-event',
				'title'=>__('Add Event','eventorganiser'),
				'content'=>'<p>' . __("You can create an event on this Calendar, by clicking on day or dragging over multiple days (in Month view) or multiple times (in Week and Day view). You can give the event a title, specify a venue and provide a descripton. The event can be immediately published or saved as a draft. In any case, the event is created and you are forwarded to that event's edit page.",'eventorganiser') . '</p>' ));
			break;
	endswitch;

	//Add a link to Event Organiser documentation on every EO page
	$screen->set_help_sidebar( 
		'<p> <strong>'. __('For more information','eventorganiser').'</strong></br>'
			.sprintf(__('See the <a %s> documentation</a>','eventorganiser'),'target="_blank" href="http://docs.wp-event-organiser.com/"').'</p>' 
			.sprintf('<p><strong><a href="%s">%s</a></strong></p>', admin_url('edit.php?post_type=event&page=debug'),__('Debugging Event Organiser','eventorganiser' ) )
			.sprintf('<p><strong><a href="%s">%s</a></strong></p>', admin_url('index.php?page=eo-pro'),__('Go Pro!','eventorganiser'))
	);

	return $contextual_help;
}
add_action( 'contextual_help', 'eventorganiser_cpt_help_text', 10, 3 );

/*
* The following adds the ability to associate a colour with an event-category.
* Currently stores data in the options table/
* If Taxonomy meta table becomes core, then these options will be migrated there.
*/

/**
 * Enqueue the javascript necessary for colour-picker on category pages.
 * Hooked onto admin_menu. Why?
 *
 * @ignore
 * @access private
 * @since 1.3
 */
function eventorganiser_colour_scripts() {
    wp_enqueue_style( 'farbtastic' );
    wp_enqueue_script( 'farbtastic' );
    wp_enqueue_script( 'jQuery' );
}
add_action( 'admin_menu', 'eventorganiser_colour_scripts' );


/**
 * When a category is created/updated save its color
 * Hooked onto created_event-category and edited_event-category
 *
 * @ignore
 * @access private
 * @since 1.3
 */
function eventorganiser_save_event_cat_meta( $term_id ) {
	if ( isset( $_POST['eo_term_meta'] ) ):
		$term_meta = get_option( "eo-event-category_$term_id");
		$cat_keys = array_keys($_POST['eo_term_meta']);

		foreach ($cat_keys as $key):
			if (isset($_POST['eo_term_meta'][$key]))
				$term_meta[$key] = $_POST['eo_term_meta'][$key];
		endforeach;

	        //save the option array
	        update_option( "eo-event-category_$term_id", $term_meta );
	endif;
}
add_action('created_event-category', 'eventorganiser_save_event_cat_meta', 10, 2);
add_action( 'edited_event-category', 'eventorganiser_save_event_cat_meta', 10, 2);

/**
 * When a category is deleted, delete the saved colour (saved in options table).
 * Hooked onto delete_event-category
 *
 * @ignore
 * @access private
 * @since 1.3
 */
function eventorganiser_tax_term_deleted($term_id, $tt_id){
	//Delete taxonomies meta
	delete_option('eo-event-category_'.$term_id);
}
add_action('delete_event-category','eventorganiser_tax_term_deleted',10,2);

/**
 * Add the colour picker forms to main taxonomy page: (This one needs stuff wrapped in Divs)
 * uses eventorganiser_tax_meta_form to display the guts of the form.
 * Hooked onto event-category_add_form_fields
 * @uses eventorganiser_tax_meta_form 
 *
 * @ignore
 * @access private
 * @since 1.3
 */
function eventorganiser_add_tax_meta($taxonomy){
	?>
	<div class="form-field"><?php eventorganiser_tax_meta_form('');?></div>
	<p> &nbsp; </br>&nbsp; </p>
<?php
}
add_action('event-category_add_form_fields', 'eventorganiser_add_tax_meta',10,1);


/**
 * Add the colour picker forms to taxonomy-edit page: (This one needs stuff wrapped in rows)
 * uses eventorganiser_tax_meta_form to display the guts of the form.
 * Hooked onto event-category_edit_form_fields
 * @uses eventorganiser_tax_meta_form
 *
 * @ignore
 * @access private
 * @since 1.3
 */
function eventorganiser_edit_tax_meta($term,$taxonomy){
	//Check for existing data
	$term_meta = get_option( "eo-event-category_$term->term_id");
	$colour = (!empty($term_meta) && isset($term_meta['colour']) ? $term_meta['colour'] : '');
	?>
	<tr class="form-field"><?php eventorganiser_tax_meta_form($colour);?></tr>
<?php
}
add_action( 'event-category_edit_form_fields', 'eventorganiser_edit_tax_meta', 10, 2);

/**
 * Displays the guts of the taxonomy-meta form (i.e. colour picker);
 *
 * @ignore
 * @access private
 * @since 1.3
 */
function eventorganiser_tax_meta_form($colour){
	?>
		<th>
			<label for="tag-description"><?php _e('Color','eventorganiser')?></label>
		</th>
		<td> 
			<input type="text" style="width:100px" name="eo_term_meta[colour]" class="color colour-input" id="color" value="<?php echo $colour; ?>" />
			<a id="link-color-example" class="color eo-event-category-color-sample hide-if-no-js"></a>
   			 <div style="z-index: 100; background: none repeat scroll 0% 0% rgb(238, 238, 238); border: 1px solid rgb(204, 204, 204); position: absolute;display: none;" id="colorpicker"></div>
			<p><?php _e('Assign the category a colour.','eventorganiser')?></p>
		</td>
	<script>
var farbtastic;(function($){var pickColor=function(a){farbtastic.setColor(a);$('.colour-input').val(a);$('a.color').css('background-color',a)};$(document).ready(function(){farbtastic=$.farbtastic('#colorpicker',pickColor);pickColor($('.colour-input').val());$('.color').click(function(e){e.preventDefault();if($('#colorpicker').is(":visible")){$('#colorpicker').hide()}else{$('#colorpicker').show()}});$('.colour-input').keyup(function(){var a=$('.colour-input').val(),b=a;a=a.replace(/[^a-fA-F0-9]/,'');if('#'+a!==b)$('.colour-input').val(a);if(a.length===3||a.length===6)pickColor('#'+a)});$(document).mousedown(function(){$('#colorpicker').hide()})})})(jQuery);
	</script>	
<?php
}


/**
 * Add a "Color" column to the Event Categories table.
 */
function eventorganiser_add_color_column_header( $columns ) {
	// Insert the Color column before the Events ("posts") column.
	$offset = array_search( 'posts', array_keys( $columns ) );
	return array_merge (
			array_slice( $columns, 0, $offset ),
			array( 'event-color' => esc_html__( 'Color', 'eventorganiser' ) ),
			array_slice( $columns, $offset, null )
		);
}
add_filter( 'manage_edit-event-category_columns', 'eventorganiser_add_color_column_header' );  


/**
 * Add a box with the color of the current row's event category.
 */
function eventorganiser_add_color_column_data( $html, $column, $term_id ) {
	$term = get_term( $term_id, 'event-category' );
	if( $column == 'event-color'){
		$html = sprintf(
					'<a class="eo-event-category-color-sample" style="background-color: %s;"></a>',
					esc_attr( eo_get_category_meta( $term, 'color' ) )
				);
	}
	return $html;
}
add_filter( 'manage_event-category_custom_column', 'eventorganiser_add_color_column_data', 10, 3 );

/**
 * Prints styling to event category admin pages
 */
function eventorganiser_print_event_cat_admin_styles(){
	?>
	<style>
	/* Category amin page */
	.eo-event-category-color-sample{ border: 1px solid #DFDFDF;border-radius: 4px;margin: 0 7px 0 3px;padding: 4px 14px;line-height: 25px;}
	th.column-event-color{ width:10%}
	</style>
	<?php
}
add_action( 'admin_print_styles-edit-tags.php', 'eventorganiser_print_event_cat_admin_styles' );
/**
 * Add the colour of the category to the term object.
 * Hooked onto get_event-category
 *
 * @ignore
 * @access private
 * @since 1.3
 */
function eventorganiser_append_cat_meta($term){
	if($term):
		$term_meta = get_option( "eo-event-category_{$term->term_id}");
		$colour = (isset($term_meta['colour']) ? $term_meta['colour'] : '');
		$term->color = $colour;
	endif;
	return $term;
}
add_filter('get_event-category','eventorganiser_append_cat_meta');


/**
 * Add the colour of the category to the term object.
 * Hooked onto get_event-category
 *
 * @ignore
 * @access private
 * @since 1.3
 */
function eventorganiser_get_terms_meta($terms){
	if($terms):
		foreach($terms as $term):
			if(isset($term->taxonomy) && $term->taxonomy=='event-category'){
				$term_meta = get_option( "eo-event-category_{$term->term_id}");
				$colour = (isset($term_meta['colour']) ? $term_meta['colour'] : '');
				$term->color = $colour;
			}	
		endforeach;
	endif;
	return $terms;
}
add_filter('get_terms','eventorganiser_get_terms_meta');
add_filter('get_the_terms','eventorganiser_get_terms_meta');


/**
 * Retrieve a category term's colour.
 *
 * @since 1.3
 * @param term|slug $term The event category term object, or slug. Can be empty to get colour of term being viewed.
 * @return string The event category colour in Hex format
 */
function eo_get_category_meta($term='',$key=''){
	if( $key != 'color' )
		return false;

	if (is_object($term)){
		if(isset($term->color))
			return $term->color;
		else
			$term = $term->slug;
	}

	if( !empty($term) ){
		$term = get_term_by('slug', $term,'event-category');
		if( isset($term->color))
			return $term->color;

	}elseif( is_tax('event-category') ){
		$term = get_queried_object();
		$term = $term->term_id;
		$term = get_term( $term, 'event-category' );
		if( isset($term->color))
			return $term->color;
	}
	
	return false;
}

/**
 * Adds custom tables to $wpdb;
 *
 * @ignore
 * @access private
 * @since 1.5
 */
function eventorganiser_wpdb_fix(){
	global $wpdb;
	$wpdb->eo_venuemeta = "{$wpdb->prefix}eo_venuemeta";
	$wpdb->eo_events = "{$wpdb->prefix}eo_events";
}
add_action( 'init', 'eventorganiser_wpdb_fix',1);
add_action( 'switch_blog', 'eventorganiser_wpdb_fix');
	

/**
 * Updates venue meta cache when an event's venue is retrieved..
 * Hooked onto wp_get_object_terms
 *
 * @ignore
 * @access private
 * @since 1.5
 */
function _eventorganiser_get_event_venue($terms, $post_ids,$taxonomies,$args){
	//Passes taxonomies as a string inside quotes...
	$taxonomies = explode(',',trim($taxonomies,"\x22\x27"));
	return eventorganiser_update_venue_meta_cache( $terms, $taxonomies);
}
add_filter('wp_get_object_terms','_eventorganiser_get_event_venue',10,4);


/**
 * Updates venue meta cache when event venues are retrieved.
 *
 * For backwards compatibility it adds the venue details to the taxonomy terms.
 * Hooked onto get_terms and get_event-venue
 *
 * @ignore
 * @access private
 * @since 1.5
 *
 * @param array $terms Array of terms,
 * @param string $tax Should be (an array containing) 'event-venue'.
 * @param array  Array of event-venue terms,
 */
function eventorganiser_update_venue_meta_cache( $terms, $tax){

		if( is_array($tax) && !in_array('event-venue',$tax) ){
			return $terms;
		}
		if( !is_array($tax) && $tax != 'event-venue'){
			return $terms;
		}

		$single = false;
		if( ! is_array($terms) ){
			$single = true;
			$terms = array( $terms );
		}

		if( empty($terms) )
		       return $terms;

		//Check if its array of terms or term IDs
		$first_element = reset( $terms );
		if ( is_object( $first_element ) ){
			$term_ids = wp_list_pluck( $terms, 'term_id' );
		} else {
			$term_ids = $terms;
		}

   		update_meta_cache('eo_venue',$term_ids);

		//Backwards compatible. Depreciated - use the functions, not properties.
		foreach ($terms as $term){
			if( !is_object($term) )
				continue;
			$term_id = (int) $term->term_id;

			if( !isset($term->venue_address) ){
				$address = eo_get_venue_address($term_id);
				foreach( $address as $key => $value )
					$term->{'venue_'.$key} = $value;
			}

			if( !isset($term->venue_lat) || !isset($term->venue_lng) ){
				$term->venue_lat =  number_format(floatval(eo_get_venue_lat($term_id)), 6);
				$term->venue_lng =  number_format(floatval(eo_get_venue_lng($term_id)), 6);
			}

		}
		
		if( $single ) return $terms[0];

		return $terms;
	} 
add_filter('get_terms','eventorganiser_update_venue_meta_cache',10,2);
add_filter('get_event-venue','eventorganiser_update_venue_meta_cache',10,2);



/**
 * Allows event-venue terms to be sorted by address, city, state, country, or postcode (on venue admin table)
 * Hooked onto terms_clauses
 *
 * @ignore
 * @access private
 * @since 1.5
 */
function eventorganiser_join_venue_meta($pieces,$taxonomies,$args){
	global $wpdb;

	if( ! in_array('event-venue',$taxonomies) )
		return $pieces;

	/* Order by */
	$address_keys = array_keys(_eventorganiser_get_venue_address_fields());
	if( in_array('_'.$args['orderby'], $address_keys) )
		$meta_key ='_'.$args['orderby'];
	else
		$meta_key = false;

	if(false === $meta_key)
		return $pieces;

	$sql = get_meta_sql(array(array('key'=>$meta_key)), 'eo_venue', 't', 'term_id');

	$pieces['join'] .= $sql['join'];
	$pieces['where'] .= $sql['where'];
	$pieces['orderby'] = "ORDER BY {$wpdb->eo_venuemeta}.meta_value";
	return $pieces;
}
add_filter('terms_clauses', 'eventorganiser_join_venue_meta',10,3);

/**
 * Filters to the edit venue term link so that points to the correct place
 * Hooked onto get_edit_term_link
 *
 * @ignore
 * @access private
 * @since 1.5
 */
function eventorganiser_edit_venue_link($link, $term_id, $taxonomy){

	if( $taxonomy != 'event-venue' )
        	return $link;

	$tax = get_taxonomy( $taxonomy );
	if ( !current_user_can( $tax->cap->edit_terms ) )
			return;

	$venue = get_term($term_id, $taxonomy);

	$link = add_query_arg(array(
			'page'=>'venues',
			'action'=>'edit',
			'event-venue'=> $venue->slug,
	), admin_url('edit.php?post_type=event'));

	return $link;
}
add_filter('get_edit_term_link','eventorganiser_edit_venue_link',10,3);


/*
 * A walker class to use that extends wp_dropdown_categories and allows it to use the term's slug as a value rather than ID.
*
* See https://core.trac.wordpress.org/ticket/13258
*
* Usage, as normal:
* wp_dropdown_categories($args);
*
* But specify the custom walker class, and (optionally) a 'id' or 'slug' for the 'value' parameter:
* $args=array('walker'=> new EO_Walker_TaxonomyDropdown(), 'value'=>'slug', .... );
* wp_dropdown_categories($args);
*
* If the 'value' parameter is not set it will use term ID for categories, and the term's slug for other taxonomies in the value attribute of the term's <option>.
*/

class EO_Walker_TaxonomyDropdown extends Walker_CategoryDropdown{

	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		$pad = str_repeat('&nbsp;', $depth * 3);
		/**
		 * @ignore
		 */
		$cat_name = apply_filters('list_cats', $category->name, $category);

		if( !isset($args['value']) ){
			$args['value'] = ( $category->taxonomy != 'category' ? 'slug' : 'id' );
		}

		$value = ($args['value']=='slug' ? $category->slug : $category->term_id );

		$output .= "\t<option class=\"level-$depth\" value=\"".$value."\"";
		if ( $value === (string) $args['selected'] ){
			$output .= ' selected="selected"';
		}
		$output .= '>';
		$output .= $pad.$cat_name;
		if ( $args['show_count'] )
			$output .= '&nbsp;&nbsp;('. $category->count .')';

		$output .= "</option>\n";
	}

}

/**
 * For this to work you need to add the following to the custom field exceptions on the ThreeWP settings page:
 *
 * _eventorganiser_event_schedule
 * _eventorganiser_schedule_start_start
 * _eventorganiser_schedule_start_finish
 *_eventorganiser_schedule_last_start
 *_eventorganiser_schedule_last_finish
 *
 * @access private
 * @ignore
 *
*/
function eventorganiser_threeWP( $activity ){
	

	if( isset( $activity['activity_id'] ) && $activity['activity_id'] == '3broadcast_broadcasted' && 'event' == get_post_type( get_the_ID() ) ){

		$details = $activity['activity_details'];
  		$current_blog_id = get_current_blog_id();
		$original_id = get_the_ID();
		$original_cats = get_the_terms( $original_id, 'event-category' );
		$original_venue_id = eo_get_venue( $original_id );

		//Venue Meta &Thumbnail
		$venue_meta =  eo_get_venue_meta( $original_venue_id, '', false );
		if( $original_venue_id && $venue_thumbnail_id = eo_get_venue_meta( $original_venue_id, '_eventorganiser_thumbnail_id', true ) ){

			$original_upload_dir = wp_upload_dir();
			$metadata = wp_get_attachment_metadata( $venue_thumbnail_id );
			$data = get_post( $venue_thumbnail_id );
			$file = $metadata['file'];
			$guid = $data->guid;
			$post_title = $data->post_title;
			$menu_order = $data->menu_order;
			$post_excerpt = $data->post_excerpt;
			$filename_base = basename( $metadata['file'] );
			$filename_path = $original_upload_dir[ 'basedir' ] . '/' . $metadata[ 'file' ];

			$venue_thumbnail = compact( 'filename_path' , 'file', 'filename_base', 'post_title', 'guid', 'menu_order', 'post_excerpt' );
			unset( $venue_meta['_eventorganiser_thumbnail_id'] );
		}
	
		foreach( $details as $broadcast ){
			$blog_id = $broadcast['blog_id'];
			$post_id = $broadcast['post_id'];
			switch_to_blog( $blog_id );
			
			$event_data = array ( 'force_regenerate_dates' => true );
			eo_update_event($post_id, $event_data );

			$venue_id = eo_get_venue( $post_id );
			//Delete old venue meta
			if( $old_meta =  eo_get_venue_meta( $venue_id, '', false ) ){
				$old_meta_keys = array_keys( $old_meta );
				foreach( $old_meta_keys as $key ){
					eo_delete_venue_meta( $venue_id, $key );
				}
			}
			//Add new venue  meta
			foreach( $venue_meta as $key => $values ){
				foreach( $values as $value ){
					eo_add_venue_meta( $venue_id, $key, $value );
				}
			}

			//Sync cat colours
			$cats = get_the_terms( $post_id, 'event-category' );
			foreach( $cats as $cat ){
				//Find original cat
				foreach( $original_cats as $original_cat ){
					if( $original_cat->slug == $cat->slug ){
						$re = update_option( "eo-event-category_{$cat->term_id}", array( 'colour' => $original_cat->color ) );
						break;
					}
				}
			}

			//Sync venue thumbnails
			$upload_dir = wp_upload_dir();
			if ( file_exists( $venue_thumbnail['filename_path'] ) ){
				$file_path =  $upload_dir['path'] . '/' . $venue_thumbnail['filename_base'];

				// Copy the file to the blog's upload directory
				copy( $venue_thumbnail['filename_path'], $file_path );

				if(  false == ( $attachment_id = eventorganiser_file_to_attachment( $venue_thumbnail['file'] ) ) ){

					// And now create the attachment stuff.
					// This is taken almost directly from https://codex.wordpress.org/Function_Reference/wp_insert_attachment
					$wp_filetype = wp_check_filetype( $venue_thumbnail['filename_base'], null );
					$attachment = array(
						'guid' => $upload_dir['url'] . '/' . $venue_thumbnail['filename_base'],
						'menu_order' => $venue_thumbnail['menu_order'],
						'post_excerpt' =>$venue_thumbnail['post_excerpt'],
						'post_mime_type' => $wp_filetype['type'],
						'post_title' => $venue_thumbnail['post_title'],
						'post_status' => 'inherit',
					);
					$attachment_id = wp_insert_attachment( $attachment, $file_path, 0 );
		
					// Now to handle the metadata.
					require_once(ABSPATH . "wp-admin" . '/includes/image.php' );
					$attach_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
					wp_update_attachment_metadata( $attachment_id,  $attach_data );
				}//If attachment post doesn't exist

				eo_update_venue_meta( $venue_id, '_eventorganiser_thumbnail_id', $attachment_id );
			}//If original file exists
			
		}//Foreach blog
		switch_to_blog( $current_blog_id );
	}//If broadcasting

}


function eventorganiser_file_to_attachment( $file ){

	$attachments = get_posts( array(
		'numberposts' => 1,
		'post_type'  => 'attachment',
            	'fields'     => 'ids',
            	'meta_query' => array(
                	array(
                    		'value'   => $file,
                    		'key' => '_wp_attached_file',
                	),
            	)
        ));
    	if( $attachments )
		return intval( $attachments[0] );
	else
		return false;
}

add_action( 'threewp_activity_monitor_new_activity', 'eventorganiser_threeWP' );


function eventorganiser_event_shortlink( $shortlink, $id, $context ) {

	//Context can be post/blog/meta ID
	$event_id = 0;
	if( 'query' == $context && is_singular( 'event' ) ){
		$event_id = get_queried_object_id();
	}elseif( 'post' == $context ){
		$event_id = $id;
	}

	//Only do something if of event post type
	if( 'event' == get_post_type( $event_id )  ){
		$shortlink = home_url( '?p=' . $event_id );
	}
	
	return $shortlink;
}
add_filter( 'pre_get_shortlink', 'eventorganiser_event_shortlink', 10, 3 );


function _eventorganiser_add_venue_admin_bar_edit_menu( ){
	global $wp_admin_bar;

	if ( is_admin() ) {
		$current_screen = get_current_screen();

		if ( 'event_page_venues' == $current_screen->base
				&& isset( $_GET['action'] ) && 'edit' == $_GET['action']
				&& ( $tax = get_taxonomy( 'event-venue' ) )
				&& $tax->public )
		{
			$wp_admin_bar->add_menu( array(
					'id' => 'view',
					'title' => $tax->labels->view_item,
					'href' => eo_get_venue_link( $_GET['event-venue'] )
			) );
		}
	} else {
		$current_object = get_queried_object();

		if ( !eo_is_venue() )
			return;

		if ( ( $tax = get_taxonomy( $current_object->taxonomy ) )
				&& current_user_can( $tax->cap->edit_terms ) )
		{
			$wp_admin_bar->add_menu( array(
					'id' => 'edit',
					'title' => $tax->labels->edit_item,
					'href' => get_edit_term_link( $current_object->term_id, $current_object->taxonomy )
			) );
		}
	}
}
add_action( 'admin_bar_menu', '_eventorganiser_add_venue_admin_bar_edit_menu', 80 );
?>
