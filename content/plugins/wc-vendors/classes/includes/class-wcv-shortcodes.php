<?php
/**
 * WCV_Shortcodes class. 
 *
 * @class 		WCV_Shortcodes
 * @version		1.0.0
 * @package		WCVendors/Classes
 * @category	Class
 * @author 		WC Vendors (Jamie Madden http://github.com/digitalchild)
 */
class WCV_Shortcodes {

	/**
	 * Initialise shortcodes
	 */
	function __construct() {
		// Define shortcodes

		// Recent Products 
		add_shortcode( 'wcv_recent_products', array( $this, 'recent_products'));
		// Products by vendor
		add_shortcode( 'wcv_products', array( $this, 'products'));
		//Featured products by vendor
		add_shortcode( 'wcv_featured_products', array( $this, 'featured_products'));
		// Sale products by vendor
		add_shortcode( 'wcv_sale_products', array( $this, 'sale_products'));
		// Top Rated products by vendor 
		add_shortcode( 'wcv_top_rated_products', array( $this, 'top_rated_products'));
		// Best Selling product 
		add_shortcode( 'wcv_best_selling_products', array( $this, 'best_selling_products'));
		// List products in a category shortcode
		add_shortcode( 'wcv_product_category', array( $this, 'product_category'));
		// List of paginated vendors 
		add_shortcode( 'wcv_vendorslist', array( $this, 'wcv_vendorslist' ) );

	}

	public static function get_vendor ( $slug ) { 

		$vendor_id = get_user_by('slug', $slug); 

		if (!empty($vendor_id)) { 
			$author = $vendor_id->ID; 
		} else $author = '';

		return $author; 

	}

	/*
		
		Get recent products based on vendor username 
	
	*/
	public static function recent_products( $atts ) {
			global $woocommerce_loop;
 
			extract( shortcode_atts( array(
				'per_page' 	=> '12',
				'vendor' 	=> '', 
				'columns' 	=> '4',
				'orderby' 	=> 'date',
				'order' 	=> 'desc'
			), $atts ) );
 
			$meta_query = WC()->query->get_meta_query();
			
			$args = array(
				'post_type'				=> 'product',
				'post_status'			=> 'publish',
				'author'				=> self::get_vendor($vendor), 
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' 		=> $per_page,
				'orderby' 				=> $orderby,
				'order' 				=> $order,
				'meta_query' 			=> $meta_query
			);
 
			ob_start();
 
			$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
 
			$woocommerce_loop['columns'] = $columns;
 
			if ( $products->have_posts() ) : ?>
 
				<?php woocommerce_product_loop_start(); ?>
 
					<?php while ( $products->have_posts() ) : $products->the_post(); ?>
 
						<?php wc_get_template_part( 'content', 'product' ); ?>
 
					<?php endwhile; // end of the loop. ?>
 
				<?php woocommerce_product_loop_end(); ?>
 
			<?php endif;
 
			wp_reset_postdata();
 
			return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}

