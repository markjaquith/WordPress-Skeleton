<?php
/**
 * Install tables required by MediaPress
 * Currently, we create only one table ( Log table )
 * 
 * @global WPDB $wpdb
 */
function mpp_install_db() {
	global $wpdb;
	
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
	$charset_collate = !empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET {$wpdb->charset}" : '';
	
	$sql = array();
	
	$log_table = mediapress()->get_table_name( 'logs' );
	
	$sql[] = "CREATE TABLE IF NOT EXISTS {$log_table} (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) NOT NULL,
		item_id bigint(20) NOT NULL,
		action varchar(16) NOT NULL,
		value varchar(32) NOT NULL,
		logged_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id)
	) {$charset_collate};";

	dbDelta( $sql );	
}

function mpp_upgrade_legacy_1_0_b1_activity() {
	
	
	if( get_option( 'mpp_upgraded_1_0_b1') ) {
		return ;//already upgraded
	}
	
	add_option( 'mpp_upgraded_1_0_b1', 1 );
	
	if( ! get_option( 'mpp-settings' ) ) {
		return ;
		//mediapress was not installed earlier
	}
	
	if( ! function_exists( 'buddypress' ) ) {
		return ;
	}
		
	global $wpdb;

	$activity_table = bp_core_get_table_prefix() .'bp_activity_meta';
	
	//rename _mpp_attached_media_ids key tp _mpp_attached_media_id
	$sql = "UPDATE {$activity_table} SET meta_key = '_mpp_attached_media_id' WHERE meta_key = '_mpp_attached_media_ids'";
	$wpdb->query( $sql );
	
	//add context to all Media comments
			
	$update_query = "INSERT INTO {$activity_table} (activity_id, meta_key, meta_value) 
			SELECT  activity_id, %s as meta_key, %s as meta_value FROM {$activity_table} where meta_key ='_mpp_galery_id'";

	//update the context? should we?

	$wpdb->query( $wpdb->prepare( $update_query, '_mpp_context', 'gallery' ) );
	//update type
	$wpdb->query( $wpdb->prepare( $update_query, '_mpp_activity_type', 'media_upload' ) );
	
	//for media comments
	//$entries = $wpdb->get_col( "SELECT activity_id, meta_value FROM {$activity_table} WHERE meta_key = '_mpp_media_id'" );
	$entries = $wpdb->get_results( "SELECT activity_id, meta_value FROM {$activity_table} WHERE meta_key = '_mpp_media_id'" );
	
	$media_ids = wp_list_pluck( $entries, 'meta_value' );
	//comments are there
	if( ! empty( $media_ids ) ) {
		_prime_post_caches( $media_ids, false, false );
		//add parent gallery id for each of the media
		foreach( $entries as $entry ) {
			$media = get_post( $entry->meta_value );
			mpp_activity_update_gallery_id( $entry->activity_id, $media->post_parent );
		}
		
		//update context to 'media'
		
		$update_query = "INSERT INTO {$activity_table} (activity_id, meta_key, meta_value) 
			SELECT  activity_id, %s as meta_key, %s as meta_value FROM {$activity_table} WHERE meta_key ='_mpp_media_id'";
		
		$wpdb->query( $wpdb->prepare( $update_query, '_mpp_activity_type', 'media_comment' ) );
		$wpdb->query( $wpdb->prepare( $update_query, '_mpp_activity_type', 'media' ) );
		
			
	}
	
	
}
/**
 * This little code saves around 55 queries
 * WordPress does not allow bulk term insert and the queries to insert terms are inefficient
 * MediaPress 
 * @global WPDB $wpdb
 * @return type
 */	
