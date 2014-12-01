<?php
/****** VENUE PAGE ******/
if ( !class_exists( 'EventOrganiser_Admin_Page' ) ){
    require_once( EVENT_ORGANISER_DIR.'classes/class-eventorganiser-admin-page.php' );
}

/**
 * @ignore
 */
class EventOrganiser_Venues_Page extends EventOrganiser_Admin_Page
{
	function set_constants(){
		
		$tax = get_taxonomy( 'event-venue' );
		if( $tax ){
			$this->hook        = 'edit.php?post_type=event';
			$this->title       = $tax->labels->menu_name;
			$this->menu        = $tax->labels->menu_name;
			$this->permissions = 'manage_venues';
			$this->slug        = 'venues';

			//Workaround for bug https://core.trac.wordpress.org/ticket/18958
			add_filter( 'set-screen-option', array( $this, 'set_per_page' ),10,3 );
		}
	}
	
	function hooks_init(){
		if( taxonomy_exists( 'event-venue' ) ){
			add_action('admin_init', array($this,'admin_init_actions'));
			add_action('admin_menu', array($this,'add_page'));
		}
	}

	/*
	* Actions to be taken prior to page loading. Hooked on to load-{page}
	*/
	function page_actions(){	

		global $EO_Errors;
		add_action( 'admin_notices',array( $this, 'admin_notices' ) );

		//Determine action if any
		$action = $this->current_action();
		$venue = ( isset( $_REQUEST['event-venue'] ) ? $_REQUEST['event-venue'] : false );

		if ( ( $action && $venue ) || $action == 'add' ):

			if ( !current_user_can( 'manage_venues' ) )
				wp_die( __( 'You do not have permission to manage venues', 'eventorganiser' ) );

			switch( $action ):
				case 'update':
					if( !check_admin_referer( 'eventorganiser_update_venue_'.$venue ) )
						wp_die( __( 'You do not have permission to edit this venue.', 'eventorganiser' ) );

					$venue = get_term_by( 'slug', esc_attr( $venue ), 'event-venue' );

					$return = eo_update_venue( $venue->term_id, $_POST['eo_venue'] );

					if ( is_wp_error( $return ) ){
						$EO_Errors->add( 'eo_error', __( 'Venue <strong>was not</strong> updated', 'eventorganiser' ).': '.$return->get_error_message() );
					} else{
						$term_id = (int) $return['term_id'];
						$venue = get_term( $term_id, 'event-venue' );
						$url = add_query_arg( 
							array(
								'page'        => 'venues',
								'action'      => 'edit',
								'event-venue' => $venue->slug,
								'message'     => 2,
						    ), 
							admin_url( 'edit.php?post_type=event' ) 
						);

						wp_redirect( $url );
						exit();
					}
					break;


				case 'add':
					if( !check_admin_referer( 'eventorganiser_add_venue' ) )
						wp_die( __( 'You do not have permission to edit this venue.', 'eventorganiser' ) );

					$args = $_POST['eo_venue'];
					$name = isset( $args['name'] ) ?  $args['name'] : '';
	
					//Venue may already exist in database since it may have been added via ajax (by pro plug-in);
					if ( !empty( $args['venue_id'] ) && $_venue = eo_get_venue_by( 'id', $args['venue_id'] ) ){
						//Since we're updating, need to explicitly provide slug to update it. Slug will be 'new-venue'.
						$args['slug'] = sanitize_title( $args['name'] );
						$return = eo_update_venue( $_venue->term_id, $args );
					} else{
						$return  = eo_insert_venue( $name, $args );
					}

					if ( is_wp_error( $return ) ){
						$EO_Errors->add( 'eo_error', __( 'Venue <strong>was not</strong> created', 'eventorganiser' ).': '.$return->get_error_message() );
						$_REQUEST['action'] = 'create';
					} else{
						$term_id = (int) $return['term_id'];
						$venue = get_term( $term_id, 'event-venue' );

						$url = add_query_arg(
							array(
								'page'        => 'venues',
								'action'      => 'edit',
								'event-venue' => $venue->slug,
								'message'     => 1,
							), 
							admin_url( 'edit.php?post_type=event' ) 
						);

						wp_redirect( $url );
						exit();
					}
					break;
	

				case 'delete':
					if( is_array( $_REQUEST['event-venue'] ) )
						$nonce = 'bulk-venues';
					else
						$nonce = 'eventorganiser_delete_venue_'.$venue;
	
					if( !check_admin_referer( $nonce ) )
						wp_die( __( 'You do not have permission to delete this venue', 'eventorganiser' ) );

					$venues = (array) $venue;

					//Count the number of deleted venues
					$deleted = 0;

					foreach ( $venues as $venue ):
						$venue = get_term_by( 'slug',esc_attr( $venue ), 'event-venue' );
						$resp = eo_delete_venue( $venue->term_id );

						if ( !is_wp_error( $resp) && true === $resp ){
							$deleted++;
						}
					endforeach;
		
					if ( $deleted > 0 ){
						$url = add_query_arg(
								array(
									'page' => 'venues',
									'message' => 3,
								    ), 
								admin_url( 'edit.php?post_type=event' ) 
						);
						wp_redirect( $url );
						exit();
					} else{
						$EO_Errors = new WP_Error( 'eo_error', __( 'Venue(s) <strong>were not </strong> deleted', 'eventorganiser' ) );
					}
					break;
				endswitch;
		endif;

		$action = $this->current_action();

		if ( in_array( $action, array( 'edit', 'update', 'create' ) ) ){
			$venue = ( isset( $_REQUEST['event-venue'] ) ? $_REQUEST['event-venue'] : false );

			//Venued edit page	
			add_meta_box( 'submitdiv', __( 'Save', 'eventorganiser' ), 'eventorganiser_venue_submit', 'event_page_venues', 'side', 'high' );
			
			/**
 			 * Fires after all built-in meta boxes for venues have been added.
 			 *
 			 * @param Object $venue Venue (term object)
 			 */
			do_action( 'add_meta_boxes_event_page_venues', $venue );
			
			/**
			 * @ignore
			 */
		 	do_action( 'add_meta_boxes', 'event_page_venues', $venue );
		 	
			add_screen_option( 'layout_columns', array( 'max' => 2, 'default' => 2 ) );
		} else{
			//Venue admin list
			require_once( 'classes/class-eo-venue-list-table.php' );
			add_filter( 'manage_event_page_venues_columns', 'eventorganiser_venue_admin_columns' ) ;
			add_screen_option( 'per_page', array( 'option' => 'edit_event_venue_per_page', 'label' => __( 'Venues', 'eventorganiser' ), 'default' => 20 ) );
		}
	}