	/**
	 * List all products for a vendor shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function products( $atts ) {
		global $woocommerce_loop;

		if ( empty( $atts ) ) return '';

		extract( shortcode_atts( array(
			'vendor' 	=> '',
			'columns' 	=> '4',
			'orderby'   => 'title',
			'order'     => 'asc'
		), $atts ) );



		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'author'				=> self::get_vendor($vendor), 
			'ignore_sticky_posts'	=> 1,
			'orderby' 				=> $orderby,
			'order' 				=> $order,
			'posts_per_page' 		=> -1,
			'meta_query' 			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array('catalog', 'visible'),
					'compare' 	=> 'IN'
				)
			)
		);

		if ( isset( $atts['skus'] ) ) {
			$skus = explode( ',', $atts['skus'] );
			$skus = array_map( 'trim', $skus );
			$args['meta_query'][] = array(
				'key' 		=> '_sku',
				'value' 	=> $skus,
				'compare' 	=> 'IN'
			);
		}

		if ( isset( $atts['ids'] ) ) {
			$ids = explode( ',', $atts['ids'] );
			$ids = array_map( 'trim', $ids );
			$args['post__in'] = $ids;
		}

		ob_start();

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}


	/**
	 * Output featured products
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function featured_products( $atts ) {
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'vendor' => '',
			'per_page' 	=> '12',
			'columns' 	=> '4',
			'orderby' 	=> 'date',
			'order' 	=> 'desc'
		), $atts ) );

		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'author'				=> self::get_vendor($vendor), 
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $per_page,
			'orderby' 				=> $orderby,
			'order' 				=> $order,
			'meta_query'			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array('catalog', 'visible'),
					'compare'	=> 'IN'
				),
				array(
					'key' 		=> '_featured',
					'value' 	=> 'yes'
				)
			)
		);

		ob_start();

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}
	
	/**
	 * List all products on sale
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function sale_products( $atts ) {
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'vendor' 		=> '', 
			'per_page'      => '12',
			'columns'       => '4',
			'orderby'       => 'title',
			'order'         => 'asc'
		), $atts ) );

		// Get products on sale
		$product_ids_on_sale = wc_get_product_ids_on_sale();

		$meta_query   = array();
		$meta_query[] = WC()->query->visibility_meta_query();
		$meta_query[] = WC()->query->stock_status_meta_query();
		$meta_query   = array_filter( $meta_query );

		$args = array(
			'posts_per_page'	=> $per_page,
			'author'			=> self::get_vendor($vendor), 
			'orderby' 			=> $orderby,
			'order' 			=> $order,
			'no_found_rows' 	=> 1,
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product',
			'meta_query' 		=> $meta_query,
			'post__in'			=> array_merge( array( 0 ), $product_ids_on_sale )
		);

		ob_start();

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}

	/**
	 * List top rated products on sale by vendor
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function top_rated_products( $atts ) {
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'vendor'		=> '', 
			'per_page'      => '12',
			'columns'       => '4',
			'orderby'       => 'title',
			'order'         => 'asc'
			), $atts ) );

		$args = array(
			'post_type' 			=> 'product',
			'author'				=> self::get_vendor($vendor), 
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'   => 1,
			'orderby' 				=> $orderby,
			'order'					=> $order,
			'posts_per_page' 		=> $per_page,
			'meta_query' 			=> array(
				array(
					'key' 			=> '_visibility',
					'value' 		=> array('catalog', 'visible'),
					'compare' 		=> 'IN'
				)
			)
		);

		ob_start();

		add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		remove_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}

	/**
	 * List best selling products on sale per vendor
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function best_selling_products( $atts ) {
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'vendor'		=> '', 
			'per_page'      => '12',
			'columns'       => '4'
		), $atts ) );

		$args = array(
			'post_type' 			=> 'product',
			'post_status' 			=> 'publish',
			'author'				=> self::get_vendor($vendor), 
			'ignore_sticky_posts'   => 1,
			'posts_per_page'		=> $per_page,
			'meta_key' 		 		=> 'total_sales',
			'orderby' 		 		=> 'meta_value_num',
			'meta_query' 			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array( 'catalog', 'visible' ),
					'compare' 	=> 'IN'
				)
			)
		);

		ob_start();

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}

	/**
	 * List products in a category shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function product_category( $atts ) {
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'vendor'   => '', 
			'per_page' => '12',
			'columns'  => '4',
			'orderby'  => 'title',
			'order'    => 'desc',
			'category' => '',  // Slugs
			'operator' => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
		), $atts ) );

		if ( ! $category ) {
			return '';
		}

		// Default ordering args
		$ordering_args = WC()->query->get_catalog_ordering_args( $orderby, $order );

		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'author'				=>  self::get_vendor($vendor),
			'ignore_sticky_posts'	=> 1,
			'orderby' 				=> $ordering_args['orderby'],
			'order' 				=> $ordering_args['order'],
			'posts_per_page' 		=> $per_page,
			'meta_query' 			=> array(
				array(
					'key' 			=> '_visibility',
					'value' 		=> array('catalog', 'visible'),
					'compare' 		=> 'IN'
				)
			),
			'tax_query' 			=> array(
				array(
					'taxonomy' 		=> 'product_cat',
					'terms' 		=> array_map( 'sanitize_title', explode( ',', $category ) ),
					'field' 		=> 'slug',
					'operator' 		=> $operator
				)
			)
		);

		if ( isset( $ordering_args['meta_key'] ) ) {
			$args['meta_key'] = $ordering_args['meta_key'];
		}

		ob_start();

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		woocommerce_reset_loop();
		wp_reset_postdata();

		$return = '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';

		// Remove ordering query arguments
		WC()->query->remove_ordering_args();

		return $return;
	}

	/**
	*	vendors_with_products - Get vendors with products pubilc or private 
	*	@param array $query 	
	*/
	public function vendors_with_products( $query ) {

		global $wpdb; 

		// $post_count = $products ? ' AND post_count  > 0 ' : ''; 

	    if ( isset( $query->query_vars['query_id'] ) && 'vendors_with_products' == $query->query_vars['query_id'] ) {  
	        $query->query_from = $query->query_from . ' LEFT OUTER JOIN (
	                SELECT post_author, COUNT(*) as post_count
	                FROM '.$wpdb->prefix.'posts
	                WHERE post_type = "product" AND (post_status = "publish" OR post_status = "private")
	                GROUP BY post_author
	            ) p ON ('.$wpdb->prefix.'users.ID = p.post_author)';
	        $query->query_where = $query->query_where . ' AND post_count  > 0 ' ;  
	    } 
	}

	/**
	  * 	list of vendors 
	  * 
	  * 	@param $atts shortcode attributs 
	*/
	public function wcv_vendorslist( $atts ) {

		$html = ''; 
		
	  	extract( shortcode_atts( array(
	  			'orderby' 		=> 'registered',
	  			'order'			=> 'ASC',
				'per_page'      => '12',
				'columns'       => '4', 
				'show_products'	=> 'yes' 
			), $atts ) );

	  	$paged      = ( get_query_var('paged') ) ? get_query_var('paged') : 1;   
	  	$offset     = ( $paged - 1 ) * $per_page;

	  	// Hook into the user query to modify the query to return users that have at least one product 
	  	if ($show_products == 'yes') add_action( 'pre_user_query', array( $this, 'vendors_with_products') );

	  	// Get all vendors 
	  	$vendor_total_args = array ( 
	  		'role' 				=> 'vendor', 
	  		'meta_key' 			=> 'pv_shop_slug', 
  			'meta_value'   		=> '',
			'meta_compare' 		=> '>',
			'orderby' 			=> $orderby,
  			'order'				=> $order,
	  	);

	  	if ($show_products == 'yes') $vendor_total_args['query_id'] = 'vendors_with_products'; 

	  	$vendor_query = New WP_User_Query( $vendor_total_args ); 
	  	$all_vendors =$vendor_query->get_results(); 

	  	// Get the paged vendors 
	  	$vendor_paged_args = array ( 
	  		'role' 				=> 'vendor', 
	  		'meta_key' 			=> 'pv_shop_slug', 
  			'meta_value'   		=> '',
			'meta_compare' 		=> '>',
			'orderby' 			=> $orderby,
  			'order'				=> $order,
	  		'offset' 			=> $offset, 
	  		'number' 			=> $per_page, 
	  	);

	  	if ($show_products == 'yes' ) $vendor_paged_args['query_id'] = 'vendors_with_products'; 

	  	$vendor_paged_query = New WP_User_Query( $vendor_paged_args ); 
	  	$paged_vendors = $vendor_paged_query->get_results(); 

	  	// Pagination calcs 
		$total_vendors = count( $all_vendors );  
		$total_vendors_paged = count($paged_vendors);  
		$total_pages = intval( $total_vendors / $per_page ) + ( $total_vendors % $per_page );
	    
	   	ob_start();

	    // Loop through all vendors and output a simple link to their vendor pages
	    foreach ($paged_vendors as $vendor) {
	       wc_get_template( 'vendor-list.php', array(
	      												'shop_link'			=> WCV_Vendors::get_vendor_shop_page($vendor->ID), 
														'shop_name'			=> $vendor->pv_shop_name, 
														'vendor_id' 		=> $vendor->ID, 
														'shop_description'	=> $vendor->pv_shop_description, 
												), 'wc-vendors/front/', wcv_plugin_dir . 'templates/front/' );
	    } // End foreach 
	   	
	   	$html .= '<ul class="wcv_vendorslist">' . ob_get_clean() . '</ul>';

	    if ($total_vendors > $total_vendors_paged) {  
			$html .= '<div class="wcv_pagination">';  
			  $current_page = max( 1, get_query_var('paged') );  
			  $html .= paginate_links( 	array(  
			        'base' => get_pagenum_link( ) . '%_%',  
			        'format' => 'page/%#%/',  
			        'current' => $current_page,  
			        'total' => $total_pages,  
			        'prev_next'    => false,  
			        'type'         => 'list',  
			    ));  
			$html .= '</div>'; 
		}

	    return $html; 
	}

}