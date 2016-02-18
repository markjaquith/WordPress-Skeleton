<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooSidebars Base Class
 *
 * All functionality pertaining to core functionality of the WooSidebars plugin.
 *
 * @package WordPress
 * @subpackage WooSidebars
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * public $version
 * private $file
 * public $upper_limit
 *
 * private $token
 * private $prefix
 * public $conditions
 *
 * private $plugin_url
 * private $assets_url
 *
 * - __construct()
 * - init()
 * - register_post_type_columns()
 * - register_post_type()
 * - register_custom_columns()
 * - register_custom_column_headings()
 * - meta_box_setup()
 * - meta_box_content()
 * - meta_box_save()
 * - description_meta_box()
 * - enter_title_here()
 * - update_messages()
 * - get_registered_sidebars()
 * - register_custom_sidebars()
 * - init_sidebar_replacement()
 * - replace_sidebars()
 * - find_best_sidebars()
 * - enqueue_styles()
 * - add_post_column_headings()
 * - add_post_column_data()
 * - enable_custom_post_sidebars()
 * - multidimensional_search()
 * - add_contextual_help()
 *
 * - load_localisation()
 * - activation()
 * - register_plugin_version()
 */
class Woo_Sidebars {
	public $version;
	private $file;
	public $upper_limit;

	private $token;
	private $prefix;
	public $conditions;

	private $plugin_url;
	private $assets_url;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct ( $file ) {
		$this->version = '';
		$this->file = $file;
		$this->upper_limit = intval( apply_filters( 'woosidebars_upper_limit', 200 ) );

		$this->token = 'sidebar';
		$this->prefix = 'woo_sidebar_';

		/* Plugin URL/path settings. */
		$this->plugin_url = str_replace( '/classes', '', plugins_url( plugin_basename( dirname( __FILE__ ) ) ) );
		$this->assets_url = $this->plugin_url . '/assets';

		$this->conditions = new Woo_Conditions();
		$this->conditions->token = $this->token;
	} // End __construct()

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init () {
		add_action( 'init', array( $this, 'load_localisation' ) );

		add_action( 'init', array( $this, 'register_post_type' ), 20 );
		add_action( 'admin_menu', array( $this, 'meta_box_setup' ), 20 );
		add_action( 'save_post', array( $this, 'meta_box_save' ) );
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
		add_filter( 'post_updated_messages', array( $this, 'update_messages' ) );
		add_action( 'widgets_init', array( $this, 'register_custom_sidebars' ) );
		add_action( 'get_header', array( $this, 'init_sidebar_replacement' ) );

		if ( is_admin() ) {
			global $pagenow;

			add_action( 'admin_print_styles', array( $this, 'enqueue_styles' ), 12 );
			add_action( 'admin_head', array( $this, 'add_contextual_help' ) );
			if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && esc_attr( $_GET['post_type'] ) == $this->token ) {
				add_filter( 'manage_edit-' . $this->token . '_columns', array( $this, 'register_custom_column_headings' ), 10, 1 );
				add_action( 'manage_posts_custom_column', array( $this, 'register_custom_columns' ), 10, 2 );
			}
		}

		// By default, add post type support for sidebars to the "post" post type.
		add_post_type_support( 'post', 'woosidebars' );

		add_action( 'admin_head', array( $this, 'register_post_type_columns' ) );

		add_action( 'wp_ajax_woosidebars-post-enable', array( $this, 'enable_custom_post_sidebars' ) );

