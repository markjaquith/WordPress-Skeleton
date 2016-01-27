<?php

// Exit if the file is accessed directly over web
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class MPP_Gallery_Query extends WP_Query {

	private $post_type;

	public function __construct ( $query = '' ) {

		$this->post_type = mpp_get_gallery_post_type();

		parent::__construct( $query );
	}

	public function query ( $args ) {
		//make sure that the query params was not built before
		if ( ! isset( $args['_mpp_mapped_query'] ) ) {
			$args = self::build_params( $args );
		}

		parent::query( $args );
	}

	/**
	 * Map gallery parameters to wp_query native parameters
	 * 
	 * @param type $args
	 * @return string
	 */
	public function build_params ( $args ) {

		$defaults = array(
			'type'			=> array_keys( mpp_get_active_types() ), //gallery type, all,audio,video,photo etc
			'id'			=> false, //pass specific gallery id
			'in'			=> false, //pass specific gallery ids as array
			'exclude'		=> false, //pass gallery ids to exclude
			'slug'			=> false, //pass gallery slug to include
			'status'		=> array_keys( mpp_get_active_statuses() ), //public,private,friends one or more privacy level
			'component'		=> array_keys( mpp_get_active_components() ), //one or more component name user,groups, evenets etc
			'component_id'	=> false, // the associated component id, could be group id, user id, event id
			'per_page'		=> mpp_get_option( 'galleries_per_page' ),
			'offset'		=> false, //how many galleries to offset/displace
			'page'			=> false, //which page when paged
			'nopaging'		=> false, //to avoid paging
			'order'			=> 'DESC', //order 
			'orderby'		=> 'date', //none, id, user, title, slug, date,modified, random, comment_count, meta_value,meta_value_num, ids
			//user params
			'user_id'		=> false,
			'include_users' => false,
			'exclude_users' => false, //users to exclude
			'user_name'		=> false,
			'scope'			=> false,
			'search_terms'	=> '',
			//time parameter
			'year'			=> false, //this years
			'month'			=> false, //1-12 month number
			'week'			=> '', //1-53 week
			'day'			=> '', //specific day
			'hour'			=> '', //specific hour
			'minute'		=> '', //specific minute
			'second'		=> '', //specific second 0-60
			'yearmonth'		=> false, // yearMonth, 201307//july 2013
			//'meta_key'=>'',
			// 'meta_value'=>'',
			// 'meta_query'=>false,
			'fields'		=> false, //which fields to return ids, id=>parent, all fields(default)
		);


		//build params for WP_Query


		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );


		//build the wp_query args
		$wp_query_args = array(
			'post_type'			=> mpp_get_gallery_post_type(),
			'post_status'		=> 'any',
			'p'					=> $id,
			'post__in'			=> $in,
			'post__not_in'		=> $exclude,
			'name'				=> $slug,
			'posts_per_page'	=> $per_page,
			'paged'				=> $page,
			'offset'			=> $offset,
			'nopaging'			=> $nopaging,
			//user params
			'author'			=> $user_id,
			'author_name'		=> $user_name,
			'author__in'		=> $include_users,
			'author__not_in'	=> $exclude_users,
			//date time params
			'year'				=> $year,
			'monthnum'			=> $month,
			'w'					=> $week,
			'day'				=> $day,
			'hour'				=> $hour,
			'minute'			=> $minute,
			'second'			=> $second,
			'm'					=> $yearmonth,
			//order by
			'order'				=> $order,
			'orderby'			=> $orderby,
			's'					=> $search_terms,
			//meta key, may be we can set them here?
			// 'meta_key'=>$meta_key,
			//'meta_value'=>$meta_value,
			//which fields to fetch
			'fields'			=> $fields,
			'_mpp_mapped_query'	=> true,
		);

		$tax_query		= array();
		$gmeta_query	= array(); //meta
		

		if ( isset( $meta_key ) && $meta_key ) {
			$wp_query_args['meta_key'] = $meta_key;
		}

		if ( isset( $meta_value ) ) {
			$wp_query_args['meta_value'] = $meta_value;
		}

		if ( isset( $meta_query ) ) {
			$gmeta_query = $meta_query;
		}
		//TODO: SCOPE
		//
    
    
    //we will need to build tax query/meta query
		//type, audio video etc
		//if type is given and it is valid gallery type
		//Pass one or more types
		//should we restrict to active types only here? I guss no, Insteacd the calling scope should take care of that
		if ( ! empty( $type ) && mpp_are_registered_types( $type ) ) {

			$type = mpp_string_to_array( $type );
			$type = array_map( 'mpp_underscore_it', $type );

			$tax_query[] = array(
				'taxonomy'	=> mpp_get_type_taxname(),
				'field'		=> 'slug',
				'terms'		=> $type,
				'operator'	=> 'IN',
			);
		}

		//privacy
		//pass ne or more privacy level
		if ( ! empty( $status ) && mpp_are_registered_statuses( $status ) ) {

			$status = mpp_string_to_array( $status );
			$status = array_map( 'mpp_underscore_it', $status );

			$tax_query[] = array(
				'taxonomy'	=> mpp_get_status_taxname(),
				'field'		=> 'slug',
				'terms'		=> $status,
				'operator'	=> 'IN'
			);
		}

		if ( ! empty( $component ) && mpp_are_registered_components( $component ) ) {

			$component = mpp_string_to_array( $component );
			$component = array_map( 'mpp_underscore_it', $component );

			$tax_query[] = array(
				'taxonomy'	=> mpp_get_component_taxname(),
				'field'		=> 'slug',
				'terms'		=> $component,
				'operator'	=> 'IN'
			);
		}

		//done with the tax query

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		$wp_query_args['tax_query'] = $tax_query;
		// print_nice($wp_query_args);
		//meta query
		//now, for component
		if ( ! empty( $component_id ) ) {
			$meta_compare = '=';

			if ( is_array( $component_id ) ) {
				$meta_compare = 'IN';
			}

			$gmeta_query[] = array(
				'key'		=> '_mpp_component_id',
				'value'		=> $component_id,
				'compare'	=> $meta_compare,
				'type'		=> 'UNSIGNED'
			);
		}

		//reset meta query
		if ( ! empty( $gmeta_query ) ) {
			$wp_query_args['meta_query'] = $gmeta_query;
		}

		// print_r($wp_query_args);
		return $wp_query_args;
	}

	public function get_galleries () {

		return parent::get_posts();
		
	}

	public function next_gallery () {

		return parent::next_post();
		
	}

	public function the_gallery () {

		global $post;
		
		$this->in_the_loop = true;

		if ( $this->current_post == -1 ) { // loop has just started
			do_action_ref_array( 'mediapress_gallery_loop_start', array( &$this ) );
		}

		$post = $this->next_gallery();

		setup_postdata( $post );
		//setup current gallery
		mediapress()->current_gallery = mpp_get_gallery( $post );
		
		mpp_setup_gallery_data( $post );
	}

	public function have_galleries () {

		return parent::have_posts();
		
	}

	public function rewind_galleries () {
		
		parent::rewind_posts();
		
	}

	public function is_main_query () {

		$mediappress = mediapress();

		return $this == $mediappress->the_gallery_query;
	}

	function reset_gallery_data () {
		
		parent::reset_postdata();
		
		if ( ! empty( $this->post ) ) {
			mediapress()->current_gallery = mpp_get_gallery( $this->post );
		}
		
	}

	/**
	 * Putting helpers to allow easy pagination in the loops
	 */
	public function paginate () {

		$total = $this->max_num_pages;
		// only bother with the rest if we have more than 1 page!
		if ( $total > 1 ) {
			// get the current page
			if ( ! $current_page = $this->get( 'paged' ) ) {
				$current_page = 1;
			}
			// structure of “format” depends on whether we’re using pretty permalinks
			$perma_struct = get_option( 'permalink_structure' );
			$format = empty( $perma_struct ) ? '&page=%#%' : 'page/%#%/';

			$link = get_pagenum_link( 1 );

			//$format=$format;
			$base = $link;
			return paginate_links( array(
				'base'		=> $base . '%_%',
				'format'	=> $format,
				'current'	=> $current_page,
				'total'		=> $total,
				'mid_size'	=> 4,
				'type'		=> 'list'
			) );
		}
	}

	public function pagination_count () {

		$paged = $this->get( 'paged' ) ? $this->get( 'paged' ) : 1;
		$posts_pet_page = $this->get( 'posts_per_page' );

		$from_num = intval( ( $paged - 1 ) * $posts_pet_page ) + 1;

		$to_num = ( $from_num + ( $posts_pet_page - 1 ) > $this->found_posts ) ? $this->found_posts : $from_num + ( $posts_pet_page - 1);

		echo sprintf( __( 'Viewing gallery %d to %d (of %d galleries)', 'mediapress' ), $from_num, $to_num, $this->found_posts );
	}

	/**
	 * Get all the ids in this request
	 */
	public function get_ids () {
		
		$ids = array();

		if ( empty( $this->request ) ) {
			return $ids;
		}
		
		global $wpdb;
		
		$ids = $wpdb->get_col( $this->request );
		
		return $ids;
	}
	
	public static function build_from_wp_query( WP_Query $wp_query ) {
		
		$query = new self();
		
		$vars = get_object_vars( $wp_query );
		
		foreach ( $vars as $name => $value ) {
			$query->{$name} = $value; 
		}
		
		return $query;
		
		/*$query->query = $wp_query->query;
		$query->query_vars = $wp_query->query_vars;
		$query->tax_query = $wp_query->tax_query;
		$query->meta_query = $wp_query->meta_query;
		$query->date_query = $wp_query->date_query;
		$query->queried_object = $wp_query->queried_object;
		
		$query->queried_object_id = $wp_query->queried_object_id;
		$query->request = $wp_query->request;
		$query->posts = $wp_query->posts;
		$query->post_count = $wp_query->post_count;
		$query->current_post = $wp_query->current_post;
		$query->in_the_loop = $wp_query->in_the_loop;
		
		$query->post = $wp_query->post;
		
		$query->comments = $wp_query->comments;
		$query->comment_count = $wp_query->comment_count;
		$query->comment = $wp_query->comment;
		$query->found_posts = $wp_query->found_posts;
		
		$query->max_num_pages = $wp_query->max_num_pages;
		$query->max_num_comment_pages = $wp_query->max_num_comment_pages;
		
		$query->is_single = $wp_query->is_single;*/
		
	}

}

