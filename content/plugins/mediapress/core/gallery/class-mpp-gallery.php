<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gallery Class
 */

/**
 * MediaPress Gallery class.
 *
 * @since 1.0
 *
 * @property string $type Gallery Type ( e.g photo|audio|video etc )
 * @property string $status Gallery Statues( e.g public|private| friendsonly etc )
 * @property string $component Associated component name( e.g member|groups etc )
 * @property int $component_id Associated component object id( e.g group id or user id )
 * @property int $cover_id Theattachment/media id for the cover of this gallery
 * @property int $media_count number of media in this gallery ( It gives count of all media, does not look at the privacy of media )
 * 
 */
class MPP_Gallery {

	private $data = array();

	/**
	 * Gallery id.
	 *
	 * mapped to post ID
	 * 
	 * @var int 
	 */
	public $id;

	/**
	 * id of gallery creator mapped to post_author
	 *
	 * A numeric.
	 *
	 * @var string
	 */
	public $user_id = 0;

	/**
	 * The post's local publication time.
	 * 
	 * mapped to post_date
	 * 
	 * @var string
	 */
	public $date_created = '0000-00-00 00:00:00';

	/**
	 * The post's GMT publication time.
	 * 
	 * mapped to post_date_gmt
	 * 
	 * @var string
	 */
	public $date_created_gmt = '0000-00-00 00:00:00';

	/**
	 * The post's title.
	 * 
	 * mapped to post_title
	 * 
	 * @var string
	 */
	public $title = '';

	/**
	 * The gallery slug.
	 * 
	 * mapped to post_name
	 * 
	 * @var string
	 */
	public $slug = '';

	/**
	 * The post's content.
	 *
	 * mapped to post_content
	 * 
	 * @var string
	 */
	public $description = '';

	/**
	 * The post's excerpt.
	 * 
	 * mapped to post_excerpt
	 * 
	 * @var string
	 */
	public $excerpt = '';

	/**
	 * The post's status.
	 *
	 * @var string
	 */
	// public $status = 'public';

	/**
	 *  The Gallery type
	 * 
	 * audio|video|photo|mixed
	 * 
	 * @var string
	 */
	// public $type = '';

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
	 * ID of parent gallery if hierarchies are allowed.
	 *
	 * @var int
	 */
	public $parent = 0;

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

	/**
	 * Below fields are stored in post/gallery meta as _mpp_$key and accesed via magic method
	 */
	/*
	  public $cover_id; //cover image id

	  public $component; //type of component it is the term_id for components _user|_groups etc
	  //public $component_id; //actual id of user/group etc stored as _mpp_component_id

	  //public $media_count; //accessed via magic methid, stored as _mpp_media_count
	 */



	public function __construct( $gallery = false ) {

		$_gallery = null;

		if ( ! $gallery ) {
			return;
		}
		//now the $gallery is either int or object
		if ( is_numeric( $gallery ) ) {
			$_gallery = $this->get_row( $gallery );
		} else {
			$_gallery = $gallery;
		}

		if ( empty( $_gallery ) || ! $_gallery->ID ) {
			return;
		}

		$this->map_object( $_gallery );
	}

	/**
	 * Get teh DB row corresponding to this post id
	 * 
	 * @global type $wpdb
	 * @param type $id
	 * @return type
	 */
	private function get_row( $id ) {

		return get_post( $id );
		
	}

	/**
	 * Maps a DB Object to MPP_Gallery 
	 * 
	 * @param type $_gallery
	 */
	private function map_object( $_gallery ) {

		$field_map = $this->get_field_map();

		foreach ( get_object_vars( $_gallery ) as $key => $value ) {

			if ( isset( $field_map[ $key ] ) ) {
				$this->{$field_map[ $key ]} = $value;
			}
		}
		//there is no harm in doing this
		_prime_post_caches( (array) $_gallery->ID, true, true );
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
			'post_content_filtered' => 'content_filtered',
			'post_parent'			=> 'parent',
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

		if ( isset( $this->data[$key] ) ) {
			return true;
		}

		if ( 'component' == $key ) {
			$this->set( $key, mpp_get_object_component( $this->id ) );
			return true;
		} elseif ( 'type' == $key ) {
			$this->set( $key, mpp_get_object_type( $this->id ) );
			return true;
		} elseif ( 'status' == $key ) {
			$this->set( $key, mpp_get_object_status( $this->id ) );
			return true;
		}

		return metadata_exists( 'post', $this->id, '_mpp_' . $key );
	}