		// Run this on activation.
		register_activation_hook( $this->file, array( $this, 'activation' ) );
	} // End init()

	/**
	 * register_post_type_columns function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_type_columns () {
		$post_type = get_post_type();

		if ( $post_type != '' && post_type_supports( $post_type, 'woosidebars' ) ) {
			add_filter( 'manage_edit-' . $post_type . '_columns', array( $this, 'add_post_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( $this, 'add_post_column_data' ), 10, 2 );
			add_action( 'manage_pages_custom_column', array( $this, 'add_post_column_data' ), 10, 2 );
		}
	} // End register_post_type_columns()

	/**
	 * register_post_type function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_type () {
		// Allow only users who can adjust the theme to view the WooSidebars admin.
		if ( ! current_user_can( 'edit_theme_options' ) ) return;

		$page = 'themes.php';

		$singular = __( 'Widget Area', 'woosidebars' );
		$plural = __( 'Widget Areas', 'woosidebars' );
		$rewrite = array( 'slug' => 'sidebars' );
		$supports = array( 'title', 'excerpt' );

		if ( $rewrite == '' ) { $rewrite = $this->token; }

		$labels = array(
			'name' => _x( 'Widget Areas', 'post type general name', 'woosidebars' ),
			'singular_name' => _x( 'Widget Area', 'post type singular name', 'woosidebars' ),
			'add_new' => _x( 'Add New', 'Widget Area' ),
			'add_new_item' => sprintf( __( 'Add New %s', 'woosidebars' ), $singular ),
			'edit_item' => sprintf( __( 'Edit %s', 'woosidebars' ), $singular ),
			'new_item' => sprintf( __( 'New %s', 'woosidebars' ), $singular ),
			'all_items' => sprintf( __( 'Widget Areas', 'woosidebars' ), $plural ),
			'view_item' => sprintf( __( 'View %s', 'woosidebars' ), $singular ),
			'search_items' => sprintf( __( 'Search %a', 'woosidebars' ), $plural ),
			'not_found' =>  sprintf( __( 'No %s Found', 'woosidebars' ), $plural ),
			'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', 'woosidebars' ), $plural ),
			'parent_item_colon' => '',
			'menu_name' => $plural

		);
		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'show_in_admin_bar' => false,
			'show_in_menu' => $page,
			'query_var' => true,
			'rewrite' => $rewrite,
			'capability_type' => 'post',
			'has_archive' => 'sidebars',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => $supports
		);
		register_post_type( $this->token, $args );
	} // End register_post_type()

	/**
	 * register_custom_columns function.
	 *
	 * @access public
	 * @param string $column_name
	 * @param int $id
	 * @return void
	 */
	public function register_custom_columns ( $column_name, $id ) {
		global $wpdb, $post;

		$meta = get_post_custom( $id );
		$sidebars = $this->get_registered_sidebars();

		$this->conditions->setup_default_conditions_reference();

		switch ( $column_name ) {

			case 'sidebar_to_replace':
				$value = '';

				if ( isset( $meta['_sidebar_to_replace'] ) && ( $meta['_sidebar_to_replace'][0] != '' ) ) {
					$value = $meta['_sidebar_to_replace'][0];

					if ( isset( $sidebars[$value] ) ) {
						$value = $sidebars[$value]['name'];
					} else {
						$value .= '<br /><strong>' . __( '(Not in use by current theme)', 'woosidebars' ) . '</strong>';
					}
				}

				echo $value;
			break;

			case 'condition':
				$value = '';

				if ( isset( $meta['_condition'] ) && ( $meta['_condition'][0] != '' ) ) {
					foreach ( $meta['_condition'] as $k => $v ) {
						$value .= $this->multidimensional_search( $v, $this->conditions->conditions_reference ) . '<br />' . "\n";
					}
				}

				echo $value;
			break;

			default:
			break;

		}
	} // End register_custom_columns()

	/**
	 * register_custom_column_headings function.
	 *
	 * @access public
	 * @param array $defaults
	 * @return void
	 */
	public function register_custom_column_headings ( $defaults ) {
		$this->conditions->setup_default_conditions_reference();

		$new_columns = array( 'sidebar_to_replace' => __( 'Sidebar To Replace', 'woosidebars' ), 'condition' => __( 'Condition(s)', 'woosidebars' ) );

		$last_item = '';

		if ( isset( $defaults['date'] ) ) { unset( $defaults['date'] ); }

		if ( count( $defaults ) > 2 ) {
			$last_item = array_slice( $defaults, -1 );

			array_pop( $defaults );
		}
		$defaults = array_merge( $defaults, $new_columns );

		if ( $last_item != '' ) {
			foreach ( $last_item as $k => $v ) {
				$defaults[$k] = $v;
				break;
			}
		}

		return $defaults;
	} // End register_custom_column_headings()

	/**
	 * meta_box_setup function.
	 *
	 * @access public
	 * @return void
	 */
	public function meta_box_setup () {
		add_meta_box( 'sidebar-to-replace', __( 'Sidebar To Replace', 'woosidebars' ), array( $this, 'meta_box_content' ), $this->token, 'side', 'low' );

		// Remove "Custom Settings" meta box.
		remove_meta_box( 'woothemes-settings', 'sidebar', 'normal' );

		// Customise the "Excerpt" meta box for the sidebars.
		remove_meta_box( 'postexcerpt', $this->token, 'normal' );
		add_meta_box( 'sidebar-description', __( 'Description', 'woosidebars' ), array( $this, 'description_meta_box' ), $this->token, 'normal', 'core' );
	} // End meta_box_setup()

	/**
	 * meta_box_content function.
	 *
	 * @access public
	 * @return void
	 */
	public function meta_box_content () {
		global $post_id;

		$sidebars = $this->get_registered_sidebars();

		$selected_sidebar = get_post_meta( $post_id, '_sidebar_to_replace', true );

		$html = '';

		$html .= '<input type="hidden" name="woo_' . $this->token . '_noonce" id="woo_' . $this->token . '_noonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

		if ( count( $sidebars ) > 0 ) {
			$html .= '<select name="sidebar_to_replace" class="widefat">' . "\n";
				foreach ( $sidebars as $k => $v ) {
					$html .= '<option value="' . $v['id'] . '"' . selected( $selected_sidebar, $v['id'], false ) . '>' . $v['name'] . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
		} else {
			$html .= '<p>' . __( 'No sidebars are available with this theme.', 'woosidebars' ) . '</p>';
		}

		echo $html;

	} // End meta_box_content()

	/**
	 * meta_box_save function.
	 *
	 * @access public
	 * @param int $post_id
	 * @return void
	 */
	public function meta_box_save ( $post_id ) {
		global $post, $messages;

		// Verify
		if ( ( get_post_type() != $this->token ) || ! wp_verify_nonce( $_POST['woo_' . $this->token . '_noonce'], plugin_basename( __FILE__ ) ) ) {
			return $post_id;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$fields = array( 'sidebar_to_replace' );

		foreach ( $fields as $f ) {

			${$f} = strip_tags(trim($_POST[$f]));

			if ( get_post_meta( $post_id, '_' . $f ) == '' ) {
				add_post_meta( $post_id, '_' . $f, ${$f}, true );
			} elseif( ${$f} != get_post_meta( $post_id, '_' . $f, true ) ) {
				update_post_meta( $post_id, '_' . $f, ${$f} );
			} elseif ( ${$f} == '' ) {
				delete_post_meta( $post_id, '_' . $f, get_post_meta( $post_id, '_' . $f, true ) );
			}
		}
	} // End meta_box_save()

	/**
	 * description_meta_box function.
	 *
	 * @param object $post
	 */
	public function description_meta_box ( $post ) {
	?>
	<label class="screen-reader-text" for="excerpt"><?php _e( 'Description', 'woosidebars' ); ?></label><textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
	<p><?php printf( __( 'Add an optional description, to be displayed when adding widgets to this widget area on the %sWidgets%s screen.', 'woosidebars' ), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">', '</a>' ); ?></p>
	<?php
	} // End description_meta_box()

	/**
	 * enter_title_here function.
	 *
	 * @access public
	 * @param string $title
	 * @return void
	 */
	public function enter_title_here ( $title ) {
		if ( get_post_type() == $this->token ) {
			$title = __( 'Enter widget area name here', 'woosidebars' );
		}
		return $title;
	} // End enter_title_here()

	/**
	 * update_messages function.
	 *
	 * @access public
	 * @param array $messages
	 * @return void
	 */
	public function update_messages ( $messages ) {
		if ( get_post_type() != $this->token ) {
			return $messages;
		}

		$messages[$this->token][1] = __( 'Widget Area updated.', 'woosidebars' );

		return $messages;
	} // End update_messages()

	/**
	 * get_registered_sidebars function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_registered_sidebars () {
		global $wp_registered_sidebars;

		$sidebars = array();
		$to_ignore = array();

		$custom_sidebars = get_posts( array( 'post_type' => 'sidebar', 'numberposts' => intval( $this->upper_limit ), 'suppress_filters' => 'false' ) );
		if ( ! is_wp_error( $custom_sidebars ) && count( $custom_sidebars ) > 0 ) {
			foreach ( $custom_sidebars as $k => $v ) {
				$to_ignore[] = $v->post_name;
			}
		}

		if ( is_array( $wp_registered_sidebars ) && ( count( $wp_registered_sidebars ) > 0 ) ) {
			foreach ( $wp_registered_sidebars as $k => $v ) {
				if ( ! stristr( $v['id'], $this->prefix ) && ! stristr( $v['id'], 'woo_sbm_' ) && ! in_array( $v['id'], $to_ignore ) ) {
					$sidebars[$k] = $v;
				}
			}
		}

		return $sidebars;
	} // End get_registered_sidebars()

	/**
	 * register_custom_sidebars function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_custom_sidebars () {
		$sidebars = get_posts( array( 'post_type' => 'sidebar', 'posts_per_page' => intval( $this->upper_limit ), 'suppress_filters' => 'false' ) );

		if ( count( $sidebars ) > 0 ) {
			foreach ( $sidebars as $k => $v ) {
				$sidebar_id = $v->post_name;
				// $sidebar_id = $this->prefix . $v->ID;
				register_sidebar( array( 'name' => $v->post_title, 'id' => $sidebar_id, 'description' => $v->post_excerpt ) );
			}
		}
	} // End register_custom_sidebars()

	/**
	 * init_sidebar_replacement function.
	 *
	 * @access public
	 * @return void
	 */
	public function init_sidebar_replacement () {
		add_filter( 'sidebars_widgets', array( $this, 'replace_sidebars' ) );
	} // End init_sidebar_replacement()

	/**
	 * replace_sidebars function.
	 *
	 * @access public
	 * @param array $sidebars_widgets
	 * @return void
	 */
	public function replace_sidebars ( $sidebars_widgets ) {
		if ( is_admin() ) {
	 		return $sidebars_widgets;
	 	}

		// Determine the conditions to construct the query.
		$conditions = $this->conditions->conditions;

		if ( ! isset( $this->conditions->conditions ) || count( $this->conditions->conditions ) <= 0 ) {
			return $sidebars_widgets;
		}

	 	global $woo_custom_sidebar_data;

	 	if ( ! isset( $woo_custom_sidebar_data ) ) {

		 	$conditions_str = join( ', ', $conditions );

		 	$args = array(
		 		'post_type' => $this->token,
		 		'posts_per_page' => intval( $this->upper_limit ),
		 		'suppress_filters' => 'false'
		 	);

		 	$meta_query = array(
		 					'key' => '_sidebar_to_replace',
		 					'compare' => '!=',
		 					'value' => ''
		 					);

		 	$args['meta_query'][] = $meta_query;

		 	$meta_query = array(
		 					'key' => '_condition',
		 					'compare' => 'IN',
		 					'value' => $conditions
		 					);

		 	$args['meta_query'][] = $meta_query;

		 	$sidebars = get_posts( $args );

		 	if ( count( $sidebars ) > 0 ) {
		 		foreach ( $sidebars as $k => $v ) {
		 			$to_replace = get_post_meta( $v->ID, '_sidebar_to_replace', true );
		 			$sidebars[$k]->to_replace = $to_replace;

		 			$conditions = get_post_meta( $v->ID, '_condition', false );

		 			$sidebars[$k]->conditions = array();

		 			// Remove any irrelevant conditions from the array.
		 			if ( is_array( $conditions ) ) {
		 				foreach ( $conditions as $i => $j ) {
		 					if ( in_array( $j, $this->conditions->conditions ) ) {
		 						$sidebars[$k]->conditions[] = $j;
		 					}
		 				}
		 			}

		 		}
		 	}

		 	$woo_custom_sidebar_data = $sidebars;
	 	}

		// Make sure only the most appropriate sidebars are kept.
		// $woo_custom_sidebar_data = $this->remove_unwanted_sidebars( $woo_custom_sidebar_data );
		$woo_custom_sidebar_data = $this->find_best_sidebars( $woo_custom_sidebar_data );

	 	if ( count( $woo_custom_sidebar_data ) > 0 ) {
	 		foreach ( $woo_custom_sidebar_data as $k => $v ) {
	 			$sidebar_id = $v->post_name;
				// $sidebar_id = $this->prefix . $v->ID;
	 			if ( isset( $sidebars_widgets[$sidebar_id] ) && isset( $v->to_replace ) && $v->to_replace != '' ) {
				 	$widgets = $sidebars_widgets[$sidebar_id];
					unset( $sidebars_widgets[$sidebar_id] );
					$sidebars_widgets[$v->to_replace] = $widgets;
				}
	 		}
	 	}

		return $sidebars_widgets;
	} // End replace_sidebars()

	/**
	 * find_best_sidebars function.
	 *
	 * @access public
	 * @param array $sidebars
	 * @return array $sorted_sidebars
	 */
	public function find_best_sidebars ( $sidebars ) {
		$sorted_sidebars = array();

		if ( ! isset( $this->conditions->conditions ) || count( $this->conditions->conditions ) <= 0 ) {
			return $sidebars;
		}

		// Keep track of each sidebar we'd like to replace widgets for.
		foreach ( $sidebars as $k => $v ) {
			if ( isset( $v->to_replace ) && ( $v->to_replace != '' ) && ! isset( $sorted_sidebars[$v->to_replace] ) ) {
				$sorted_sidebars[$v->to_replace] = '';
			}
		}

		foreach ( $sidebars as $k => $v ) {
			if ( isset( $sorted_sidebars[$v->to_replace] ) && ( $sorted_sidebars[$v->to_replace] == '' ) ) {
				$sorted_sidebars[$v->to_replace] = $v;
			} else {
				continue;
			}
		}

		return $sorted_sidebars;
	} // End find_best_sidebars()

	/**
	 * enqueue_styles function.
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_styles () {
		global $pagenow;

		if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
			if ( get_post_type() != $this->token ) { return; }
			wp_enqueue_style( 'jquery-ui-tabs' );

			wp_register_style( $this->token . '-admin', $this->assets_url . '/css/admin.css', array(), '1.0.0' );
			wp_enqueue_style( $this->token . '-admin' );

			wp_dequeue_style( 'jquery-ui-datepicker' );

			if ( class_exists( 'WPSEO_Metabox' ) ) {
				// Dequeue unused WordPress SEO CSS files.
				wp_dequeue_style( 'edit-page' );
				wp_dequeue_style( 'metabox-tabs' );

				$color = get_user_meta( get_current_user_id(), 'admin_color', true );
				if ( '' == $color ) $color = 'fresh';

				wp_dequeue_style( 'metabox-' . $color );
			}
		}

		if ( in_array( $pagenow, array( 'edit.php' ) ) ) {
			wp_register_style( $this->token . '-admin-posts', $this->assets_url . '/css/admin-posts.css', array(), '1.0.0' );
			wp_enqueue_style( $this->token . '-admin-posts' );
		}
	} // End enqueue_styles()

	/**
	 * add_post_column_headings function.
	 *
	 * @access public
	 * @param array $defaults
	 * @return array $new_columns
	 */
	public function add_post_column_headings ( $defaults ) {
		$defaults['woosidebars_enable'] = __( 'Custom Sidebars', 'woosidebars' );
		return $defaults;
	} // End add_post_column_headings()

	/**
	 * add_post_column_data function.
	 *
	 * @access public
	 * @param string $column_name
	 * @param int $id
	 * @return void
	 */
	public function add_post_column_data ( $column_name, $id ) {
		global $wpdb, $post;
		$meta = get_post_custom( $id );

		switch ( $column_name ) {
			case 'woosidebars_enable':
				$image = 'success-off';
				$value = '';
				$class = 'custom-sidebars-disabled';

				if ( isset( $meta['_enable_sidebar'] ) && ( $meta['_enable_sidebar'][0] != '' ) && ( $meta['_enable_sidebar'][0] == 'yes' ) ) {
					$image = 'success';
					$class = 'custom-sidebars-enabled';
				}

				$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=woosidebars-post-enable&post_id=' . $post->ID ), 'woosidebars-post-enable' );
				$value = '<span class="' . esc_attr( $class ) . '"><a href="' . esc_url( $url ) . '"><img src="' . esc_url( $this->assets_url . '/images/' . $image . '.png' ) . '" /></a></span>';

				echo $value;
			break;

			default:
			break;
		}
	} // End add_post_column_data()

	/**
	 * enable_custom_post_sidebars function.
	 *
	 * @access public
	 * @return void
	 */
	public function enable_custom_post_sidebars () {
		if( ! is_admin() ) die;
		if( ! current_user_can( 'edit_posts' ) ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'woosidebars' ) );
		if( ! check_admin_referer( 'woosidebars-post-enable' ) ) wp_die( __( 'You have taken too long. Please go back and retry.', 'woosidebars' ) );

		$post_id = isset( $_GET['post_id'] ) && (int)$_GET['post_id'] ? (int)$_GET['post_id'] : '';

		if( ! $post_id ) die;

		$post = get_post( $post_id );
		if( ! $post ) die;

		$meta = get_post_meta( $post->ID, '_enable_sidebar', true );

		if ( $meta == 'yes' ) {
			update_post_meta($post->ID, '_enable_sidebar', 'no' );
		} else {
			update_post_meta($post->ID, '_enable_sidebar', 'yes' );
		}

		$sendback = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() );
		wp_safe_redirect( esc_url( $sendback ) );
	} // End enable_custom_post_sidebars()

	/**
	 * multidimensional_search function.
	 *
	 * @access public
	 * @param string $needle
	 * @param array $haystack
	 * @return string $m
	 */
	public function multidimensional_search ( $needle, $haystack ) {
		if (empty( $needle ) || empty( $haystack ) ) {
            return false;
        }

        foreach ( $haystack as $key => $value ) {
            $exists = 0;
        	foreach ( (array)$needle as $nkey => $nvalue) {
                if ( ! empty( $value[$nvalue] ) && is_array( $value[$nvalue] ) ) {
                    return $value[$nvalue]['label'];
                }
            }
        }

        return false;
	} // End multidimensional_search()

	/**
	 * add_contextual_help function.
	 *
	 * @description Add contextual help to the current screen.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function add_contextual_help () {
		if ( get_current_screen()->id != 'edit-sidebar' ) { return; }

		get_current_screen()->add_help_tab( array(
		'id'		=> 'overview',
		'title'		=> __( 'Overview', 'woosidebars' ),
		'content'	=>
			'<p>' . __( 'All custom widget areas are listed on this screen. To add a new customised widget area, click the "Add New" button.', 'woosidebars' ) . '</p>'
		) );
		get_current_screen()->add_help_tab( array(
		'id'		=> 'wooframework-sbm',
		'title'		=> __( 'Sidebar Manager', 'woosidebars' ),
		'content'	=>
			'<p>' . __( 'WooSidebars is intended to replace the Sidebar Manager found in the WooFramework. Please ensure that all sidebars have been transferred over from the Sidebar Manager, if you choose to use WooSidebars instead.', 'woosidebars' ) . '</p>' .
			'<p>' . __( 'To transfer a sidebar from the Sidebar Manager:', 'woosidebars' ) . '</p>' .
			'<ul>' . "\n" .
			'<li>' . __( 'Create a new custom widget area in WooSidebars.', 'woosidebars' ) . '</li>' . "\n" .
			'<li>' . sprintf( __( 'Visit the %sAppearance &rarr; Widgets%s screen and drag the widgets from the old sidebar into the newly created sidebar.', 'woosidebars' ), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">', '</a>' ) . '</li>' . "\n" .
			'<li>' . __( 'Repeat this process for each of your custom sidebars, including dependencies if necessary (the WooSidebars conditions system replaces the need for dependencies).', 'woosidebars' ) . '</li>' . "\n" .
			'<li>' . __( 'Once you are certain that you widgets have been moved across for all widget areas, remove the sidebar from the Sidebar Manager (don\'t forget to transfer any dependencies over as well, if necessary).', 'woosidebars' ) . '</li>' . "\n" .
			'</ul>' . "\n"
		) );

		get_current_screen()->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', 'woosidebars' ) . '</strong></p>' .
		'<p><a href="http://support.woothemes.com/?ref=' . 'woosidebars' . '" target="_blank">' . __( 'Support HelpDesk', 'woosidebars' ) . '</a></p>' .
		'<p><a href="http://docs.woothemes.com/document/woosidebars/?ref=' . 'woosidebars' . '" target="_blank">' . __( 'WooSidebars Documentation', 'woosidebars' ) . '</a></p>'
		);
	} // End add_contextual_help()

	/**
	 * load_localisation function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		$lang_dir = trailingslashit( str_replace( 'classes', 'lang', plugin_basename( dirname(__FILE__) ) ) );
		load_plugin_textdomain( 'woosidebars', false, $lang_dir );
	} // End load_localisation()

	/**
	 * activation function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * register_plugin_version function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'woosidebars' . '-version', $this->version );
		}
	} // End register_plugin_version()
} // End Class
?>