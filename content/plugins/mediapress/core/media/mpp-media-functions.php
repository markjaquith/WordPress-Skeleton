<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if given post is a valid MediaPress media
 * Checks for post type + mpp_is_mpp_media meta
 * 
 * @param int $media_id
 */
function mpp_is_valid_media( $media_id ) {

	if ( mpp_get_media_meta( $media_id, '_mpp_is_mpp_media', true ) && ( get_post_type( $media_id ) == mpp_get_media_post_type() ) ) {
		return true;
	}

	return false;
}

function mpp_get_all_media_ids( $args = null ) {

	$component = mpp_get_current_component();
	$component_id = mpp_get_current_component_id();

	$default = array(
		'gallery_id'	=> mpp_get_current_gallery_id(),
		'component'		=> $component,
		'component_id'	=> $component_id,
		'per_page'		=> -1,
		'status'		=> mpp_get_accessible_statuses( $component, $component_id, get_current_user_id() ),
		'nopaging'		=> true,
		'fields'		=> 'ids'
	);

	$args = wp_parse_args( $args, $default );

	$ids = new MPP_Media_Query( $args );

	return $ids->get_ids();
}

/**
 * Get total media count for user|group etc
 * 
 * @param type $args
 * @see mpp_get_object_count for parameter details
 * @return type
 * 
 * @todo to save queries, in future, I believe It will be a better option to save the count in user meta, i have left it for now to avoid any syn issue
 * We will do it when we introduce mpp-tools
 * 
 * 
 */
function mpp_get_media_count( $args ) {

	$args['post_status'] = 'inherit';
	return mpp_get_object_count( $args, mpp_get_media_post_type() );
}

/**
 * Check if a media exists or not
 * @param type $media_slug
 * @param type $component
 * @param type $component_id
 * @return type
 */
function mpp_media_exists( $media_slug, $component, $component_id ) {
	
	if ( ! $media_slug ) {
		return false;
	}

	return mpp_post_exists( array(
		'component'		=> $component,
		'component_id'	=> $component_id,
		'slug'			=> $media_slug,
		'post_status'	=> 'inherit',
		'post_type'		=> mpp_get_media_post_type()
	));
}

/**
 * Add a New Media to teh Gallery
 * 
 * @param type $args
 * @return int|boolean
 */
function mpp_add_media( $args ) {


	$default = array(
		'user_id'			=> get_current_user_id(),
		'gallery_id'		=> 0,
		'post_parent'		=> 0,
		'is_orphan'			=> 0, //notorphan
		'is_uploaded'		=> 0,
		'is_remote'			=> 0,
		'is_imorted'		=> 0,
		'is_embedded'		=> 0,
		'embed_url'			=> '',
		'embed_html'		=> '',
		'component_id'		=> false,
		'component'			=> '',
		'context'			=> '',
		'status'			=> '',
		'type'				=> '',
		'storage_method'	=> '',
		'mime_type'			=> '',
		'description'		=> '',
		'sort_order'		=> 0, //sort order	
	);
	
	$args = wp_parse_args( $args, $default );
	extract( $args );

	//print_r($args );
	//return ;
	if ( ! $title || ! $user_id || ! $type ) {
		return false;
	}
	
	$post_data = array();

	//check if the gallery is sorted and the sorting order is not set explicitly
	//we update it
	if ( ! $sort_order && mpp_is_gallery_sorted( $gallery_id ) ) {
		//current max sort order +1
		$sort_order = (int) mpp_get_max_media_order( $gallery_id ) + 1;
	}
	// Construct the attachment array
	$attachment = array_merge( array(
		'post_mime_type'	=> $mime_type,
		'guid'				=> $url,
		'post_parent'		=> $gallery_id,
		'post_title'		=> $title,
		'post_content'		=> $description,
		'menu_order'		=> $sort_order,
	), $post_data );

	// This should never be set as it would then overwrite an existing attachment.
	if ( isset( $attachment['ID'] ) ) {
		unset( $attachment['ID'] );
	}

	// Save the data
	$id = wp_insert_attachment( $attachment, $src, $gallery_id );

	if ( ! is_wp_error( $id ) ) {
		//set component
		if ( $component ) {
			wp_set_object_terms( $id, mpp_underscore_it( $component ), mpp_get_component_taxname() );
		}

		//set _component_id meta key user_id/gallery_id/group id etc
		if ( $component_id ) {
			mpp_update_media_meta( $id, '_mpp_component_id', $component_id );
		}
		//set upload context
		if ( $context && $context == 'activity' ) {
			//only store context for activity uploaded media
			mpp_update_media_meta( $id, '_mpp_context', $context );
		}

		//set media privacy
		if ( $status ) {
			wp_set_object_terms( $id, mpp_underscore_it( $status ), mpp_get_status_taxname() );
		}
		//set media type internally as audio/video etc
		if ( $type ) {
			wp_set_object_terms( $id, mpp_underscore_it( $type ), mpp_get_type_taxname() );
		}
		//
		if ( $storage_method && $storage_method != 'local' ) {
			//keep storge manager info if it is not default
			mpp_update_media_meta( $id, '_mpp_storage_method', $storage_method );
		}
		//
		//add all extraz here

		if ( $is_orphan ) {
			mpp_update_media_meta( $id, '_mpp_is_orphan', $is_orphan );
		}
		//is_uploaded
		//is_remote
		//mark as mediapress media
		mpp_update_media_meta( $id, '_mpp_is_mpp_media', 1 );

		wp_update_attachment_metadata( $id, mpp_generate_media_metadata( $id, $src ) );

		do_action( 'mpp_media_added', $id, $gallery_id );
		return $id;
	}

	return false; // there was an error
}

