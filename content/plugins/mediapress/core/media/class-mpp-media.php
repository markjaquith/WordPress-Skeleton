<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MediaPress Media class.
 *
 * Please do not use this class directly instead use mpp_get_media
 * 
 * @see mpp_get_media 
 * 
 * @since 1.0.0
 *
 */
class MPP_Media {

	private $data = array();

	/**
	 * Media id.
	 *
	 * @var int mapped to post ID/Attachment ID
	 */
	public $id;

	/**
	 * Parent Gallery Id.
	 *
	 * @var int post id for the gallery
	 */
	public $gallery_id;

	/**
	 * id of media uploader user.
	 *
	 * A numeric.
	 *
	 * @var int creator id
	 */
	public $user_id = 0;

	/**
	 * The media's local publication time.
	 *
	 * @var string
	 */
	public $date_created = '0000-00-00 00:00:00';

	/**
	 * The media's GMT publication time.
	 *
	 * @var string
	 */
	public $date_created_gmt = '0000-00-00 00:00:00';

	/**
	 * The Media title.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * The media slug
	 * mapped to post_name
	 *
	 * @var string
	 */
	public $slug = '';

	/**
	 * The media description.
	 *
	 * mapped to post_content
	 * 
	 * @var string
	 */
	public $description = '';

	/**
	 * The media excerpt.
	 *
	 * @var string
	 */
	public $excerpt = '';

	/**
	 * The media status
	 *
	 * @var string
	 */
	//public $status = 'public';

	/**
	 *  The Media type
	 * 
	 * audio|video|photo|mixed
	 * 
	 * @var string
	 */
	//public $type = '';
	/**
	 * Whether comments are allowed.
	 *
	 * @var string
	 */
	public $comment_status = 'open';

	/**
	 * The post's password in plain text.
	 *
	 * @var string
	 */
	public $password = '';

	/**
	 * The gallery's local modified time.
	 *
	 * @var string
	 */
	public $date_modified = '0000-00-00 00:00:00';

	/**
	 * The Gallery's GMT modified time.
	 *
	 * @var string
	 */
	public $date_modified_gmt = '0000-00-00 00:00:00';

	/**
	 * A utility DB field for gallery content.
	 *
	 *
	 * @var string
	 */
	public $content_filtered = '';

	/**
	 * A field used for ordering posts.
	 *
	 * @var int
	 */
	public $sort_order = 0;

	/**
	 * Cached comment count.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @var string
	 */
	public $comment_count = 0;

	//public $component;//type of component it is the term_id for components _user|_groups etc
	//public $component_id;//actual id of user/group etc

	/**
	 * was the file actually uploaded by the user? 
	 *
	 * @var bool true if uploaded to local or remote location by the user
	 */
	public $is_uploaded = 0;

	/**
	 * Is the file stored at remote location. (have we used ftp|cdn for storing files?)
	 *
	 * @var bool true if the file is not stored on local server
	 */
	public $is_remote = 0;

	/**
	 * Which remote Service Is being Used (id will dep[end on type of service, It uniquely identifies the remote)
	 *
	 * @var int
	 */
	public $remote_service_id = 0;

	/**
	 * Is imported file, in this case we treat it as local file but store the original url from where it was imported
	 *
	 * @var boolean true if file is imported from somewhere else
	 */
	public $is_imported = 0;

	/**
	 * In case of imported file, from where it was imported?
	 *
	 * @var int
	 */
	public $imported_url = 0;

	/**
	 * Is Embedded content
	 *
	 * @var int
	 */
	public $is_embedded = 0;

	/**
	 * In case of embedded content, from where it originates?
	 *
	 * @var string
	 */
	public $embed_url = 0;

	/**
	 * the html content of the embedded thing?
	 *
	 * @var string
	 */
	public $embed_html = 0;

	/**
	 *
	 * @var MPP_Storage_Manager 
	 */
	public $storage = null;

	public function __construct( $media = false ) {

		$_media = null;

		if ( ! $media ) { 
			return;
		}

		if ( is_numeric( $media ) ) {
			$_media = $this->get_row( $media );
		} else {
			//assuming object
			$_media = $media;
		}

		if ( empty( $_media ) || ! $_media->ID ) {
			return;
		}

		$this->map_object( $_media );
	}

	/**
	 * 
	 * @param type $media_id
	 * @return WP_POST || null
	 */
	public function get_row( $media_id ) {

		return get_post( $media_id );
		
	}

	public function map_object( $media ) {

		//media could be a db row or a self object or a WP_Post object
		if ( is_a( $media, 'MPP_Media' ) ) {
			//should we map or should we throw exception and ask them to use the mpp_get_media
			_doing_it_wrong( 'MPP_Media::__construct', __( 'Please do not call the constructor directly, Instead the recommended way is to use mpp_get_media', 'mediapress' ), '1.0' );
			return;
		}
		
		$field_map = $this->get_field_map();

		foreach ( get_object_vars( $media ) as $key => $value ) {

			if ( isset( $field_map[ $key ] ) ) {
				$this->{$field_map[ $key ]} = $value;
			}
			
		}
	}

