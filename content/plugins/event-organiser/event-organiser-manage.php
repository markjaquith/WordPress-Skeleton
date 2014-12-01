<?php
/**
 * Functions altering the CPT Event table
 *
 * @since 1.0.0
 */

/**
 * Adds custom columns to Event CPT table
 * @since 1.0.0
 * @access private
 * @ignore
 */
function eventorganiser_event_add_columns( $columns ) {

	unset( $columns['date'] );//Unset unnecessary columns

	//Set 'title' column title
	$columns['title'] = __( 'Event', 'eventorganiser' );

	//If displaying 'author', change title
	if ( isset( $columns['author'] ) ){
		$columns['author'] = __( 'Organiser', 'eventorganiser' );
	}
	
	if( isset( $columns['author'] ) && !eo_is_multi_event_organiser() ){
		unset( $columns['author'] );
	}

	if( taxonomy_exists( 'event-venue' ) ){
		$tax = get_taxonomy( 'event-venue' );
		$columns['venue'] = $tax->labels->singular_name;
	}
	
	$columns['eventcategories'] = __( 'Categories' );
	$columns['datestart'] = __( 'Start Date/Time', 'eventorganiser' );
	$columns['dateend'] = __( 'End Date/Time', 'eventorganiser' );
	$columns['reoccurence'] = __( 'Reoccurrence', 'eventorganiser' ); 

	return $columns;
}
add_filter( 'manage_edit-event_columns', 'eventorganiser_event_add_columns' );

/**
 * Registers the custom columns in Event CPT table to be sortable
 * @since 1.0.0
 * @access private
 * @ignore
 */
add_filter( 'manage_edit-event_sortable_columns', 'eventorganiser_event_sortable_columns' );
function eventorganiser_event_sortable_columns( $columns ) {
	$columns['datestart'] = 'eventstart';
	$columns['dateend'] = 'eventend';
	return $columns;
}


/**
 * What to display in custom columns of Event CPT table
 * @since 1.0.0
 * @access private
 * @ignore
 */
add_action( 'manage_event_posts_custom_column', 'eventorganiser_event_fill_columns', 10, 2 );
function eventorganiser_event_fill_columns( $column_name, $id ) {
	global $post;

	$series_id = ( empty( $post->event_id) ? $id :'' );

	$phpFormat = 'M, j Y';
	if ( !eo_is_all_day( $series_id ) )
		$phpFormat .= '\<\/\b\r\>'. get_option( 'time_format' );
	
	switch ( $column_name ) {
		case 'venue':
			$venue_id = eo_get_venue( $post->ID );
			$venue_slug = eo_get_venue_slug( $post->ID );
			
			if( $venue_id ){
				echo '<a href="'. add_query_arg( 'event-venue', $venue_slug ) .'">'.esc_html( eo_get_venue_name( $venue_id ) ) . '</a>';
				echo '<input type="hidden" value="'.$venue_id.'"/>';
			}
			break;

		case 'datestart':
			eo_the_start( $phpFormat, $series_id );
			break;
		
		case 'dateend':
			eo_the_end( $phpFormat, $series_id );
			break;

		case 'reoccurence':
			eo_display_reoccurence( $series_id );
			break;

		case 'eventcategories':
		    	$terms = get_the_terms( $post->ID, 'event-category' );
 			
			if ( !empty( $terms) ) {
       	 		foreach ( $terms as $term )
			            $post_terms[] = '<a href="'.add_query_arg( 'event-category', $term->slug ).'">'.esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'event-category', 'display' ) ).'</a>';
			        echo join( ', ', $post_terms );
			}
			break;

	default:
		break;
	} // end switch
}

/**
 * Adds a drop-down filter to the Event CPT table by category
 * @since 1.0.0
 */
add_action( 'restrict_manage_posts', 'eventorganiser_restrict_events_by_category' );
function eventorganiser_restrict_events_by_category() {

    // only display these taxonomy filters on desired custom post_type listings
    global $typenow, $wp_query;
    if ( $typenow == 'event' && !is_wp_error( wp_count_terms( 'event-category' ) ) && wp_count_terms( 'event-category' ) > 0 ) {
	eo_event_category_dropdown( array( 'hide_empty' => false, 'show_option_all' => __( 'View all categories' ) ) );
    }
}

/**
 * Adds a drop-down filter to the Event CPT table by venue
 * @since 1.0.0
 */
add_action( 'restrict_manage_posts', 'eventorganiser_restrict_events_by_venue' );
function eventorganiser_restrict_events_by_venue() {
	global $typenow;
	
	//Only add if CPT is event
	if ( $typenow == 'event' && taxonomy_exists( 'event-venue' ) && !is_wp_error( wp_count_terms( 'event-category' ) ) && wp_count_terms( 'event-venue' ) > 0  ) {
		$tax = get_taxonomy( 'event-venue' );	
		 eo_event_venue_dropdown( array( 
		 	'hide_empty'      => false, 
		 	'show_option_all' => $tax->labels->view_all_items 
		 ));
	}
}

/**
 * Adds a drop-down filter to the Event CPT table by intervals
 * @since 1.2.0
 */
