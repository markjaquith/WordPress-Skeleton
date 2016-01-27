<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}


/**
 * Register various core features
 * Registers statuses
 * Registers types(media type)
 * Also registers Component types
 */
function mpp_setup_core() {
    
		//if the 'gallery' slug is not set , set it to mediapress?
        
	if ( ! defined( 'MPP_GALLERY_SLUG' ) ) {
        define( 'MPP_GALLERY_SLUG', 'mediapress' );
	}
    //register privacies
    //private
    mpp_register_status( array(
            'key'				=> 'public',
            'label'				=> __( 'Public', 'mediapress' ),
            'labels'			=> array( 
									'singular_name' => __( 'Public', 'mediapress' ),
									'plural_name'	=> __( 'Public', 'mediapress' )
			),
            'description'		=> __( 'Public Gallery Privacy Type', 'mediapress' ),
            'callback'			=> 'mpp_check_public_access',
			'activity_privacy'	=> 'public'
    ));
   
    mpp_register_status( array(
            'key'				=> 'private',
            'label'				=> __( 'Private', 'mediapress' ),
            'labels'			=> array( 
									'singular_name' => __( 'Private', 'mediapress' ),
									'plural_name'	=> __( 'Private', 'mediapress' )
			),
            'description'		=> __( 'Private Privacy Type'. 'mediapress' ),
            'callback'			=> 'mpp_check_private_access',
			'activity_privacy'	=> 'onlyme'
    ));

		
	mpp_register_status( array(
				'key'				=> 'loggedin',
				'label'				=> __( 'Logged In Users Only', 'mediapress' ),
				'labels'			=> array( 
										'singular_name' => __( 'Logged In Users Only', 'mediapress' ),
										'plural_name'	=> __( 'Logged In Users Only', 'mediapress' )
				),
				'description'		=> __( 'Logged In Users Only Privacy Type', 'mediapress' ),
				'callback'			=> 'mpp_check_loggedin_access',
				'activity_privacy'	=> 'loggedin',
	));
    
	//For BuddyPress specific status, please check modules/buddypress/loader.php 
	//Register sitewide gallery component
	mpp_register_component( array(
				'key'           => 'sitewide',
				'label'         => __( 'Sitewide Galleries', 'mediapress' ),
				'labels'		=> array(
									'singular_name'	=> __( 'Sitewide Gallery', 'mediapress' ),
									'plural_name'	=> __( 'Sitewide Galleries', 'mediapress' )
				),
				'description'   => __( 'Sitewide Galleries', 'mediapress' ),
	) );
	
    //register types
    //photo
    mpp_register_type( array(
            'key'           => 'photo',
            'label'         => __( 'Photo', 'mediapress' ),
            'description'   => __( 'taxonomy for image media type', 'mediapress' ),
			'labels'		=> array(
								'singular_name'	=> __( 'Photo', 'mediapress' ),
								'plural_name'	=> __( 'Photos', 'mediapress' )
			),
            'extensions'    => array( 'jpeg', 'jpg', 'gif', 'png' ),
    ) );
    //video
    mpp_register_type( array(
            'key'           => 'video',
            'label'         => __( 'Video', 'mediapress' ),
			'labels'		=> array(
								'singular_name'	=> __( 'Video', 'mediapress' ),
								'plural_name'	=> __( 'Videos', 'mediapress' )
			),
            'description'   => __( 'Video media type taxonomy', 'mediapress' ),
			'extensions'	=> array( 'mp4', 'flv', 'mpeg' )
    ) );
	
    mpp_register_type( array(
            'key'           => 'audio',
            'label'         => __( 'Audio', 'mediapress' ),
			'labels'		=> array(
								'singular_name'	=> __( 'Audio', 'mediapress' ),
								'plural_name'	=> __( 'Audios', 'mediapress' )
			),
            'description'   => __( 'Audio Media type taxonomy', 'mediapress' ),
			'extensions'	=> array( 'mp3', 'wmv', 'midi' )
    ) );
	
    mpp_register_type( array(
            'key'           => 'doc',
            'label'         => __( 'Documents', 'mediapress' ),
			'labels'		=> array(
								'singular_name'	=> __( 'Document', 'mediapress' ),
								'plural_name'	=> __( 'Documents', 'mediapress' )
			),
            'description'   => __( 'This is documents gallery', 'mediapress' ),
            'extensions'    => array( 'zip', 'gz', 'doc', 'pdf', 'docx', 'xls' )
    ) );
   
	//Register media sizes
    mpp_register_media_size( array(
            'name'  => 'thumbnail',
            'height'=> 200,
            'width' => 200,
            'crop'  => true,
            'type'  => 'default'
    ) );
    
    mpp_register_media_size( array(
            'name'  => 'mid',
            'height'=> 300,
            'width' => 500,
            'crop'  => true,
            'type'  => 'default'
    ) );
    
    mpp_register_media_size( array(
            'name'  => 'large',
            'height'=> 800,
            'width' => 600,
            'crop'  => false,
            'type'  => 'default'
    ) );

	//register status support		
	mpp_component_add_status_support( 'sitewide', 'public' );
	mpp_component_add_status_support( 'sitewide', 'private' );
	mpp_component_add_status_support( 'sitewide', 'loggedin' );
	
	//register type support
	mpp_component_init_type_support( 'sitewide' );
	
	//register storage managers here
    //local storage manager
    mpp_register_storage_manager( 'local', MPP_Local_Storage::get_instance() );
    //mpp_register_storage_manager( 'aws', MPP_Local_Storage::get_instance() );
    
	
	//register default viewer
	$default_view = MPP_Gallery_View_Default::get_instance();
	
	mpp_register_gallery_view( 'photo', $default_view );
	mpp_register_gallery_view( 'video', $default_view );
	mpp_register_gallery_view( 'audio', $default_view );
	mpp_register_gallery_view( 'doc',	$default_view );
	
	$list_view = MPP_Gallery_View_List::get_instance();
	
	mpp_register_gallery_view( 'photo', $list_view );
	mpp_register_gallery_view( 'video', $list_view );
	mpp_register_gallery_view( 'audio', $list_view );
	mpp_register_gallery_view( 'doc',	$list_view );
	
	//video playlist
	mpp_register_gallery_view( 'video', MPP_Gallery_View_Video_Playlist::get_instance() );
	//audio playlist
	mpp_register_gallery_view( 'audio', MPP_Gallery_View_Audio_Playlist::get_instance() );
	
	//please note, google doc viewer will not work for local files
	//files must be somewhere accessible from the web
	
	mpp_register_media_view( 'photo', 'default', new MPP_Media_View_Photo() );
	mpp_register_media_view( 'doc', 'default', new MPP_Media_View_Docs() );
	
	//we are registering for video so we can replace it in future for flexible video views
	
	mpp_register_media_view( 'video','default', new MPP_Media_View_Video() );
	
	//audio
	mpp_register_media_view( 'audio', 'default', new MPP_Media_View_Audio() );
	
	//should we register a photo viewer too? may be for the sake of simplicity?
	
	
    //setup the tabs
    mediapress()->add_menu( 'gallery', new MPP_Gallery_Menu() );
    mediapress()->add_menu( 'media', new MPP_Media_Menu() );
	
	do_action( 'mpp_setup_core' );
}

