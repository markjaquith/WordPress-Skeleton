<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Not Used in version 1.0 of mediaPress
 * 
 */
class MediaPress_Menu_Manager {

	private static $instance;
	private $menus = array();
	protected $items = array();

	private function __construct() {
		;
	}

	public static function get_instance() {
		
	}

	public function add_menu( $args ) {

		$default = array(
			'component'		 => '',
			'component_id'	 => '',
			'menu'			 => '', //menu object
			'type'			 => ''
		);

		$args = wp_parse_args( $args, $default );
		extract( $args );

		if ( ! $menu || ! $component ) {
			return;
		}

		//if we are here, the $component is set
		if ( $component_id ) {
			$this->menus[ $component ][ $component_id ] = $menu;
		}
	}

	public function remove_menu( $args ) {
		
	}

	public function get_menu( $args ) {
		
	}

	public function get_menu_by( $value, $type ) {
		
	}

	public function get_menu_by_component() {
		
	}

	public function get_menu_by_type() {
		
	}

}

/**
 * MediaPress menu Class
 * 
 */
class MPP_Menu {

	/**
	 *
	 * @var array menu items array(multi dimensional)
	 *  
	 */
	protected $items = array();

	/**
	 * Add a new menu item
	 * 
	 * @param type $args
	 * @return boolean
	 */
	public function add_item( $args ) {

		$default = array(
			'slug'		 => '',
			'label'		 => '',
			'action'	 => '',
			'url'		 => '',
			'callback'	 => 'mpp_is_menu_item_visible', //we might have used capability here
		);

		$args = wp_parse_args( $args, $default );

		extract( $args );

		if ( ! $action || ! $slug || ! $label ) {
			return false;
		}

		$this->items[ $slug ] = $args;
	}
	/**
	 * Remove a Menu Item from the current Menu Item stack
	 * 
	 * @param type $slug
	 */
	public function remove_item( $slug ) {

		$item = $this->items[ $slug ];

		unset( $this->items[ 'slug' ] );
	}
	
	/**
	 * Render the Menu
	 * 
	 * @param type $gallery
	 */
	public function render( $gallery, $selected = '' ) {
		
		$items = apply_filters( 'mpp_pre_render_gallery_menu_items', $this->items, $gallery );

		$html = array();
		
		foreach ( $items as $item ) {
			$class = '';
		
			$url = $item[ 'url' ];
			//For visibility, check for the cllback return value
			if ( is_callable( $item[ 'callback' ] ) && call_user_func( $item[ 'callback' ], $item, $gallery ) ) {

				if ( ! $url ) {
					$url	 = $this->get_url( $gallery, $item[ 'action' ] );
				}
				
				if ( $item['action'] == $selected ) {
					$class = 'mpp-selected-item';
				}
				
				$html[]	 = sprintf( '<li><a href="%1$s" title ="%2$s" id="%3$s" data-mpp-action ="%4$s" class="%6$s">%5$s</a></li>', $item[ 'url' ], $item[ 'label' ], 'mpp-gallery-menu-item-' . $item[ 'slug' ], $item['action'], $item[ 'label' ], $class );
			}
		}
		
		if ( $html ) {
			echo '<ul>' . join( '', $html ) . '</ul>';
		}
	}

	/**
	 * 
	 * @param type $gallery
	 * @param type $action
	 * @return type
	 * @todo update with actual url
	 */
	public function get_url( $gallery, $action ) {
		return '#' . $action;
	}

}

class MPP_Gallery_Menu extends MPP_Menu {
	
}

class MPP_Media_Menu extends MPP_Menu {
	
}