	function admin_notices(){
		$m = isset( $_GET['message'] ) ? (int) $_GET['message'] : 0;
		$messages = array(
			1 => __( 'Venue <strong>created</strong>', 'eventorganiser' ),
			2 => __( 'Venue <strong>updated</strong>', 'eventorganiser' ),
			3 => __( 'Venue(s) <strong>deleted</strong>', 'eventorganiser' )
		);

		if( isset( $messages[$m]) )
			printf( '<div class="updated"><p>%s</p></div>', $messages[$m] );
	}

	function page_scripts(){
		$action = $this->current_action();
		$screen = get_current_screen();

		if ( in_array( $action,array( 'create', 'edit', 'add', 'update' ) ) ):
			wp_enqueue_script( 'eo-venue-admin' );
			wp_localize_script( 'eo-venue-admin', 'EO_Venue', array( 'location' => get_option( 'timezone_string' ), 'draggable' => true, 'screen_id' => $screen->id) );
			wp_enqueue_style( 'eventorganiser-style' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'postbox' ); 
			add_thickbox();	
		endif;
	}

	function set_per_page( $validated_value, $option, $value ){
		//Workaround for bug https://core.trac.wordpress.org/ticket/18958

		if ( 'edit_event_venue_per_page' != $option )
			return $validated_value;

		$value = (int) $value;
		if ( $value < 1 || $value > 999 )
			return false;
		
		return $value;
	}