	/**
	 * Get field map
	 * 
	 * Maps WordPress post table fields to gallery field
	 * @return type
	 */
	private function get_field_map() {

		return array(
			'ID'					=> 'id',
			'post_author'			=> 'user_id',
			'post_title'			=> 'title',
			'post_content'			=> 'description',
			'post_excerpt'			=> 'excerpt',
			'post_name'				=> 'slug',
			'post_password'			=> 'password',
			'post_date'				=> 'date_created',
			'post_date_gmt'			=> 'date_created_gmt',
			'post_modified'			=> 'date_modified',
			'post_modified_gmt'		=> 'date_modified_gmt',
			'comment_status'		=> 'comment_status',
			'post_content_filtered'	=> 'content_filtered',
			'post_parent'			=> 'gallery_id',
			'menu_order'			=> 'sort_order',
			'comment_count'			=> 'comment_count'
		);
	}

	/**
	 * Get reverse field map
	 * Maps gallery variables to WordPress post table fields  
	 * @return type
	 */
	private function get_reverse_field_map() {

		return array_flip( $this->get_field_map() );
	}

	public function __isset( $key ) {
		
		$exists = false;
		
		if ( isset( $this->data[ $key ] ) ) {
			return true;
		}

		if ( 'component' == $key ) {
			$this->set( $key, mpp_get_object_component( $this->id ) );
			$exists = true;
		} elseif ( 'type' == $key ) {
			$this->set( $key, mpp_get_object_type( $this->id ) );
			$exists = true;
		} elseif ( 'status' == $key ) {
			$this->set( $key, mpp_get_object_status( $this->id ) );
			$exists = true;
		}
		
		if ( $exists ) {
			return $exists;
		}
		
		return metadata_exists( 'post', $this->id, '_mpp_' . $key ); //eg _mpp_is_remote etc on call of $obj->is_remote
	}

	public function __get( $key ) {

		if ( isset( $this->data[ $key ] ) ) {
			return $this->data[ $key ];
		}
		
		if ( 'component' == $key ) {
			$this->set( $key, mpp_get_object_component( $this->id ) );
			return $this->data[ $key ];
		} elseif ( 'type' == $key ) {

			$this->set( $key, mpp_get_object_type( $this->id ) );
			return $this->data[ $key ];
		} elseif ( 'status' == $key ) {
			$this->set( $key, mpp_get_object_status( $this->id ) );
			return $this->data[ $key ];
		}

		$value = mpp_get_media_meta( $this->id, '_mpp_' . $key, true );

		return $value;
	}

	public function __set( $key, $value ) {

		$this->set( $key, $value );
	}

	/**
	 * Convert Object to array
	 * 
	 * @return array
	 */
	public function to_array() {

		$post = get_object_vars( $this );

		foreach ( array( 'ancestors' ) as $key ) {
			
			if ( $this->__isset( $key ) ) {
				$post[$key] = $this->__get( $key );
			}
			
		}

		return $post;
	}

	private function set( $key, $value ) {
		
		$this->data[ $key ] = $value;
		//update cache
		mpp_add_media_to_cache( $this );
	}

}

/**
 * Retrieves Media data given a media id or media object.
 *
 * @param int|object $media media id or media object. Optional, default is the current media from the loop.
 * @param string $output Optional, default is Object. Either OBJECT, ARRAY_A, or ARRAY_N.
 * @param string $filter Optional, default is raw.
 * @return MPP_Media|null MPP_Media on success or null on failure
 */
function mpp_get_media( $media = null, $output = OBJECT ) {

	$_media = null;
	$needs_caching = false;

	//if a media is not given but we are inside the media loop
	if ( empty( $media ) && mediapress()->current_media ) {
		$media = mediapress()->current_media;
	}

	if ( ! $media ) {
		return null;
	}

	//if already an instance of gallery object
	if ( is_a( $media, 'MPP_Media' ) ) {
		$_media = $media;
	} elseif ( is_numeric( $media ) ) {
		$_media = mpp_get_media_from_cache( $media );

		if ( ! $_media ) {
			$_media = new MPP_Media( $media );
			$needs_caching = true;
		}
		
	} elseif ( is_object( $media ) ) {
		$_media = mpp_get_media_from_cache( $media->ID );

		if ( ! $_media ) {
			$_media = new MPP_Media( $media );
			$needs_caching = true;
		}
	}

	//save to cache if not already in cache
	if ( $needs_caching && ! empty( $_media ) && $_media->id ) {
		mpp_add_media_to_cache( $_media );
	}

	if ( empty( $_media ) ) {
		return null;
	}

	if ( ! $_media->id ) {
		return;
	}

	if ( $output == ARRAY_A ) {
		return $_media->to_array();
	} elseif ( $output == ARRAY_N ) {
		return array_values( $_media->to_array() );
	}
	
	return $_media;
}

/**
 * Retrives Media object from cache
 * @access  private
 * @param type $media_id
 * @return type
 */
function mpp_get_media_from_cache( $media_id ) {

	return wp_cache_get( 'mpp_gallery_media_' . $media_id, 'mpp' );
	
}

/**
 * Adds a Media object to cache
 * 
 * @param type $media
 */
function mpp_add_media_to_cache( $media ) {

	wp_cache_set( 'mpp_gallery_media_' . $media->id, $media, 'mpp' );
	
}

//clear media cache
function mpp_delete_media_cache( $media_id ) {

	global $_wp_suspend_cache_invalidation;

	if ( ! empty( $_wp_suspend_cache_invalidation ) ) {
		return;
	}

	if ( mpp_get_media_from_cache( $media_id ) ) {
		wp_cache_delete( 'mpp_gallery_media_' . $media_id, 'mpp' );
	}
}
