<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Storage Space related
 * should we put into storage helper?
 * 
 */

/**
 * Is upload space available for the given component( based on type & ID )
 * @param type $component
 * @param type $component_id
 * @return boolean
 */
function mpp_has_available_space( $component, $component_id ) {
    
	$allowed_space = mpp_get_allowed_space( $component, $component_id );//how much
	
    $used_space = mpp_get_used_space( $component,$component_id );
   
    if ( ( $allowed_space - $used_space ) <= 0 ) {
		return false;
	}

    return true;
}

/**
 * Get allowed space for the given component( In MB) 
 * @param type $component
 * @param type $component_id
 * @return numeric : no. of MBs
 */
function mpp_get_allowed_space( $component, $component_id = null ) {
    
    if ( ! empty( $component_id ) ) {
        
        if ( $component == 'members' ) {
            $space_allowed = mpp_get_user_meta( $component_id, 'mpp_upload_space', true );
		} elseif ( $component == 'groups' && function_exists( 'groups_get_groupmeta' ) ) {
            $space_allowed = groups_get_groupmeta ( $component_id, 'mpp_upload_space', true );
		}
    }

    if ( empty( $component_id ) || empty( $space_allowed ) ) {
        //if owner id is empty
        //get the gallery/group space
        if ( $component == 'members' ) {
			$space_allowed = mpp_get_option( 'mpp_upload_space' );
		} elseif ( $component == 'groups' ) {
             $space_allowed = mpp_get_option( 'mpp_upload_space_groups' );
		}
    }
   
        //we should have some value by now
    
    //if( empty($space_allowed))
     ///   $space_allowed = get_option("gallery_upload_space");//currently let us deal with blog space gallery will have it's own limit later
    if ( empty( $space_allowed ) ) {
        $space_allowed = mpp_get_option( 'mpp_upload_space' );
	}
    //if we still don't have anything
    
	if ( empty( $space_allowed ) || ! is_numeric( $space_allowed ) ) {
		$space_allowed = 10;//by default
	}

	return apply_filters( 'mpp_allowed_space', $space_allowed, $component, $component_id );//allow to override for specific users/groups
}

/**
 * Get the Used space by a component
 * 
 * @param type $component
 * @param type $component_id
 * @return int
 */

function mpp_get_used_space( $component, $component_id ) {
    
    $storage_manager = mpp_get_storage_manager();//get default
    
    return $storage_manager->get_used_space( $component, $component_id );

}

/**
 * Get the remaining space in MBs
 * @param type $component
 * @param type $component_id
 * @return type
 */
function mpp_get_remaining_space( $component, $component_id ) {
    
    $allowed	= mpp_get_allowed_space( $component, $component_id );
    $used		= mpp_get_used_space( $component, $component_id );
    
    return intval( $allowed - $used );
}


function mpp_display_space_usage( $component = null, $component_id = null ) {
   
	if ( ! mpp_get_option( 'show_upload_quota' ) ) {
		return;
	}

	if ( ! $component ) {
		$component = mpp_get_current_component();
	}
	
	if ( ! $component_id ) {
		$component_id = mpp_get_current_component_id();
	}
	
	$total_space = mpp_get_allowed_space( $component, $component_id );

	$used = mpp_get_used_space( $component, $component_id );

	if ( $used > $total_space ) { 
		$percentused = '100';
	} else {
		$percentused = ( $used / $total_space ) * 100;
	}
	
	if ( $total_space > 1000 ) {
		$total_space = number_format( $total_space / 1024 );
		$total_space .= __( 'GB', 'mediapress' );
	} else {
		$total_space .= __( 'MB', 'mediapress' );
	}
	
	?>
	<strong><?php printf( __( 'You have <span> %1s%%</span> of your %2s space left','mediapress' ), number_format( 100 - $percentused ), $total_space );?></strong>
	<?php
}