	function display(){
		
		$tax    = get_taxonomy( 'event-venue' );
		$action = $this->current_action();
		$venue  = ( isset( $_REQUEST['event-venue'] ) ? $_REQUEST['event-venue'] : false );
	?>
	<div class="wrap">
		<?php screen_icon( 'edit' );?>

		<?php	
			if ( ( ( $action == 'edit' || $action == 'update' ) && $venue )  || $action == 'create' ):
				$this->edit_form( $venue );
	
			else: 

				//Else we are not creating or editing. Display table  
				$venue_table = new EO_Venue_List_Table();
			    	$venue_table->prepare_items();    
	
				//Check if we have searched the venues
				$search_term = ( isset( $_REQUEST['s'] ) ?  esc_attr( $_REQUEST['s']) : '' );?>

				<h2>
					<?php ?>
					<?php echo esc_html( $tax->labels->name ) ?>
					 <a href="edit.php?post_type=event&page=venues&action=create" class="add-new-h2"><?php _ex( 'Add New', 'post' ); ?></a> 
					<?php
					if ( $search_term)
						printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', $search_term ) 
					?>
				</h2>
  
		       	 <form id="eo-venue-table" method="get">
	       		     <!-- Ensure that the form posts back to our current page -->
	       		     <input type="hidden" name="page" value="venues" />
	       		     <input type="hidden" name="post_type" value="event" />
	
	       		     <!-- Now we can render the completed list table -->
	       		     <?php $venue_table->search_box( $tax->labels->search_items, 's' ); ?>
				     <?php $venue_table->display(); ?>
				 </form>
			<?php endif;?>

	</div><!--End .wrap -->
    <?php
	}


/**
 * Display form for creating / editing venues
 *
 * @since 1.0.0
 */
function edit_form( $venue = false ){

	$tax     = get_taxonomy( 'event-venue' ); 
	$venue   = get_term_by( 'slug', $venue, 'event-venue' );
	$term_id = isset( $venue->term_id ) ? (int) $venue->term_id : 0;
	$do      = ( $this->current_action() == 'edit' ? 'update' : 'add' );
	$nonce   = ( $do == 'update' ? 'eventorganiser_update_venue_'.$venue->slug : 'eventorganiser_add_venue' );

	if ( $this->current_action() == 'edit' ) : ?>
		<h2>
			<?php echo esc_html( $tax->labels->edit_item ); ?>
			<a href="edit.php?post_type=event&page=venues&action=create" class="add-new-h2"><?php _ex( 'Add New', 'post' ); ?></a>
		</h2>
	<?php else: ?>
		<h2>
			<?php echo esc_html( $tax->labels->add_new_item ); ?> 
		</h2>
	<?php endif; ?>

	<form name="venuedetails" id="eo_venue_form" method="post" action="<?php echo admin_url( 'edit.php?post_type=event&page=venues' ); ?>">  
		<input type="hidden" name="action" value="<?php echo $do; ?>"> 
		<input type="hidden" id="eo_venue_id" name="eo_venue[venue_id]" value="<?php echo $term_id;?>">  
		<input type="hidden" name="event-venue" value="<?php echo ( isset( $venue->slug ) ? $venue->slug : '' ) ;?>">  

		<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
		<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
		<?php wp_nonce_field( $nonce ); ?>

		<?php 
		//WP3.3-3.3.1 backwards compabt
		if( version_compare( get_bloginfo('version'), 3.4 ) < 0 ) 
			$columns = '1';
		else
			$columns = (1 == get_current_screen()->get_columns() ? '1' : '2' );
		?>
		<div id="poststuff">

			<div id="post-body" class="metabox-holder columns-<?php echo $columns;?>">

				<div id="post-body-content">
					<div id="titlediv"><?php eventorganiser_venue_title( $venue ); ?></div>
					<div class="postbox " id="venue_address">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h3 class="hndle"><span><?php echo esc_html( $tax->labels->venue_location ); ?></span></h3>
						<div class="inside"><?php eventorganiser_venue_location( $venue ); ?></div>
					</div><!-- .postbox -->
					<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="venue_description postarea">
						<?php wp_editor(eo_get_venue_meta($term_id,'_description'), 'content', array( 'textarea_name' => 'eo_venue[description]', 'dfw' => false, 'tabindex' => 7 ) ); ?>
					</div>
		 		</div><!-- #post-body-content -->

				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes( '', 'side', $venue ); ?>
				</div>	

				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( '', 'normal', $venue );  ?>
					<?php do_meta_boxes( '', 'advanced', $venue ); ?>
		 		</div>

		 	</div><!-- #post-body -->
				<br class="clear"> 

		</div><!-- #poststuff -->
	</form> 	
	<?php
	}
}
$venues_page = new EventOrganiser_Venues_Page();


//Helper function to get the 'structure' of the event-venue taxonomy
function eo_get_venue_permastructure(){
	global  $wp_rewrite;
	$termlink = $wp_rewrite->get_extra_permastruct( 'event-venue' );
	if ( empty( $termlink ) ){
		$t = get_taxonomy( 'event-venue' );
		$termlink = "?$t->query_var=";
	} else{
		$termlink = preg_replace( '/%event-venue%/', '', $termlink );
	}
	$termlink = home_url( $termlink );

	return $termlink;
}

