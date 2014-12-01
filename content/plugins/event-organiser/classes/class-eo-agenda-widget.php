<?php 
/**
 * Class used to create the event calendar widget
 */
class EO_Events_Agenda_Widget extends WP_Widget{
	var $w_arg = array();

	static $agendas=array();

	function __construct() {
		$widget_ops = array('classname' => 'widget_events', 'description' =>  __('Displays a list of events, grouped by date','eventorganiser'));
		$this->w_arg = array(
				'title'=> '',
				'mode'=> 'day',
				'group_format'=>'l, jS F',
				'item_format'=> get_option( 'time_format' ),
				'add_to_google' => 1,
		);
		parent::__construct('EO_Events_Agenda_Widget', __('Events Agenda','eventorganiser'), $widget_ops);
  	}

  	/**
  	 * Registers the widget with the WordPress Widget API.
  	 *
  	 * @return void.
  	 */
  	public static function register() {
  		register_widget( __CLASS__ );
  	}

	function form($instance)  {
		$instance = wp_parse_args( (array) $instance, $this->w_arg );
?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'eventorganiser'); ?>: </label>
		<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']);?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('mode'); ?>"><?php _e('Group by', 'eventorganiser'); ?>: </label>
		<select id="<?php echo $this->get_field_id('mode'); ?>" name="<?php echo $this->get_field_name('mode'); ?>" type="text">
			<option value="day" <?php selected($instance['mode'], ''); ?>><?php _e('Day','eventorganiser'); ?> </option>
			<option value="week" <?php selected($instance['mode'], 'week'); ?>><?php _e('Week','eventorganiser'); ?> </option>
			<option value="month" <?php selected($instance['mode'], 'month'); ?>><?php _e('Month','eventorganiser'); ?> </option>
		</select>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('group_format'); ?>"><?php _e('Group date format', 'eventorganiser'); ?>: </label>
		<input id="<?php echo $this->get_field_id('group_format'); ?>" name="<?php echo $this->get_field_name('group_format'); ?>" type="text" value="<?php echo esc_attr($instance['group_format']);?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('item_format'); ?>"><?php _e('Event date/time format', 'eventorganiser'); ?>: </label>
		<input id="<?php echo $this->get_field_id('item_format'); ?>" name="<?php echo $this->get_field_name('item_format'); ?>" type="text" value="<?php echo esc_attr($instance['item_format']);?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('add_to_google'); ?>"><?php _e('Include \'Add To Google\' link','eventorganiser'); ?>: </label>
		<input id="<?php echo $this->get_field_id('add_to_google'); ?>" name="<?php echo $this->get_field_name('add_to_google'); ?>" type="checkbox" value="1" <?php checked($instance['add_to_google'],1);?>" />
	</p>

  <?php
  }
 

  function update($new_instance, $old_instance){
    	$validated=array();
	delete_transient('eo_widget_agenda');
	$validated['title'] = sanitize_text_field( $new_instance['title'] );
	$validated['mode'] = sanitize_text_field( $new_instance['mode'] );
	$validated['group_format'] = sanitize_text_field( $new_instance['group_format'] );
	$validated['item_format'] = sanitize_text_field( $new_instance['item_format'] );
	$validated['add_to_google'] = intval( $new_instance['add_to_google']);
	return $validated;
    }

 
 
  function widget($args, $instance){
	global $wp_locale;
	wp_enqueue_script( 'eo_front');
	if( !eventorganiser_get_option( 'disable_css' ) )
		wp_enqueue_style( 'eo_front');
	extract($args, EXTR_SKIP);

	add_action('wp_footer', array(__CLASS__, 'add_options_to_script'));
	$id = esc_attr($args['widget_id']).'_container';
	self::$agendas[$id] = array(
		'id'=>esc_attr($args['widget_id']),
		'number'=>$this->number,
		'mode'=> isset($instance['mode']) ? $instance['mode'] : 'day',
		'add_to_google'=>$instance['add_to_google']
	);

	//Echo widget
    	echo $before_widget;

	$widget_title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    	if ( $widget_title )
   		echo $before_title.esc_html($widget_title).$after_title;

	echo "<div style='width:100%' id='{$id}' class='eo-agenda-widget'>";
?>
	<div class='agenda-nav'>
		<span class="next button ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" title="">
			<span class="ui-button-icon-primary ui-icon ui-icon-carat-1-e"></span><span class="ui-button-text"></span>
		</span>
		<span class="prev button ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" title="">
			<span class="ui-button-icon-primary ui-icon ui-icon-carat-1-w"></span><span class="ui-button-text"></span>
		</span>
	</div>
<?php
	echo "<ul class='dates'>";
	echo '</ul>';//End dates
	echo "</div>";
    	echo $after_widget;
  }

	static function add_options_to_script() {
		if(!empty(self::$agendas))
			wp_localize_script( 'eo_front', 'eo_widget_agenda', self::$agendas);	
	}

}
add_action( 'widgets_init', array( 'EO_Events_Agenda_Widget', 'register' ) );