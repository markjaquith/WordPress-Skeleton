<?php
/**
 * Addon Admin Page
 * Inspired, and based on, Easy Digital Download's add-on page (by Pippin Williamson)
 */
if ( ! class_exists( 'EventOrganiser_Admin_Page' ) ){
	require_once(EVENT_ORGANISER_DIR.'classes/class-eventorganiser-admin-page.php' );
}
/**
 * @ignore
 */
class EventOrganiser_Add_Ons_Page extends EventOrganiser_Admin_Page
{
	/**
     * This sets the calendar page variables
     */
	function set_constants(){
		$this->hook = 'edit.php?post_type=event';
		$this->title = __( 'Event Organiser Extensions', 'eventorganiser' );
		$this->menu = __( 'Extensions','eventorganiser' );
		$this->permissions = 'manage_options';
		$this->slug = 'eo-addons';
	}
	
	function add_page(){
		$this->page = add_submenu_page( $this->hook, $this->title, $this->menu, $this->permissions, $this->slug, array( $this, 'render_page' ), 10 );
		add_action( 'load-'.$this->page, array( $this, 'page_actions' ), 9 );
		add_action( 'admin_print_scripts-'.$this->page, array( $this, 'page_styles' ), 10 );
		add_action( 'admin_print_styles-'.$this->page, array( $this, 'page_scripts' ), 10 );
		
		
		if ( eventorganiser_get_option( 'hide_addon_page' ) )
			remove_submenu_page( 'edit.php?post_type=event', $this->slug );
	}

	function page_actions(){
		//Fetch addons
		add_action( 'admin_footer', array( $this, 'footer_scripts' ) );		
		$addons = self::get_addons();
	}
	/**
     * Enqueues the page's scripts and styles, and localises them.
     */
	function footer_scripts(){
		?>
		<script type="text/javascript">
			jQuery('document').ready(function( $ ){		
				jQuery('#eo-submenu-toggle').click( function(){ 
					if ( !jQuery(this).is(':checked') ){
						if( jQuery('#menu-posts-event li.current').length === 0 ){
							 jQuery('#menu-posts-event .wp-submenu').append('<li class="current"><a href="edit.php?post_type=event&amp;page=eo-addons" class="current">Add-ons</a></li>' );
						}
						jQuery('#menu-posts-event li.current').show();
					}else{
						jQuery('#menu-posts-event li.current').hide();
					}
					jQuery.ajax({
						  type: "POST",
						  url: ajaxurl,
						  data: { action: 'eo_toggle_addon_page', hide_addon_page: jQuery(this).is(':checked') }
					});
				});
				$(window).load(function() {
					$("#eo-addons-wrap .eo-addon").height( 'auto' );
					var max = Math.max.apply( Math, $("#eo-addons-wrap .eo-addon").map(function(){return $(this).height();}));
					$("#eo-addons-wrap .eo-addon").height(max);
				});
			});
		</script>
		<?php 
	}
	