//Submit metabox
function eventorganiser_venue_submit( $venue ){
	
	$tax   = get_taxonomy( 'event-venue' );
	$value = $venue ? $tax->labels->update_item : $tax->labels->add_new_item ?>

	<div id="minor-publishing-actions">
		<div id="save-action">
			<p>
				<input type="submit" class="button button-primary" id="save-venue" name="eo_venue[Submit]" value="<?php echo esc_attr( $value ); ?>" tabindex="10" />
			</p>  
		</div>
		<div class="clear"></div>
	</div>

<?php
}

//Location metabox - called directly. Is not movable. 
function eventorganiser_venue_location( $venue ){
	$term_id = isset( $venue->term_id ) ? (int) $venue->term_id : 0;
	$address = eo_get_venue_address( $term_id );?>

	<div class="address-fields">
	
		<table>
			<tbody>
			<?php
			$address_fields = _eventorganiser_get_venue_address_fields();
			foreach ( $address_fields as $key => $label ){
				//Keys are prefixed by '_'.
				$key = trim( $key, '_'  );
				printf(
					'<tr>
						<th><label for="eo-venue-%2$s">%1$s:</label></th>
						<td><input type="text" name="eo_venue[%2$s]" class="eo_addressInput" id="eo-venue-%2$s"  value="%3$s" /></td>
					</tr>',
					$label,
					esc_attr( $key ),
					esc_attr( $address[$key] )
				);
			}
			?>
			</tbody>
		</table>
	
		<?php $latlng = eo_get_venue_latlng( $term_id ); ?>
		<small>
			<?php esc_html_e( 'Latitude/Longitude:', 'eventorganiser' ); ?>
			<span id="eo-venue-latllng-text" 
				data-eo-lat="<?php echo esc_attr( $latlng['lat'] );?>" 
				data-eo-lng="<?php echo esc_attr( $latlng['lng'] );?>" 
				contenteditable="true">
				<?php echo esc_html( implode( ',', $latlng ) );?>
			</span>
		</small>
		
	</div>
	
	<div id="venuemap"></div>
	
	<div class="clear"></div>

	<input type="hidden" name="eo_venue[latitude]" id="eo_venue_Lat"  value="<?php echo esc_attr( eo_get_venue_lat( $term_id ) ); ?>"/>
	<input type="hidden" name="eo_venue[longtitude]" id="eo_venue_Lng"  value="<?php echo esc_attr( eo_get_venue_lng( $term_id ) ); ?>"/>
	<?php
}

//Venue title input 
function eventorganiser_venue_title( $venue ){
	
	$tax     = get_taxonomy( 'event-venue' );
	$term_id = isset( $venue->term_id) ? (int) $venue->term_id : 0;
	$name    = isset( $venue->name ) ? $venue->name : ''; ?>

	<div id="titlewrap">
		<input type="text" placeholder="<?php esc_attr_e( 'Venue name', 'eventorganiser' );?>" autocomplete="off" id="title" value="<?php echo esc_attr( $name );?>" tabindex="1" size="30" name="eo_venue[name]">
	</div>

	<div class="inside">
		<div id="edit-slug-box">
		<?php if ( $venue ): ?>
			<strong><?php _e( 'Permalink:' );?></strong> 
			<span id="sample-permalink">
				<?php echo eo_get_venue_permastructure();?>
				<input type="text" name="eo_venue[slug]"value="<?php echo ( isset( $venue->slug ) ? esc_attr( $venue->slug ) : '' ) ;?>" id="<?php echo $term_id; ?>-slug">
			</span> 
	
			<input type="hidden" value="<?php echo get_term_link( $venue, 'event-venue' ); ?>" id="shortlink">
			<a onclick="prompt( 'URL:', jQuery( '#shortlink' ).val() ); return false;" class="button" href=""><?php _e( 'Get Link', 'eventorganiser' );?></a>	
			<span id='view-post-btn'><a href="<?php echo get_term_link( $venue, 'event-venue' ); ?>" class='button' target='_blank'><?php echo esc_html( $tax->labels->view_item );?></a></span>
		<?php endif;?>					
		</div><!-- #edit-slug-box -->
	</div> <!-- .inside -->
	<?php
}


function eventorganiser_venue_admin_columns( $columns ){
	
	$tax = get_taxonomy( 'event-venue' );
	
	$columns = array(
		'cb'   => '<input type="checkbox" />', //Render a checkbox instead of text
		'name' => esc_html( $tax->labels->singular_name ),
	);
	$address_columns = _eventorganiser_get_venue_address_fields();
	foreach( $address_columns  as $key => $label )
		$columns['venue'.$key] = $label;

	$columns += array(
		'venue_slug' => __( 'Slug' ),
		'posts'      => __( 'Events', 'eventorganiser' ),
	);

	return $columns;	
}
