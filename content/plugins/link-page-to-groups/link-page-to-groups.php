<?php
/**
 * Plugin Name: Link Page to Groups
 * Plugin URI: http://buddydev.com/link-page-to-groups/
 * Version: 1.0.2
 * Author: BuddyDev
 * Author URI: http://BuddyDev.com
 * Description: Link a Page to a BuddyPress Group
 * 
 */

class BP_Link_Page_Group_Helper {
	
	private static $instance = null;
	
	private function __construct() {
		
		$this->setup();
	}
	
	public static function get_instance() {
		
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * setup hooks
	 * 
	 */
	private function setup() {
		
		add_action( 'bp_group_options_nav', array( $this, 'show_links') );
		add_action( 'bp_groups_admin_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'bp_group_admin_edit_after', array( $this, 'update' ) );
		
		if ( apply_filters( 'bp_allow_fronted_group_page_liking', false ) ) {
			$this->enable_frontend_settings();
		}
	}
	
	/**
	 * These hooks are only applied if a side admin allows group admins to modify the settings
	 * 
	 */
	private function enable_frontend_settings() {
		
		add_action( 'bp_after_group_settings_admin', array( $this, 'show_settings' ), 1000 );
		add_action( 'groups_group_settings_edited', array( $this, 'update' ) );
		add_action( 'groups_update_group', array( $this, 'update' ) );
		
	}
	/**
	 * Show attached page in menu
	 * 
	 * @return type
	 */
	public function show_links() {
		
		$group_id = bp_get_current_group_id();
		
		if( ! $group_id ) {
			return ;
		}
		
		$page_id = groups_get_groupmeta( $group_id, '_group_linked_page', true );
		
		if( ! $page_id ) {
			return ;
		}
		
		$post = get_post( $page_id );
		
		echo '<li class="bp-linked-group-page"><a href="' . get_permalink( $post ) .'">'. get_the_title( $post ).'</a></li>';
		
	}
	
	/**
	 * Add metabox on Group Edit page in dashboard
	 */
	public function add_metabox() {
		
		add_meta_box('_bp_linked_group_page_metabox', __( 'Link Page'), array( $this, 'admin_metabox' ), get_current_screen(), 'side' );
	}
	
	/**
	 * Render Metabox
	 * 
	 * @param type $item
	 */
	public function admin_metabox( $item ) {
		
		$group_id = $item->id;
		
		$page_id = groups_get_groupmeta( $group_id, '_group_linked_page', true );
		
		wp_nonce_field( '_bp_linked_group_page', '_bp_linked_group_page_nonce' );
		
		wp_dropdown_pages( array(
			'name'				=> '_group_linked_page',
			'selected'			=> $page_id,
			'show_option_none'	=> __( 'Please select a page')
			) );
	}
	
	/**
	 *  Update association 
	 * 
	 * @param type $group_id
	 * @return type
	 */
	public function update( $group_id ) {
		
		if ( !  isset( $_POST['_bp_linked_group_page_nonce'] ) ) {
			return ;
		}
		
		if( ! $this->current_user_can_modify_settings( $group_id ) ) {
			return ;
		}
		
		if ( ! wp_verify_nonce( $_POST['_bp_linked_group_page_nonce'], '_bp_linked_group_page' ) ) {
			return ;
		}
		
		$page_id = absint( $_POST['_group_linked_page'] );
		
		if ( ! $page_id ) {
			
			groups_delete_groupmeta( $group_id, '_group_linked_page' );
		} else {
			
			groups_update_groupmeta( $group_id, '_group_linked_page',  $page_id );
		}
			
		
	}
	
	
	/**
	 * Show settings on single group page in front end
	 * 
	 */
	public function show_settings() {
		
		$group_id = bp_get_current_group_id();
		
		if ( $this->current_user_can_modify_settings( $group_id ) ) {
			echo "<div class='group-page-link-box'>";
			echo "<h4>" . __( 'Link to Page', 'link-page-to-groups' ) ."</h4>";
				$this->admin_metabox( groups_get_current_group() );
			echo "</div>";
			echo "<hr />";
		}
	}
	
	private function current_user_can_modify_settings( $group_id = false  ) {
		
		
		if ( ! $group_id ) {
			$group_id = bp_get_current_group_id();
		}
		
		if ( is_super_admin() || groups_is_user_admin( get_current_user_id(), $group_id ) ) {
			return true;
		}
		
		return false;
	}
	
}

BP_Link_Page_Group_Helper::get_instance();