	function page_styles(){
		?>
		<style>
		.eo-addon {float: left;margin: 0 5% 5% 5%;background: #f0f0f0;border: 1px solid #ccc;width: 21%;padding: 8px;height: 375px;position: relative;font-size: 13px;border-radius: 3px;}
		.eo-addon .img-wrap{text-align:center;}
		.eo-addon img{text-align:center;width: 90%;margin:auto;border: 3px solid white;box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);-webkit-box-shadow: 0 1px 3px rgba( 0, 0, 0, 0.3 );box-shadow: 0 1px 3px rgba( 0, 0, 0, 0.3 );border-radius: 3px;}
		.eo-addon .button-secondary{bottom: 7px;position: absolute;}
		.eo-addon h3 {margin: 0 0 8px;padding: 0px;font-size: 13px;}
		#eo-addons-wrap{ margin-top: 30px; }
		.eo-addon-text{ color: #777;margin: 1em 200px 1.4em 0;min-height: 60px;font-size: 15px; }
		.eo-coming-soon-text{text-align: center;font-weight: bold;display: block;font-size: 14px;text-transform: uppercase;margin-top: 10px;color: #1E8CBE;text-shadow: 1px 1px #F9F9F9;letter-spacing: 1px;}
		</style>
		<?php
	}

	
	function display(){
		$plugins = get_plugins();
		$plugin = $plugins['event-organiser/event-organiser.php'];
		?>
		<div class="wrap">  
			
			<h2> <?php esc_html_e( 'Event Organiser Extensions', 'eventorganiser' ); ?></h2>

			<div class="eo-addon-text">
				<?php 
					echo '<p>'.__( 'Event Organiser offers a range of extension which add additional features to the plug-in.', 'eventorganiser' ) . '</p>';
					$settings_link = esc_url( admin_url( 'options-general.php?page=event-settings' ) );
				?>
				<label><input type="checkbox" id="eo-submenu-toggle" <?php checked( eventorganiser_get_option( 'hide_addon_page' ), 1 );?>/>
					<small>Hide this page from the admin menu. You can still access it from <a href="<?php echo $settings_link;?>"><em><small>Settings > Event Organiser</small></em></a>.</small> 
				</label>
			</div>

			<hr style="color:#CCC;background-color:#CCC;border:0;border-bottom:1px solid #CCC;">
			<?php 
			$addons = self::get_addons();
			
			if ( $addons && ! is_wp_error( $addons ) ):
				echo '<div id="eo-addons-wrap">';
				foreach ( $addons as $addon ):
					if ( ! isset( $addon['status'] ) || ! in_array( $addon['status'], array( 'available', 'coming-soon' ) ) ){
						continue;	
					}
					self::print_addon( $addon );
				endforeach;
				echo '</div>';
			else:
				?>
				<div class="error"><p>There was an error retrieving the add-on list from the server. Please try again later.</p></div>
				<?php 
			endif;
			?>
			
			<div style="clear:both"></div>

			<p>
			<strong><a href="http://wp-event-organiser.com/extensions?aid=7"><?php _e( 'Find out more &hellip;', 'eventorganiser' )?></a></strong>
			</p>
			
		</div><!-- .wrap -->
		<?php
	}
	
	static function get_addons(){
		
		if ( false === ( $addons = get_transient( 'eventorganiser_add_ons' ) ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
			$addons = wp_remote_get( 'http://wp-event-organiser.com/addons.json', array( 'sslverify' => false ) );
			
			if ( ! is_wp_error( $addons ) ) {
				if ( isset( $addons['body'] ) && strlen( $addons['body'] ) > 0 ) {
					$addons = wp_remote_retrieve_body( $addons );
					set_transient( 'eventorganiser_add_ons', $addons, 24 * 60 * 60 );
				} else {
					return new WP_Error( 'eo-addon-feed', 'Unknown error message' );
				}
			} else {
				return $addons;//Returns WP_Error object
			}
		}

		if ( $addons )
			$addons = json_decode( $addons, true );
		
		return $addons;
	}


	
	static function print_addon( $addon ){
		?>
		<div class="eo-addon">

			<h3 class="eo-addon-title"><?php echo $addon['title']; ?> </h3>
			<div class="img-wrap">
				<?php if ( $addon['url'] ): ?>
				<a href="<?php echo $addon['url'];?>" title="<?php echo $addon['title']; ?>">
				<?php endif; ?>
					<img src="<?php echo $addon['thumbnail'];?>" class="attachment-addon wp-post-image" title="<?php echo $addon['title']; ?>">
				<?php if ( $addon['url'] ): ?>
				</a>
				<?php endif; ?>
			</div>
			<?php if ( 'coming-soon' == $addon['status'] ): ?>
				<span class="eo-coming-soon-text">Coming Soon</span>
			<?php endif; ?>
			
			<p><?php echo $addon['description'];?></p>
			
			<span style="height:20px;display:block"></span>
			<?php if ( $addon['url'] ): ?>
				<a href="<?php echo $addon['url'];?>" title="<?php echo $addon['title']; ?>" class="button-secondary">
					<?php echo ( 'available' == $addon['status'] ) ? 'Get this Add On' : 'Find out more &hellip;'; ?>
				</a>
			<?php endif; ?>
		</div>
		<?php 
	}
	
}
$calendar_page = new EventOrganiser_Add_Ons_Page();
?>