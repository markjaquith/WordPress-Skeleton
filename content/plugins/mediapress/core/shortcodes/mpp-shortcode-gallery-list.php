<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Galler Listing shortcode
 */
add_shortcode( 'mpp-list-gallery', 'mpp_shortcode_list_gallery' );

function mpp_shortcode_list_gallery( $atts = null, $content = '' ) {
    //allow everything that can be done to be passed via this shortcode
    
        $defaults = array(
                'type'          => false, //gallery type, all,audio,video,photo etc
                'id'            => false, //pass specific gallery id
                'in'            => false, //pass specific gallery ids as array
                'exclude'       => false, //pass gallery ids to exclude
                'slug'          => false,//pass gallery slug to include
                'status'        => false, //public,private,friends one or more privacy level
                'component'     => false, //one or more component name user,groups, evenets etc
                'component_id'  => false,// the associated component id, could be group id, user id, event id
                'per_page'      => false, //how many items per page
                'offset'        => false, //how many galleries to offset/displace
                'page'          => false,//which page when paged
                'nopaging'      => false, //to avoid paging
                'order'         => 'DESC',//order 
                'orderby'       => 'date',//none, id, user, title, slug, date,modified, random, comment_count, meta_value,meta_value_num, ids
                //user params
                'user_id'       => false,
                'include_users' => false,
                'exclude_users' => false,//users to exclude
                'user_name'     => false,
                'scope'         => false,
                'search_terms'  => '',
            //time parameter
                'year'          => false,//this years
                'month'         => false,//1-12 month number
                'week'          => '', //1-53 week
                'day'           => '',//specific day
                'hour'          => '',//specific hour
                'minute'        => '', //specific minute
                'second'        => '',//specific second 0-60
                'yearmonth'     => false,// yearMonth, 201307//july 2013
                'meta_key'		=> '',
                'meta_value'	=> '',
               // 'meta_query'=>false,
                'fields'    => false,//which fields to return ids, id=>parent, all fields(default)
				'column'	=> 4,
        );
        
    $atts = shortcode_atts( $defaults, $atts );
    
    if ( ! $atts['meta_key'] ) {
        unset( $atts['meta_key'] );
        unset( $atts['meta_value'] );
    }
    
	$shortcode_column = $atts['column'];
	mpp_shortcode_save_gallery_data( 'column', $shortcode_column );
	
	
	unset( $atts['column'] );
	//unset( $atts['view'] );
	$query = new MPP_Gallery_Query( $atts );
	
	mpp_shortcode_save_gallery_data( 'gallery_list_query', $query );
    
    ob_start();
    
	//include temlate
	
	mpp_get_template( 'shortcodes/gallery-list.php' );
	
    $content = ob_get_clean();
	
	mpp_shortcode_reset_gallery_data( 'column' );
    mpp_shortcode_reset_gallery_data( 'gallery_list_query' );
	
    return $content;
}

add_shortcode( 'mpp-show-gallery', 'mpp_shortcode_show_gallery' );

function mpp_shortcode_show_gallery( $atts = null, $content = '' ) {
    
        $defaults = array(
			'id'            => false, //pass specific gallery id
			'in'            => false, //pass specific gallery ids as array
			'exclude'       => false, //pass gallery ids to exclude
			'slug'          => false,//pass gallery slug to include
			'per_page'      => false, //how many items per page
			'offset'        => false, //how many galleries to offset/displace
			'page'          => false,//which page when paged
			'nopaging'      => false, //to avoid paging
			'order'         => 'DESC',//order 
			'orderby'       => 'date',//none, id, user, title, slug, date,modified, random, comment_count, meta_value,meta_value_num, ids
			//user params
			'user_id'       => false,
			'include_users' => false,
			'exclude_users' => false,//users to exclude
			'user_name'     => false,
			'scope'         => false,
			'search_terms'  => '',

			'meta_key'		=> '',
			'meta_value'	=> '',
			'column'		=> 4,
			'view'			=> '',
        );
        
	$defaults = apply_filters( 'mpp_shortcode_show_gallery_defaults', $defaults );
	
    $atts = shortcode_atts( $defaults, $atts );
    
	if ( ! $atts['id'] ) {
		return '';
	}
	
	$gallery_id = absint( $atts['id'] );
	
	global $wpdb;
	
	$attachments = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_type = %s ", $gallery_id, 'attachment' ) ); 
	
	array_push( $attachments, $gallery_id );
	
	_prime_post_caches( $attachments, true, true );
	
	$gallery = mpp_get_gallery( $gallery_id );
	//if gallery does not exist, there is no proint in further proceeding
	if ( ! $gallery ) {
		return '';
	}
	
	
    if ( ! $atts['meta_key'] ) {
        
        unset( $atts['meta_key'] );
        unset( $atts['meta_value'] );
    }
    
	$view = $atts['view'];
	
	unset( $atts['id'] );
	unset( $atts['view'] );
	
	$atts['gallery_id'] = $gallery_id;

	$shortcode_column = $atts['column'];
	mpp_shortcode_save_media_data( 'column', $shortcode_column );
	
	mpp_shortcode_save_media_data( 'shortcode_args', $atts );
	
	unset( $atts['column'] );
	
	$atts = array_filter( $atts );
	
	$query = new MPP_Media_Query( $atts );
	mpp_shortcode_save_media_data( 'query', $query );
		
	$content = apply_filters( 'mpp_shortcode_mpp_show_gallery_content', '', $atts, $view );
	
	if( ! $content ) {
		
		$templates = array(
			'shortcodes/grid.php'
	
		);
		
		if ( $view ) {
			$type = $gallery->type;
					
			$preferred_templates = array(
				"shortcodes/{$view}-{$type}.php",
				"shortcodes/{$view}.php",
			);//audio-playlist, video-playlist
			
			$templates = array_merge( $preferred_templates, $templates );
			//array_unshift( $templates, $preferred_template );
		}
		
		ob_start();
		
		mpp_locate_template( $templates,  true );//load
		 
		$content = ob_get_clean();
	}
	
	mpp_shortcode_reset_media_data( 'column' );
	mpp_shortcode_reset_media_data( 'query' );
	mpp_shortcode_reset_media_data( 'shortcode_args' );
    
    return $content;
}