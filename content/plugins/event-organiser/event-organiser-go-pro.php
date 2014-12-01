<?php
/**
 * Calendar Admin Page
 */
if(!class_exists('EventOrganiser_Admin_Page')){
    require_once(EVENT_ORGANISER_DIR.'classes/class-eventorganiser-admin-page.php' );
}
/**
 * Calendar Admin Page
 * 
 * Extends the EentOrganiser_Admin_Page class. Creates the calendar admin page
 * @version 1.0
 * @see EventOrganiser_Admin_Page
 * @package event organiser
 * @ignore
 */
class EventOrganiser_Pro_Page extends EventOrganiser_Admin_Page
{
    /**
     * This sets the calendar page variables
     */
	function set_constants(){
		$this->hook = 'edit.php?post_type=event';
		$this->title =  __('Get Event Organiser Pro Add-On','eventorganiser');
		$this->menu =__('Go Pro','eventorganiser');
		$this->permissions ='manage_options';
		$this->slug ='eo-pro';
	}
      
	function add_page(){
		$this->page = add_dashboard_page($this->title, $this->menu, $this->permissions,$this->slug,  array($this,'render_page'),10);
		add_action('load-' . $this->page,  array($this,'page_actions'),9);
		add_action('admin_print_scripts-' . $this->page,  array($this,'page_styles'),10);
		add_action('admin_print_styles-' . $this->page,  array($this,'page_scripts'),10);
		add_action("admin_footer-" . $this->page, array($this,'footer_scripts'));
		remove_submenu_page('index.php',$this->slug);
	}

    /**
     * Enqueues the page's scripts and styles, and localises them.
     */
	function page_scripts(){
	}

	
	function display(){
		$plugins = get_plugins();
		$plugin = $plugins['event-organiser/event-organiser.php'];
	?>
		<div class="wrap">  
			<div class="wrap about-wrap">
				<h1> <?php printf(__('Get Event Organiser Pro', 'eventorganiser'), $plugin['Version']); ?> </h1>

			<div class="about-text"><?php 
				echo '<p>'.__( 'Event Organiser Pro is a premium add-on bringing advanced booking management to Event Organiser.', 'eventorganiser' );
    			echo '<p>'.__( "But that's not all &hellip;", 'eventorganiser' ); ?>
			</div>

			<div class="event-organiser-logo" style="background: url('<?php echo EVENT_ORGANISER_URL.'css/images/eobadge.png';?>');padding-top: 150px;height: 52px;width: 185px;color: #666;font-weight: bold;font-size: 14px;text-align: center;text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);margin: 0 -5px;position: absolute;top: 0;right: 0;"></div>

			<hr style="color:#CCC;background-color:#CCC;border:0;border-bottom:1px solid #CCC;">
			
			<style>
			.eo-feature-section {float: left;margin: 2%;width: 29%;}
			.eo-feature-section img{border: 1px #CCC solid;-webkit-box-shadow: 0 1px 3px rgba( 0, 0, 0, 0.3 );box-shadow: 0 1px 3px rgba( 0, 0, 0, 0.3 );}
			</style>
			
			<?php 
			
			self::print_feature( 
				__( 'Flexible Booking Options', 'eventorganiser' ), 
				__('Sell tickets for specific dates or sell tickets for all dates of an event - such as booking places on a course. You can offer multiple tickets, and customise the booking form to suit your needs.', 'eventorganiser'),
				'eo-pro-ticket-picker.png'
			); 
			self::print_feature( 
				__( 'Additional shortcodes & improved UI', 'eventorganiser' ), 
				__( 'Give your users the ability to search and filter through your events with the event search shortcode. Event Organiser Pro also adds a text editor button to make inserting and configuring your shortcodes that bit easier. ', 'eventorganiser' ),
				'eo-pro-event-search.png'
			); 
			self::print_feature(
				__( 'Venue custom fields & thumbnails', 'eventorganiser' ),
				__( "Add information on your venue pages with venue custom fields, or give your venues more attentioned with their own 'featured image'", 'eventorganiser' ),
				'eo-pro-venue-cf.png'
			);
			?>
			
			<div style="clear:both"></div>

			<p>
			<strong><a href="http://wp-event-organiser.com/pro-features?aid=7"><?php _e('Find out more &hellip;', 'eventorganiser')?></a></strong>
			</p>
			
			<hr style="color:#CCC;background-color:#CCC;border:0;border-bottom:1px solid #CCC;">
			
			<div class="return-to-dashboard">
				<a href="<?php echo admin_url('options-general.php?page=event-settings');?>"><?php _e('Go to Event Organiser settings', 'eventorganiser');?></a>
			</div>
		</div>
		</div><!-- .wrap -->
<?php
	}
	
	static function print_feature( $title, $content, $img ){
		?>
		<div class="eo-feature-section images-stagger-right">
			<img src="<?php echo EVENT_ORGANISER_URL.'css/images/'.$img?>">
			<?php echo '<h4>'.$title.'</h4>'; ?>
			<p>	<?php echo $content; ?></p>
		</div>
		<?php 
	}
	
}
$calendar_page = new EventOrganiser_Pro_Page();

?>