function mpp_update_media( $args = null ) {

	//updating media can not change the Id & SRC, so 

	if ( ! isset( $args['id'] ) ) {
		return false;
	}

	$default = array(
		'user_id'			=> get_current_user_id(),
		'gallery_id'		=> false,
		'post_parent'		=> false,
		'is_orphan'			=> false,
		'is_uploaded'		=> '',
		'is_remote'			=> '',
		'is_imorted'		=> '',
		'is_embedded'		=> '',
		'embed_url'			=> '',
		'embed_html'		=> '',
		'component_id'		=> '',
		'component'			=> '',
		'context'			=> '',
		'status'			=> '',
		'type'				=> '',
		'storage_method'	=> '',
		'mime_type'			=> '',
		'description'		=> '',
		'sort_order'		=> 0,
	);
	
	$args = wp_parse_args( $args, $default );
	extract( $args );

	//print_r($args );
	//return ;
	if ( ! $title ) {
		return false;
	}

	$post_data = get_post( $id, ARRAY_A );

	if ( ! $gallery_id ) {
		$gallery_id = $post_data['post_parent'];
	}
	
	if ( $title ) {
		$post_data['post_title'] = $title;
	}
	
	if ( $description ) {
		$post_data['post_content'] = $description;
	}

	if ( $gallery_id ) {
		$post_data['post_parent'] = $gallery_id;
	}
	
	//check if the gallery is sorted and the sorting order is not set explicitly
	//we update it
	if ( ! $sort_order && ! $post_data['menu_order'] && mpp_is_gallery_sorted( $gallery_id ) ) {
		//current max sort order +1
		$sort_order = (int) mpp_get_max_media_order( $gallery_id ) + 1;
	}
	
	if ( $sort_order ) {
		$post_data['menu_order'] = absin( $sort_order );
	}
	// Save the data
	$id = wp_insert_attachment( $post_data, false, $gallery_id );

	if ( ! is_wp_error( $id ) ) {
		//set component
		if ( $component ) {
			wp_set_object_terms( $id, mpp_underscore_it( $component ), mpp_get_component_taxname() );
		}

		//set _component_id meta key user_id/gallery_id/group id etc
		if ( $component_id ) {
			mpp_update_media_meta( $id, '_mpp_component_id', $component_id );
		}
		//set upload context
		if ( $context && $context == 'activity' ) {
			//only store context for media uploaded from activity
			mpp_update_media_meta( $id, '_mpp_context', $context );
		}

		//set media privacy
		if ( $status ) {
			wp_set_object_terms( $id, mpp_underscore_it( $status ), mpp_get_status_taxname() );
		}
		//set media type internally as audio/video etc
		if ( $type ) {
			wp_set_object_terms( $id, mpp_underscore_it( $type ), mpp_get_type_taxname() );
		}
		//
		if ( $storage_method && $storage_method != 'local' ) {
			//let us not waste extra entries on local storage, ok. Storge storage info only if it is not the default local storage
			mpp_update_media_meta( $id, '_mpp_storage_method', $storage_method );
		}
		//
		//add all extraz here

		if ( $is_orphan ) {
			mpp_update_media_meta( $id, '_mpp_is_orphan', $is_orphan );
		} else {
			mpp_delete_media_meta( $id, '_mpp_is_orphan' );
		}

		do_action( 'mpp_media_updated', $id, $gallery_id );

		return $id;
	}

	return false; // there was an error
}