//end of class

function mpp_setup_gallery_data ( $post ) {

	//setup gallery data for current gallery 

	return true;
}

/**
 * Reset global gallery data
 */
function mpp_reset_gallery_data () {
	
	if ( mediapress()->the_gallery_query ) {
		mediapress()->the_gallery_query->reset_gallery_data();
	}
	
	wp_reset_postdata();
}

//Cache all the thumbnails on the Gallery loop start
add_action( 'mediapress_gallery_loop_start', '_mpp_cache_gallery_cover' );

function _mpp_cache_gallery_cover ( $query ) {

	if ( empty( $query->posts ) ) {
		return;
	}

	$gallery_ids = wp_list_pluck( $query->posts, 'ID' );

	//$gallery_ids = $query->get_ids();//all gallery ids
	//get all the media ids which are set as thumbnails

	$thumb_ids = array();

	foreach ( (array) $gallery_ids as $gallery_id ) {
		
		$media_id = mpp_get_gallery_cover_id( $gallery_id );
		
		if ( $media_id ) {
			$thumb_ids[] = $media_id;
		}
	}

	if ( ! empty( $thumb_ids ) ) {
		//ok there are times when we are only looking for one gallery, in that case don't do anything
		if ( count( $thumb_ids ) <= 1 ) {
			return;
		}
		
		//_mpp_update_post_caches( $thumb_ids, false, true );
		_prime_post_caches( $thumb_ids, true, true );
		//if we are here, we do query and cache the results
		//$mppq = new MPP_Media_Query( array( 'in'=> $thumb_ids ) );
	}
}

//Cache posts for the given ids
//we use it to improve the performance
//and decrease the no. of queries
function _mpp_update_post_caches ( $post_ids, $update_tax = true, $update_meta = true ) {

	global $wpdb;

	if ( empty( $post_ids ) ) {
		return;
	}

	$post_ids = wp_parse_id_list( $post_ids );

	$list = "(" . join( ',', $post_ids ) . ')';

	$query = "SELECT * FROM {$wpdb->posts} WHERE ID IN {$list}";

	$posts = $wpdb->get_results( $query );

	update_post_caches( $posts, $update_tax, $update_meta );
}
