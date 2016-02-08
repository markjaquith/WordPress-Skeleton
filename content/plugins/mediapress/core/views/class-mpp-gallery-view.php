<?php

/**
 * Superclass for all the gallery views
 * A valid gallery view must extend this
 * 
 * A Gallery view supports only one media type but may support one or more componemnt( members/sitewide/groups etc)
 */
class MPP_Gallery_View {

	/**
	 * Unique identifier for this view
	 * Each view must have a unique identifier that identifies it for the given media type uniquely
	 * 
	 * @var string unique identifier 
	 */
	protected $id = '';
	protected $name = '';
	protected $supported_views = array();
	protected $supported_components = array();

	protected function __construct( $args = null ) {
		//let us support all views by default, the child class can explicitly reset it if they want
		$this->supported_views = array( 'shortcode', 'gallery', 'media-list', 'activity' );
		$this->set_supported_components( array( 'sitewide', 'members', 'groups' ) );
	}

	/**
	 * Check if this supports the views for 'widget', 'shortcode', 'gallery', 'media-list', 'activity' etc
	 * 
	 * @param string $view_type one of the 'widget', 'shortcode', 'gallery', 'media-list', 'activity' etc
	 * @return boolean
	 */
	public function supports( $view_type ) {

		if ( in_array( $view_type, $this->supported_views ) ) {
			return true;
		}

		return false;
	}

	public function get_supported_views() {
		return $this->supported_views;
	}

	/**
	 * Does this view supports component
	 * 
	 * @param type $component
	 * @return boolean
	 */
	public function supports_component( $component ) {

		if ( in_array( $component, $this->supported_components ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Rese the list of supported components
	 * 
	 * @param type $components
	 */
	public function set_supported_components( $components ) {
		$this->supported_components = $components;
	}

	/**
	 * Get an array of supported components
	 * 
	 * @return type
	 */
	public function get_supported_components() {
		return $this->supported_components;
	}

	/**
	 * Get unique view id
	 * 
	 * @return string unique view ID
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get human readable name for this view
	 * 
	 * @return type
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Display single gallery media list
	 * 
	 * @param type $gallery
	 */
	public function display( $gallery ) {
		
	}

	/**
	 * Display single gallery settings
	 * 
	 * @param type $gallery
	 */
	public function display_settings( $gallery ) {
		
	}

	/**
	 * Single list of media for the given widget settings
	 * 
	 * @param type $args
	 */
	public function widget( $args = array() ) {
		
	}

	/**
	 * Display widget settings
	 * 
	 */
	public function widget_settings() {
		
	}

	/**
	 * Recieves a widget instance object and returns updated value
	 * 
	 * @param type $instance
	 * @return type
	 */
	public function update_widget_settings( $instance, $old_instance ) {

		return $instance;
	}

	/**
	 * Display media list for the shortcode
	 * @param type $args
	 */
	public function shortcode( $args = array() ) {
		
	}

	public function shrtcode_settings() {
		
	}

	/**
	 * Display the activity attachment list
	 * 
	 * @param type $media_ids
	 */
	public function activity_display( $media_ids = array() ) {
		
	}

}