/**
 * Remove a Media entry from gallery
 * 
 * @param type $media_id
 */
/**
 * @see MPP_Deletion_Actions_Mapper::map_before_delete_post_action()
 * @see MPP_Deletion_Actions_Mapper::map_deleted_post() for the approprivte function
 */

/**
 * Action flow
 *  wp_delete_attachment() 
 * 		-> do_action('delete_attachment', $post_id )
 * 		-> MPP_Deletion_Actions_Mapper::map_before_delete_attachment()
 * 		-> do_action ( 'mpp_before_media_delete', $gallery_id )
 * 		-> cleanup gallery
 * 		.........
 * 		.........
 * 
 *  wp_delete_attachment()
 * 		-> do_action( 'deleted_post', $post_id )
 * 		-> do_action( 'mpp_media_deleted', $gallery_id )		
 */
function mpp_delete_media( $media_id ) {

	return wp_delete_attachment( $media_id, true );
}

/**
 * Updates a given media Order
 * 
 * @global type $wpdb
 * @param type $media_id
 * @param type $order_number
 * @return type
 */
function mpp_update_media_order( $media_id, $order_number ) {

	global $wpdb;

	$query = $wpdb->prepare( "UPDATE {$wpdb->posts} SET menu_order =%d WHERE ID =%d", $order_number, $media_id );

	return $wpdb->query( $query );
}

/**
 * Get the order no. for the last sorted item
 * 
 * @global type $wpdb
 * @param type $gallery_id
 * @return type
 * @todo improve name, suggestions are welcome
 */
function mpp_get_max_media_order( $gallery_id ) {

	global $wpdb;

	$query = $wpdb->prepare( "SELECT MAX(menu_order) as sort_order FROM {$wpdb->posts}  WHERE post_parent =%d", $gallery_id );

	return $wpdb->get_var( $query );
}

function mpp_get_media_type_from_extension( $ext ) {

	$ext = strtolower( $ext );

	$all_extensions = mpp_get_all_media_extensions();

	foreach ( $all_extensions as $type => $extensions ) {

		if ( in_array( $ext, $extensions ) ) {
			return $type;
		}
	}

	return false; //invalid type
}

function mpp_get_file_extension( $file_name ) {

	$parts = explode( '.', $file_name );
	return end( $parts );
}

/**
 * Prepare Media for JSON
 *  this is a copy from send json for attachment, we will improve it in our 1.1 release
 * @todo refactor
 * @param type $attachment
 * @return type
 */