//initialize core

add_action( 'mpp_setup', 'mpp_setup_core');


function mpp_setup_gallery_nav() {
    
	//only add on single gallery
	
	if ( ! mpp_is_single_gallery() ) {
		return;
	}
	
    $gallery = mpp_get_current_gallery();
	
    $url = '';
	
    if ( $gallery ) {
        
       $url = mpp_get_gallery_permalink( $gallery );
    }
    
	//only add view/edit/dele links on the single mgallery view
	
    mpp_add_gallery_nav_item( array(
        'label'		=> __( 'View', 'mediapress' ),
        'url'		=> $url,
        'action'	=> 'view',
        'slug'		=> 'view'
        
    ));
	
	$user_id = get_current_user_id();
	
    if ( mpp_user_can_edit_gallery( $gallery->id, $user_id ) ) {
		
		mpp_add_gallery_nav_item( array(
			'label'		=> __( 'Edit Media', 'mediapress' ), //we can change it to media type later
			'url'		=> mpp_get_gallery_edit_media_url( $gallery ),
			'action'	=> 'edit',
			'slug'		=> 'edit'

		));
	}
	
	if ( mpp_user_can_upload( $gallery->component, $gallery->component_id ) ) {
		
		mpp_add_gallery_nav_item( array(
			'label'		=> __( 'Add Media', 'mediapress' ), //we can change it to media type later
			'url'		=> mpp_get_gallery_add_media_url( $gallery ),
			'action'	=> 'add',
			'slug'		=> 'add'

		));
	}
	
	if ( mpp_user_can_edit_gallery( $gallery->id, $user_id ) ) {
		
		mpp_add_gallery_nav_item( array(
			'label'		=> __( 'Reorder', 'mediapress' ), //we can change it to media type later
			'url'		=> mpp_get_gallery_reorder_media_url( $gallery ),
			'action'	=> 'reorder',
			'slug'		=> 'reorder'

		));

		mpp_add_gallery_nav_item( array(
			'label'		=> __( 'Edit Details', 'mediapress' ),
			'url'		=> mpp_get_gallery_settings_url( $gallery ),
			'action'	=> 'settings',
			'slug'		=> 'settings'

		));
	}
	
	if ( mpp_user_can_delete_gallery( $gallery->id ) ) {
		
		mpp_add_gallery_nav_item( array(
			'label'		=> __( 'Delete', 'mediapress' ),
			'url'		=> mpp_get_gallery_delete_url( $gallery ),
			'action'	=> 'delete',
			'slug'		=> 'delete'

		));
	}
    
}
add_action( 'mpp_setup_globals', 'mpp_setup_gallery_nav' );
