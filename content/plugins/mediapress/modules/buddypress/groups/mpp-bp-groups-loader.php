<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}


/**
 * Load MediaPress Group extension 
 */
add_action( 'bp_loaded', 'mpp_group_extension_load' );

function mpp_group_extension_load() {
	
	$files = array(
		'mpp-bp-groups-actions.php',
		'mpp-bp-groups-functions.php',
		'mpp-bp-groups-hooks.php',
		'mpp-bp-groups-group-extension.php',
	);
	
	
	$path = mediapress()->get_path() . 'modules/buddypress/groups/';
	
	foreach( $files as $file ) {
		require_once $path . $file;
	}
	
	do_action( 'mpp_group_extension_loaded' );
	
}

//mpp_group_extension_load();

function mpp_group_init() {
	
	    mpp_register_status( array(
            'key'           => 'groupsonly',
            'label'         => __( 'Group Only', 'mediapress' ),
            'labels'        => array( 
									'singular_name' => __( 'Group Only', 'mediapress' ),
									'plural_name'	=> __( 'Group Only', 'mediapress' )
			),
            'description'   => __( 'Group Only Privacy Type', 'mediapress' ),
            'callback'      => 'mpp_check_groups_access',
			'activity_privacy'	=> 'grouponly',
    ));
    
		
}

add_action( 'mpp_setup', 'mpp_group_init' );

//filter status dd

function mpp_group_filter_status( $statuses ) {
	
	if ( bp_is_group() ) {
		unset( $statuses['friends'] );
		unset( $statuses['private'] );
		
	} else {
		unset( $statuses['groupsonly'] );
	}
	
	return $statuses;
		
}

//add_filter( 'mpp_get_editable_statuses', 'mpp_group_filter_status' );