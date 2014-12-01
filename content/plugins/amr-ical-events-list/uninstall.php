<?php
/**
 * Uninstall functionality for amr iCal Events List plugin.
 * 
 * Removes the plugin cleanly in WP 2.7 and up
 */

// first, check to make sure that we are indeed uninstalling
if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}

/* This is the amr ical uninstall file */
	function amr_ical_uninstall(){	
	if (function_exists ('delete_option')) {  	// delete all options we may have used over time
		delete_option('amr-ical-calendar_preview_url');
		delete_option('amr-ical-events-version');
		delete_option('amr-ical-events-list');
		delete_option("amricalWidget");
		delete_option("amr-ical-widget");
		delete_option('amr-ical-images-to-use');
		echo '<p>'.__('amr ical options deleted from database', 'amr-ical-events-list').'</p>';
		unlink();
		// now look for and delete cache files in upload dir
		$upload_dir = wp_upload_dir();
		$dir_to_delete = $upload_dir . '/ical-events-cache/' ;
		$files = list_files( $dir_to_delete );
		if ( $files ) {
			$files_to_delete = array_merge($files_to_delete, $files);
			
		}
		$deleted = $wp_filesystem->delete($dir_to_delete, true); // delete recurively
		echo '<p>'.__('amr ical cached ics files deleted ', 'amr-ical-events-list').'</p>';
		echo '<p>'.__('Css files may also exist.  They and the css folder have not been deleted as they have been shared with other plugins.', 'amr-ical-events-list').'</p>';
		$cssdir = $upload_dir . '/css/' ;
		$files = list_files( $cssdir );
		foreach ($files as $i=> $file) {
			echo '<br />'.$file;
		}
	}
	else {
		echo '<p>Wordpress Function delete_option does not exist.</p>';
		return (false);	
		}
					
	}
/* -------------------------------------------------------------------------------------------------------------*/

	
register_uninstall_hook(__FILE__,'amr_ical_uninstall');
?>