add_action( 'restrict_manage_posts', 'eventorganiser_display_occurrences' );
function eventorganiser_display_occurrences() {
	global $typenow, $wp_query;
	if ( $typenow == 'event' ):
		$intervals = array(
			'all' => __( 'View all events', 'eventorganiser' ),
			'future' => __( 'Future events', 'eventorganiser' ),
			'expired' => __( 'Expired events', 'eventorganiser' ),
			'P1D' => __( 'Events within 24 hours', 'eventorganiser' ),
			'P1W' => __( 'Events within 1 week', 'eventorganiser' ),
			'P2W' => sprintf( __( 'Events within %d weeks', 'eventorganiser' ), 2 ),
			'P1M' => __( 'Events within 1 month', 'eventorganiser' ),
			'P6M' => sprintf( __( 'Events within %d months', 'eventorganiser' ), 6 ),
			'P1Y' => __( 'Events within 1 year', 'eventorganiser' ),
		);
		$current = ( !empty( $wp_query->query_vars['eo_interval'] ) ? $wp_query->query_vars['eo_interval'] : 'all' );	
?>
		<select style="width:150px;" name='eo_interval' id='show-events-in-interval' class='postform'>
			<?php foreach ( $intervals as $id => $interval ): ?>
				<option value="<?php echo $id; ?>" <?php selected( $current, $id )?>> <?php echo $interval;?> </option>
			<?php endforeach; ?>
		</select>
<?php
	endif;//End if CPT is event
}


/*
 * Bulk and quick editting of venues. Add drop-down menu for quick editing
 * @Since 1.3
 */
add_action( 'quick_edit_custom_box',  'eventorganiser_quick_edit_box', 10, 2 );
function eventorganiser_quick_edit_box( $column_name, $post_type ) {
	if ( $column_name != 'venue' || $post_type != 'event' ) return;?>

	<fieldset class="inline-edit-col-left"><div class="inline-edit-col">
	<?php wp_nonce_field( 'eventorganiser_event_quick_edit_'.get_current_blog_id(), '_eononce' );?>
		<label class="">
			<span class="title">Event Venue</span><?php
			wp_dropdown_categories( array( 
				'show_option_all' => 'No venue', 
				'orderby' => 'name', 
				'hide_empty' => 0, 
				'name' => 'eo_input[event-venue]', 
				'id' => 'eventorganiser_venue', 
				'taxonomy' => 'event-venue' 
			) ); ?>
	</label>
	</div></fieldset>
	<?php
}

/*
 * Bulk and quick editting of venues. Add drop-down menu for bulk editing
 * @Since 1.3
 */
add_action( 'bulk_edit_custom_box',  'eventorganiser_bulk_edit_box', 10, 2 );
function eventorganiser_bulk_edit_box( $column_name, $post_type ) {
	if ( $column_name != 'venue' || $post_type != 'event' ) return;?>

	<fieldset class="inline-edit-col-left"><div class="inline-edit-col">
	<?php wp_nonce_field( 'eventorganiser_event_quick_edit_'.get_current_blog_id(), '_eononce' );?>
		<label class="">
			<span class="title">Event Venue</span><?php
			$args = array( 'show_option_none' => __( '&mdash; No Change &mdash;' ), 'orderby' => 'name', 'hide_empty' => 0, 'name' => 'eo_input[event-venue]', 'id' => 'eventorganiser_venue_bulk', 'taxonomy' => 'event-venue' );
			 wp_dropdown_categories( $args ); ?>
	</label>
	</div></fieldset>
	<?php
}

/*
 * Bulk and quick editting of venues. Save venue update.
 * @Since 1.3
 */
add_action( 'save_post', 'eventorganiser_quick_edit_save' );
function eventorganiser_quick_edit_save( $post_id ) {
	global $wpdb;

	//make sure data came from our quick/bulk box
	if ( !isset( $_REQUEST['_eononce'] ) || !wp_verify_nonce( $_REQUEST['_eononce'], 'eventorganiser_event_quick_edit_'.get_current_blog_id() ) ) return;
	
	// verify this is not an auto save routine. 
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	//verify this is not a cron job
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) return;

	//authentication checks
	if ( !current_user_can( 'edit_event', $post_id ) ) return;

	$venue_id = ( isset( $_REQUEST['eo_input']['event-venue'] ) ? (int) $_REQUEST['eo_input']['event-venue'] : - 1 );

	if ( $venue_id >= 0 ) {
		$r = wp_set_post_terms( $post_id, array( $venue_id ), 'event-venue', false );
	}

	/**
	 * Triggered after an event has been updated.
	 *
	 * @param int $post_id The ID of the event
	 */
	do_action( 'eventorganiser_save_event', $post_id );
	return;	
}


add_action( 'admin_head-edit.php', 'eventorganiser_quick_edit_script' );
function eventorganiser_quick_edit_script() { ?>
    <script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery( '#the-list' ).on( 'click', 'a.editinline', function() {
			jQuery( '#eventorganiser_venue option' ).attr("selected", false);
			var id = inlineEditPost.getId(this);
			var val = parseInt(jQuery( '#post-' + id + ' td.column-venue input' ).val() );
			jQuery( '#eventorganiser_venue option[value="'+val+'"]' ).attr( 'selected', 'selected' );
        });
    });
    </script>
    <?php
}

?>