function mpp_media_to_json( $attachment ) {

	if ( ! $attachment = get_post( $attachment ) ) {
		return;
	}

	if ( 'attachment' != $attachment->post_type ) {
		return;
	}

	//the attachment can be either a media or a cover
	//in case of media, if it is non photo, we need the thumb.url to point to the cover(or generated cover)
	//in case of cover, we don't care

	$media = mpp_get_media( $attachment->ID );

	$meta = wp_get_attachment_metadata( $attachment->ID );

	if ( false !== strpos( $attachment->post_mime_type, '/' ) ) {
		list( $type, $subtype ) = explode( '/', $attachment->post_mime_type );
	} else {
		list( $type, $subtype ) = array( $attachment->post_mime_type, '' );
	}
	
	$attachment_url = wp_get_attachment_url( $attachment->ID );

	$response = array(
		'id'			=> $media->id,
		'title'			=> mpp_get_media_title( $media ),
		'filename'		=> wp_basename( $attachment->guid ),
		'url'			=> $attachment_url,
		'link'			=> mpp_get_media_permalink( $media ),
		'alt'			=> mpp_get_media_title( $media ),
		'author'		=> $media->user_id,
		'description'	=> $media->description,
		'caption'		=> $media->excerpt,
		'name'			=> $media->slug,
		'status'		=> $media->status,
		'parent_id'		=> $media->gallery_id,
		'date'			=> strtotime( $attachment->post_date_gmt ) * 1000,
		'modified'		=> strtotime( $attachment->post_modified_gmt ) * 1000,
		'menuOrder'		=> $attachment->menu_order,
		'mime'			=> $attachment->post_mime_type,
		'type'			=> $media->type,
		'subtype'		=> $subtype,
		'dateFormatted' => mysql2date( get_option( 'date_format' ), $attachment->post_date ),
		'meta'			=> false,
			//'thumbnail'		=> mpp_get_media_src('thumbnail', $media )
	);


	if ( $attachment->post_parent ) {
		$post_parent = get_post( $attachment->post_parent );
		$parent_type = get_post_type_object( $post_parent->post_type );
		if ( $parent_type && $parent_type->show_ui && current_user_can( 'edit_post', $attachment->post_parent ) ) {
			$response['uploadedToLink'] = get_edit_post_link( $attachment->post_parent, 'raw' );
		}
		$response['uploadedToTitle'] = $post_parent->post_title ? $post_parent->post_title : __( '(no title)' );
	}

	$attached_file = get_attached_file( $attachment->ID );

	if ( file_exists( $attached_file ) ) {
		$bytes = filesize( $attached_file );
		$response['filesizeInBytes'] = $bytes;
		$response['filesizeHumanReadable'] = size_format( $bytes );
	}


	if ( $meta && 'image' === $type ) {
		$sizes = array();

		/** This filter is documented in wp-admin/includes/media.php */
		$possible_sizes = apply_filters( 'image_size_names_choose', array(
			'thumbnail'		=> __( 'Thumbnail' ),
			'medium'		=> __( 'Medium' ),
			'large'			=> __( 'Large' ),
			'full'			=> __( 'Full Size' ),
		) );
		
		unset( $possible_sizes['full'] );

		// Loop through all potential sizes that may be chosen. Try to do this with some efficiency.
		// First: run the image_downsize filter. If it returns something, we can use its data.
		// If the filter does not return something, then image_downsize() is just an expensive
		// way to check the image metadata, which we do second.
		foreach ( $possible_sizes as $size => $label ) {

			/** This filter is documented in wp-includes/media.php */
			if ( $downsize = apply_filters( 'image_downsize', false, $attachment->ID, $size ) ) {
		
				if ( ! $downsize[3] ) {
					continue;
				}
				
				$sizes[$size] = array(
					'height'		=> $downsize[2],
					'width'			=> $downsize[1],
					'url'			=> $downsize[0],
					'orientation'	=> $downsize[2] > $downsize[1] ? 'portrait' : 'landscape',
				);
			} elseif ( isset( $meta['sizes'][$size] ) ) {
				if ( ! isset( $base_url ) ) {
					$base_url = str_replace( wp_basename( $attachment_url ), '', $attachment_url );
				}
				
				// Nothing from the filter, so consult image metadata if we have it.
				$size_meta = $meta['sizes'][$size];

				// We have the actual image size, but might need to further constrain it if content_width is narrower.
				// Thumbnail, medium, and full sizes are also checked against the site's height/width options.
				list( $width, $height ) = image_constrain_size_for_editor( $size_meta['width'], $size_meta['height'], $size, 'edit' );

				$sizes[$size] = array(
					'height'		=> $height,
					'width'			=> $width,
					'url'			=> $base_url . $size_meta['file'],
					'orientation'	=> $height > $width ? 'portrait' : 'landscape',
				);
			}
		}

		$sizes['full'] = array( 'url' => $attachment_url );

		if ( isset( $meta['height'], $meta['width'] ) ) {
			$sizes['full']['height'] = $meta['height'];
			$sizes['full']['width'] = $meta['width'];
			$sizes['full']['orientation'] = $meta['height'] > $meta['width'] ? 'portrait' : 'landscape';
		}

		$response = array_merge( $response, array( 'sizes' => $sizes ), $sizes['full'] );
	} elseif ( $meta && 'video' === $type ) {
		if ( isset( $meta['width'] ) ) {
			$response['width'] = (int) $meta['width'];
		}
		
		if ( isset( $meta['height'] ) ) {
			$response['height'] = (int) $meta['height'];
		}
	}

	if ( $meta && ( 'audio' === $type || 'video' === $type ) ) {
		if ( isset( $meta['length_formatted'] ) ) {
			$response['fileLength'] = $meta['length_formatted'];
		}
		
		$response['meta'] = array();
		foreach ( wp_get_attachment_id3_keys( $attachment, 'js' ) as $key => $label ) {
			$response['meta'][ $key ] = false;

			if ( ! empty( $meta[ $key ] ) ) {
				$response['meta'][ $key ] = $meta[ $key ];
			}
		}

		$id = mpp_get_media_cover_id( $attachment->ID );

		if ( ! empty( $id ) ) {
			list( $url, $width, $height ) = wp_get_attachment_image_src( $id, 'full' );
			$response['image'] = compact( 'url', 'width', 'height' );
			list( $url, $width, $height ) = wp_get_attachment_image_src( $id, 'thumbnail' );
			$response['thumb'] = compact( 'url', 'width', 'height' );
		} else {
			$url = mpp_get_media_cover_src( 'thumbnail', $media->id );
			$width = 48;
			$height = 64;
			$response['image'] = compact( 'url', 'width', 'height' );
			$response['thumb'] = compact( 'url', 'width', 'height' );
		}
	}

	if ( ! in_array( $type, array( 'image', 'audio', 'video' ) ) ) {
		//inject thumbnail
		$url = mpp_get_media_cover_src( 'thumbnail', $media->id );
		$width = 48;
		$height = 64;
		$response['image'] = compact( 'url', 'width', 'height' );
		$response['thumb'] = compact( 'url', 'width', 'height' );
	}

	return apply_filters( 'mpp_prepare_media_for_js', $response, $attachment, $meta );
}