function mpp_install_terms() {
	
	$type_taxname = mpp_get_type_taxname();
	$component_taxname = mpp_get_component_taxname();
	$status_taxname = mpp_get_status_taxname();
	
	//All our terms and their tax info
	$terms_info = array(
		'_sitewide'=> array(
			'name'			=> __( 'Sitewide Galleries', 'mediapress' ),
			'description'	=> __( 'Sitewide Galleries', 'mediapress' ),
			'taxonomy'		=> $component_taxname,
			
		),
		'_members'			=> array(
			'name'			=> __( 'User', 'mediapress' ),
			'description'	=> __( 'User Galleries', 'mediapress' ),
			'taxonomy'		=> $component_taxname,
			
		),
		'_groups'			=> array(
			'name'			=> __( 'Groups', 'mediapress' ),
			'description'	=> __( 'Groups Galleries', 'mediapress' ),
			'taxonomy'		=> $component_taxname,
			
		),
				
		'_public' => array(
			'name'			=> __( 'Public', 'mediapress' ),
			'description'	=> __( 'Public Gallery Privacy Type', 'mediapress' ),
			'taxonomy'		=> $status_taxname
		),
		'_private' => array(
			'name'			=> __( 'Private', 'mediapress' ),
			'description'	=> __( 'Private Gallery Privacy Type', 'mediapress' ),
			'taxonomy'		=> $status_taxname
		),
		'_loggedin' => array(
			'name'			=> __( 'Logged In Users Only', 'mediapress' ),
			'description'	=> __( 'Logged In Users Only Privacy Type', 'mediapress' ),
			'taxonomy'		=> $status_taxname
		),
		
		'_groupsonly' => array(
			'name'			=> __( 'Logged In Users Only', 'mediapress' ),
			'description'	=> __( 'Logged In Users Only Privacy Type', 'mediapress' ),
			'taxonomy'		=> $status_taxname
		),
		
		'_friendsonly' => array(
			'name'			=> __( 'Friends Only', 'mediapress' ),
			'description'	=> __( 'Friends Only Privacy Type', 'mediapress' ),
			'taxonomy'		=> $status_taxname
		),
		
		'_followersonly' => array(
			'name'			=> __( 'Followers Only', 'mediapress' ),
			'description'	=> __( 'Followers Only Privacy Type', 'mediapress' ),
			'taxonomy'		=> $status_taxname
		),
		'_followingonly' => array(
			'name'			=> __( 'Persons I Follow', 'mediapress' ),
			'description'	=> __( 'Following Only Privacy Type', 'mediapress' ),
			'taxonomy'		=> $status_taxname
		),
		
		'_photo' => array(
			'name'			=> __( 'Photo', 'mediapress' ),
			'description'	=> __( 'taxonomy for image media type', 'mediapress' ),
			'taxonomy'		=> $type_taxname
		),
		
		'_video' => array(
			'name'			=> __( 'Video', 'mediapress' ),
			'description'	=> __( 'taxonomy for video media type', 'mediapress' ),
			'taxonomy'		=> $type_taxname
		),
		'_audio' => array(
			'name'			=> __( 'Audio', 'mediapress' ),
			'description'	=> __( 'taxonomy for audio media type', 'mediapress' ),
			'taxonomy'		=> $type_taxname
		),
		'_doc' => array(
			'name'			=> __( 'Documents', 'mediapress' ),
			'description'	=> __( 'taxonomy for documents media type', 'mediapress' ),
			'taxonomy'		=> $type_taxname
		),
		
	);
	//get the term slugs
	$terms = array_keys( $terms_info );
	//we don't need it but let us keep it 
	$terms = array_map( 'esc_sql', $terms );
	//building a list of strings that can be used in the SQL IN clause
	
	$list = '("' . join( '","', $terms ) .'")';
	
	global $wpdb;
	
	//get all terms from our list that already exists in database
	$query = "SELECT slug FROM {$wpdb->terms} WHERE slug IN {$list}";
	
	$existing = $wpdb->get_col( $query);
	
	$non_existing_terms = array_diff( $terms, $existing );
	
	if( empty( $non_existing_terms ) ) {
		return ;//no need to do anything as all of our terms already exists
	}
	
	//if we are here, we have a list of term slugs that do nt exist in database and we need to insert them
	$term_group_id = 0;
	//let us build the bulk insert query
	$terms_insert_query = "INSERT INTO {$wpdb->terms} ( name, slug, term_group) VALUES ";
	
	$values = array();
	
	foreach( $non_existing_terms as $insertable_term ) {
		
		$current_term = $terms_info[$insertable_term];
		$values[] = $wpdb->prepare( '( %s, %s, %d)', $current_term['name'], $insertable_term, $term_group_id ); 
	}
	
	$values = join(',', $values ) .';';
	
	$terms_insert_query = $terms_insert_query . $values;
	//insert
	$wpdb->query( $terms_insert_query );
	
	//now, we look into wp_terms table for these terms and get the term_id
	
	$list_nonexisting = '("' . join( '","', $non_existing_terms ) .'")';
	
	$query = "SELECT term_id, slug FROM {$wpdb->terms} WHERE slug IN {$list_nonexisting}";
	
	$terms_found = $wpdb->get_results( $query );
	
	if( empty( $terms_found ) ) {
		return ;//not likely but in case our earlier insert was unsuccesful
	}
	
	//let us do a bulk insert into term_taxonomy table
	$terms_tax_insert_query = "INSERT INTO {$wpdb->term_taxonomy} ( term_id, taxonomy, description, parent, count ) VALUES ";
	
	$values = array();
	
	foreach( $terms_found  as $found_term ) {
	
		$values[] = $wpdb->prepare( '( %d, %s, %s, %d, %d )', $found_term->term_id, $terms_info[$found_term->slug]['taxonomy'], $terms_info[$found_term->slug]['description'], 0, 0 ); 
	}
	
	$values = join( ',', $values ) .';';
	
	$terms_tax_insert_query = $terms_tax_insert_query . $values;
	
	$wpdb->query( $terms_tax_insert_query );
	//that's all folks, we are all good now :)
}