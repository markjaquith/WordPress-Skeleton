<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

if ( class_exists( 'BP_Group_Extension' ) ) :
class MPP_Group_Gallery_Extension extends BP_Group_Extension{
	
	
	
	public function __construct() {
		
		//$is_enabled = mpp_is_active_component( 'groups' );
		$args = array(
			'slug'				=> MPP_GALLERY_SLUG,
			'name'				=> __( 'Gallery', 'mediapress' ),
			'visibility'		=> 'public',//private
			'nav_item_position' => 80,
			'nav_item_name'		=> __( 'Gallery', 'mediapress' ),
			'enable_nav_item'	=> mpp_group_is_gallery_enabled(),//true by default
			//'display_hook' => 'groups_custom_group_boxes', //meta box hook
			//'template_file'=> 'groups/single/plugins.php', //
			'screens' => array(
				'create' => array(
					//'position' => 81,
					'enabled' => false,
					//'name' => 'Gallery Option',
					//'slug'	=> MPP_GALLERY_SLUG,
					//'screen_callback' => '',
					//'screen_save_callback' => ''
				),
				'edit' => array(
					//'position' => 81,
					'enabled' => false,
					//'name' => 'Gallery Option',
					//'slug'	=> MPP_GALLERY_SLUG,
					//'screen_callback' => '',
					//'screen_save_callback' => ''
				),
				'admin' => array(
					//'metabox_context' => normal,
					//'metabox_priority' => '',
					'enabled' => false,
					//'name'	=> 'Gallery Settings',
					//'slug'	=> MPP_GALLERY_SLUG,
					//'screen_callback' => '',
					//'screen_save_callback' => ''
				),
			),
		);
		parent::init($args);
		
		
		
	}
	
	public function display( $group_id = null ) {
		
		mpp_get_component_template_loader( 'groups' )->loade_template();
	}
	
    /**
     * settings_screen() is the catch-all method for displaying the content 
     * of the edit, create, and Dashboard admin panels
     */
    public function settings_screen( $group_id = null ) {
		
    }
    /**
     * settings_sceren_save() contains the catch-all logic for saving 
     * settings from the edit, create, and Dashboard admin panels
     */
    public function settings_screen_save( $group_id = null  ) {
		
    }
} 

bp_register_group_extension( 'MPP_Group_Gallery_Extension' );

endif;


function mppp_group_enable_form() {
	
    if ( ! mpp_is_active_component( 'groups' ) ) {
        return;//do not show if gallery is not enabled for group component
	}
    ?>
    <div class="checkbox mpp-group-gallery-enable">
		<label><input type="checkbox" name="mpp-enable-gallery" id="mpp-enable-gallery" value="yes" <?php echo checked( 1, mpp_group_is_gallery_enabled() );?>/> <?php _e( 'Enable Gallery', 'mediapress' ) ?></label>
    </div>
<?php

}
add_action( 'bp_before_group_settings_admin', 'mppp_group_enable_form' );
add_action( 'bp_before_group_settings_creation_step', 'mppp_group_enable_form' );


function mppp_group_save_preference( $group_id ) {
	
      $enabled= isset( $_POST['mpp-enable-gallery'] ) ? $_POST['mpp-enable-gallery'] : 'no';
	  
	  if ( $enabled != 'yes' && $enabled != 'no' ) {//invalid value
		  $enabled = 'no';//set it to no
	  }
	  
      mpp_group_set_gallery_state( $group_id, $enabled );
	  
}
add_action( 'groups_group_settings_edited', 'mppp_group_save_preference' );
add_action( 'groups_create_group', 'mppp_group_save_preference' );
add_action( 'groups_update_group', 'mppp_group_save_preference' );