/**
 * Generate & Get wp compatible attachment meta data for the media
 * @param type $media_id
 * @param type $src
 * @return type
 */
function mpp_generate_media_metadata( $media_id, $src ) {

	$storage = mpp_get_storage_manager( $media_id );

	return $storage->generate_metadata( $media_id, $src );
}

/**
 * Check if the current action is media editing/management
 * 
 * @return boolean
 */
function mpp_is_media_management() {

	return mediapress()->is_editing( 'media' ) && mediapress()->is_action( 'edit' );
}

/**
 * Is media delete action?
 * 
 * @return boolean
 */
function mpp_is_media_delete() {

	return mpp_is_media_management() && mediapress()->is_edit_action( 'delete' );
}

function mpp_media_user_can_comment( $media_id ) {

	//for now, just return true
	return true;
	//in future, add an option in settings and aslo we can think of doing something for the user
	if ( mpp_get_option( 'allow_media_comment' ) ) {
		return true;
	}
	
	return false;
}

function mpp_media_record_activity( $args ) {

	$default = array(
		'media_id'	=> null,
		'action'	=> '',
		'content'	=> '',
		'type'		=> '', //type of activity  'create_gallery, update_gallery, media_upload etc'
			//'component'		=> '',// mpp_get_current_component(),
			//'component_id'	=> '',//mpp_get_current_component_id(),
			//'user_id'		=> '',//get_current_user_id(),
	);

	$args = wp_parse_args( $args, $default );

	if ( ! $args['media_id'] ) {
		return false;
	}

	$media_id = absint( $args['media_id'] );

	$media = mpp_get_media( $media_id );


	if ( ! $media ) {
		return false;
	}

	$gallery_id = $media->gallery_id;
	$gallery = mpp_get_gallery( $gallery_id );

	$status = $media->status;
	//when a media is public, make sure to check that the gallery is public too
	if ( $status == 'public' ) {
		$status = mpp_get_gallery_status( $gallery );
	}
	//it is actually a gallery activity, isn't it?
	unset( $args['media_id'] );

	$args['status'] = $status;
	$args['gallery_id'] = $gallery->id; //
	$args['media_ids'] = (array) $media_id;

	return mpp_record_activity( $args );
}

/**
 * Should we show mdia description on single media pages?
 * 
 * @param type $media
 * @return boolean
 */
function mpp_show_media_description( $media = false ) {

	$media = mpp_get_media( $media );

	$show = mpp_get_option( 'show_media_description' ); //under theme tab in admin panel

	return apply_filters( 'mpp_show_media_description', $show, $media );
}