	public function __get( $key ) {

		if ( isset( $this->data[$key] ) ) {
			return $this->data[$key];
		}

		if ( 'component' == $key ) {
			$this->set( $key, mpp_get_object_component( $this->id ) );
			return $this->data[$key];
		} elseif ( 'type' == $key ) {
			$this->set( $key, mpp_get_object_type( $this->id ) );
			return $this->data[$key];
		} elseif ( 'status' == $key ) {
			$this->set( $key, mpp_get_object_status( $this->id ) );
			return $this->data[$key];
		}

		$value = mpp_get_gallery_meta( $this->id, '_mpp_' . $key, true );

		return $value;
	}

	public function __set( $key, $value ) {

		$this->set( $key, $value );
	}

	/**
	 * Converts Gallery object to associative array of field=>val
	 * 
	 * @return type
	 */
	public function to_array() {

		$data = get_object_vars( $this );

		foreach ( array( 'ancestors' ) as $key ) {
			if ( $this->__isset( $key ) )
				$data[ $key ] = $this->__get( $key );
		}

		return $data;
	}

	private function set( $key, $value ) {
		$this->data[ $key ] = $value;
		//update cache
		mpp_add_gallery_to_cache( $this );
	}

}

/**
 * Retrieves gallery data given a gallery id or gallery object.
 *
 * @param int|object $gallery gallery id or gallery object. Optional, default is the current gallery from the loop.
 * @param string $output Optional, default is Object. Either OBJECT, ARRAY_A, or ARRAY_N.
 * @param string $filter Optional, default is raw.
 * 
 * @return MPP_Gallery|null MPP_Gallery on success or null on failure
 */
function mpp_get_gallery( $gallery = null, $output = OBJECT ) {

	$_gallery = null;
	$needs_caching = false;

	//if gallery is not given, but we do have current_gallery setup

	if ( empty( $gallery ) && mediapress()->current_gallery ) {
		$gallery = mediapress()->current_gallery;
	}

	if ( ! $gallery ) {
		return null;
	}

	//if already an instance of gallery object
	if ( is_a( $gallery, 'MPP_Gallery' ) ) {
		$_gallery = $gallery;
	} elseif ( is_numeric( $gallery ) ) {
		$_gallery = mpp_get_gallery_from_cache( $gallery );

		if ( ! $_gallery ) {
			$_gallery = new MPP_Gallery( $gallery );
			$needs_caching = true;
		}
		
	} elseif ( is_object( $gallery ) ) {

		//first check if we already have it cached
		$_gallery = mpp_get_gallery_from_cache( $gallery->ID );

		if ( ! $_gallery ) {
			$_gallery = new MPP_Gallery( $gallery );
			$needs_caching = true;
		}
	}
	//save to cache if not already in cache
	if ( $needs_caching && ! empty( $_gallery ) && $_gallery->id ) {
		mpp_add_gallery_to_cache( $_gallery );
	}

	if ( ! $_gallery ) {
		return null;
	}

	//if the gallery has no id set
	if ( ! $_gallery->id ) {
		return null;
	}

	if ( $output == ARRAY_A ) {
		return $_gallery->to_array();
	} elseif ( $output == ARRAY_N ) {
		return array_values( $_gallery->to_array() );
	}
	
	return $_gallery;
}

//mind it, mpp is not global group
function mpp_get_gallery_from_cache( $gallery_id ) {

	return wp_cache_get( 'mpp_gallery_' . $gallery_id, 'mpp' );
}

function mpp_add_gallery_to_cache( $gallery ) {

	wp_cache_set( 'mpp_gallery_' . $gallery->id, $gallery, 'mpp' );
}

/**
 * Delete gallery from cache
 * 
 * @param type $gallery_id
 */
function mpp_delete_gallery_cache( $gallery_id ) {
	global $_wp_suspend_cache_invalidation;

	if ( ! empty( $_wp_suspend_cache_invalidation ) ) {
		return;
	}

	if ( mpp_get_gallery_from_cache( $gallery_id ) ) {
		wp_cache_delete( 'mpp_gallery_' . $gallery_id, 'mpp' );
	}
}
