<?php

/**
 * MediaPress Cached Media query class
 * 
 * This class does not accept fancy parameters, only media ids and looks into the cache to get the results
 * If the given media is not in cache, will not add them to the list
 * 
 * Only use when you are sure that you have cached the ids 
 *
 * @since 1.0.0
 */
class MPP_Cached_Media_Query extends WP_Query {
	
    public function __construct( $query = '' ) {
		
		parent::__construct( $query );
		
		$this->posts = array();
		$this->query( $query );

	}
    
    public function query( $args ) {
       
		if ( isset( $args['in'] ) ) {
		
			$args['post__in'] = $args['in'];
			$args['post_type'] = mpp_get_media_post_type();
			
			unset( $args['in'] );
		}
		//setup query vars
		$this->init();
		//store
		$this->query = $this->query_vars = wp_parse_args( $args );
		//do the fake query
		return $this->get_posts();
		
    }
    
	public function get_posts() {
		//do nothing
		//setup
		//$this->posts
		//$this->post_count
		//$this->found_posts;
		$ids = array();
		
		if ( ! empty( $this->query_vars['post__in'] ) ) {
			$ids = $this->query_vars['post__in'];
		}
		
		$posts = array();
		
		foreach ( $ids as $id ) {
			
			$post = get_post( $id );
			
			if ( ! empty( $post ) ) {
			 $posts[] = $post;//it will be cache hit
			}
		}
		//$posts = array_filter( $posts );
		$this->posts = $posts;
		
		$this->post_count = count( $this->posts );
		
		$this->found_posts = $this->post_count;
		
		return $this->posts;
		
	}
      
    public function get_media() {
        
        return $this->get_posts();
    }
    
    public function next_media() {
        
		return parent::next_post();
		
    }
    //undo the pointer to next
    public function reset_next() {
    	
		$this->current_post--;

		$this->post = $this->posts[$this->current_post];
		
		return $this->post;
        
    }
    
    public function the_media() {
                
        global $post;
		
		$this->in_the_loop = true;

		if ( $this->current_post == -1 ) { // loop has just started
		   do_action_ref_array( 'mediapress_media_loop_start', array( &$this ) );
		}
		 
		$post = $this->next_media();
		        
        setup_postdata( $post );
         
        mediapress()->current_media = mpp_get_media( $post );
        //mpp_setup_media_data( $post );
       
    }
    
    public function have_media() {
        
        return parent::have_posts();
		
    }
    
    public function rewind_media() {
		
        parent::rewind_posts();
		
    }
    
    
    public function is_main_query() {

       return false;//it will never be the main query

    }
    
	public function reset_media_data() {
		
        parent::reset_postdata();
		
		if ( ! empty( $this->post ) ) {
			mediapress()->current_media = mpp_get_media( $this->post );
			
		}
	}

	/**
	 * Utility method to get all the ids in this request
	 * 
	 * @return array of mdia ids
	 */
	public function get_ids() {
		
		return wp_list_pluck( $this->posts, 'ID' );
	}
}
