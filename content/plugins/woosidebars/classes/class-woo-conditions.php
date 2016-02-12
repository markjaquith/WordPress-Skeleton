<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Woo Conditions Class
 *
 * Determine the conditions that apply to each screen within WordPress.
 *
 * @package WordPress
 * @subpackage WooSidebars
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * public $token
 * public $conditions
 * public $conditions_headings
 * public $conditions_reference
 * public $meta_box_settings
 * public $upper_limit
 * private $assets_url
 * private $plugin_url
 *
 * - __construct()
 * - get_conditions()
 * - determine_conditions()
 * - setup_default_conditions_reference()
 * - is_hierarchy()
 * - is_taxonomy()
 * - is_post_type_archive()
 * - is_page_template()
 * - meta_box_setup()
 * - meta_box_content()
 * - meta_box_save()
 * - show_advanced_items()
 * - ajax_toggle_advanced_items()
 * - enqueue_scripts()
 */
class Woo_Conditions {
	public $token = '';
	public $conditions = array();
	public $conditions_headings = array();
	public $conditions_reference = array();
	public $meta_box_settings = array();
	public $upper_limit;
	private $assets_url;
	private $plugin_url;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct () {
		$this->meta_box_settings['title'] = __( 'Conditions', 'woosidebars' );
		$this->upper_limit = intval( apply_filters( 'woosidebars_upper_limit', 200 ) );

		if ( is_admin() && get_post_type() == $this->token || ! get_post_type() ) {
			add_action( 'admin_menu', array( $this, 'meta_box_setup' ), 20 );
			add_action( 'save_post', array( $this, 'meta_box_save' ) );
		}

		/* Plugin URL/path settings. */
		$this->plugin_url = str_replace( '/classes', '', plugins_url( plugin_basename( dirname( __FILE__ ) ) ) );
		$this->assets_url = $this->plugin_url . '/assets';

		if ( is_admin() ) {
			add_action( 'admin_print_scripts', array( $this, 'enqueue_scripts' ), 12 );
		}

		add_action( 'get_header', array( $this, 'get_conditions' ) );

		add_action( 'wp_ajax_woosidebars-toggle-advanced-items', array( $this, 'ajax_toggle_advanced_items' ) );
	} // End __construct()

	/**
	 * get_conditions function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_conditions () {
		$this->determine_conditions();

		$this->conditions = apply_filters( 'woo_conditions', $this->conditions );

		$this->conditions = array_reverse( $this->conditions );
	} // End get_conditions()

	/**
	 * determine_conditions function.
	 *
	 * @access public
	 * @return void
	 */
	public function determine_conditions () {
		$this->is_hierarchy();
		$this->is_taxonomy();
		$this->is_post_type_archive();
		$this->is_page_template();
	} // End determine_conditions()

	/**
	 * setup_default_conditions_reference function.
	 *
	 * @description Setup the default conditions and their information, for display when selecting conditions.
	 * @access public
	 * @return void
	 */
	public function setup_default_conditions_reference () {
		$conditions = array();
		$conditions_headings = array();

		// Get an array of the different post status labels, in case we need it later.
		$post_statuses = get_post_statuses();

		// Pages
		$conditions['pages'] = array();

		$statuses_string = join( ',', array_keys( $post_statuses ) );
		$pages = get_pages( array( 'post_status' => $statuses_string ) );

		if ( count( $pages ) > 0 ) {

			$conditions_headings['pages'] = __( 'Pages', 'woosidebars' );

			foreach ( $pages as $k => $v ) {
				$token = 'post-' . $v->ID;
				$label = esc_html( $v->post_title );
				if ( 'publish' != $v->post_status ) {
					$label .= ' (' . $post_statuses[$v->post_status] . ')';
				}

				$conditions['pages'][$token] = array(
									'label' => $label,
									'description' => sprintf( __( 'The "%s" page', 'woosidebars' ), $v->post_title )
									);
			}

		}

		$args = array(
					'show_ui' => true,
					'public' => true,
					'publicly_queryable' => true,
					'_builtin' => false
					);

		$post_types = get_post_types( $args, 'object' );

		// Set certain post types that aren't allowed to have custom sidebars.
		$disallowed_types = array( 'slide' );

		// Make the array filterable.
		$disallowed_types = apply_filters( 'woosidebars_disallowed_post_types', $disallowed_types );

		if ( count( $post_types ) ) {
			foreach ( $post_types as $k => $v ) {
				if ( in_array( $k, $disallowed_types ) ) {
					unset( $post_types[$k] );
				}
			}
		}

		// Add per-post support for any post type that supports it.
		$args = array(
				'show_ui' => true,
				'public' => true,
				'publicly_queryable' => true,
				'_builtin' => true
				);

		$built_in_post_types = get_post_types( $args, 'object' );

		foreach ( $built_in_post_types as $k => $v ) {
			if ( $k == 'post' ) {
				$post_types[$k] = $v;
				break;
			}
		}

		foreach ( $post_types as $k => $v ) {
			if ( ! post_type_supports( $k, 'woosidebars' ) ) { continue; }

			$conditions_headings[$k] = $v->labels->name;

			$query_args = array( 'numberposts' => intval( $this->upper_limit ), 'post_type' => $k, 'meta_key' => '_enable_sidebar', 'meta_value' => 'yes', 'meta_compare' => '=', 'post_status' => 'any', 'suppress_filters' => 'false' );

			$posts = get_posts( $query_args );

			if ( count( $posts ) > 0 ) {
				foreach ( $posts as $i => $j ) {
					$label = $j->post_title;
					if ( 'publish' != $j->post_status ) {
						$label .= ' <strong>(' . $post_statuses[$j->post_status] . ')</strong>';
					}
					$conditions[$k]['post' . '-' . $j->ID] = array(
										'label' => $label,
										'description' => sprintf( __( 'A custom sidebar for "%s"', 'woosidebars' ), esc_attr( $j->post_title ) )
										);
				}
			}
		}

		// Page Templates
		$conditions['templates'] = array();

		$page_templates = get_page_templates();

		if ( count( $page_templates ) > 0 ) {

			$conditions_headings['templates'] = __( 'Page Templates', 'woosidebars' );

			foreach ( $page_templates as $k => $v ) {
				$token = str_replace( '.php', '', 'page-template-' . $v );
				$conditions['templates'][$token] = array(
									'label' => $k,
									'description' => sprintf( __( 'The "%s" page template', 'woosidebars' ), $k )
									);
			}
		}

		// Post Type Archives
		$conditions['post_types'] = array();

		if ( count( $post_types ) > 0 ) {

			$conditions_headings['post_types'] = __( 'Post Types', 'woosidebars' );

			foreach ( $post_types as $k => $v ) {
				$token = 'post-type-archive-' . $k;

				if ( $v->has_archive ) {
					$conditions['post_types'][$token] = array(
										'label' => sprintf( __( '"%s" Post Type Archive', 'woosidebars' ), $v->labels->name ),
										'description' => sprintf( __( 'The "%s" post type archive', 'woosidebars' ), $v->labels->name )
										);
				}
			}

			foreach ( $post_types as $k => $v ) {
				$token = 'post-type-' . $k;
				$conditions['post_types'][$token] = array(
									'label' => sprintf( __( 'Each Individual %s', 'woosidebars' ), $v->labels->singular_name ),
									'description' => sprintf( __( 'Entries in the "%s" post type', 'woosidebars' ), $v->labels->name )
									);
			}

		}

		// Taxonomies and Taxonomy Terms
		$conditions['taxonomies'] = array();

		$args = array(
					'public' => true
					);

		$taxonomies = get_taxonomies( $args, 'objects' );

		if ( count( $taxonomies ) > 0 ) {

			$conditions_headings['taxonomies'] = __( 'Taxonomy Archives', 'woosidebars' );

			foreach ( $taxonomies as $k => $v ) {
				$taxonomy = $v;

				if ( $taxonomy->public == true ) {
					$conditions['taxonomies']['archive-' . $k] = array(
										'label' => esc_html( $taxonomy->labels->name ) . ' (' . esc_html( $k ) . ')',
										'description' => sprintf( __( 'The default "%s" archives', 'woosidebars' ), strtolower( $taxonomy->labels->name ) )
										);

					// Setup each individual taxonomy's terms as well.
					$conditions_headings['taxonomy-' . $k] = $taxonomy->labels->name;
					$terms = get_terms( $k );
					if ( count( $terms ) > 0 ) {
						$conditions['taxonomy-' . $k] = array();
						foreach ( $terms as $i => $j ) {
							$conditions['taxonomy-' . $k]['term-' . $j->term_id] = array( 'label' => esc_html( $j->name ), 'description' => sprintf( __( 'The %s %s archive', 'woosidebars' ), esc_html( $j->name ), strtolower( $taxonomy->labels->name ) ) );
							if ( $k == 'category' ) {
								$conditions['taxonomy-' . $k]['in-term-' . $j->term_id] = array( 'label' => sprintf( __( 'All posts in "%s"', 'woosidebars' ), esc_html( $j->name ) ), 'description' => sprintf( __( 'All posts in the %s %s archive', 'woosidebars' ), esc_html( $j->name ), strtolower( $taxonomy->labels->name ) ) );
							}
							if ( $k == 'post_tag' ) {
								$conditions['taxonomy-' . $k]['has-term-' . $j->term_id] = array( 'label' => sprintf( __( 'All posts tagged "%s"', 'woosidebars' ), esc_html( $j->name ) ), 'description' => sprintf( __( 'All posts tagged %s', 'woosidebars' ), esc_html( $j->name ) ) );
							}
						}
					}

				}
			}
		}

		$conditions_headings['hierarchy'] = __( 'Template Hierarchy', 'woosidebars' );

		// Template Hierarchy
		$conditions['hierarchy']['page'] = array(
									'label' => __( 'Pages', 'woosidebars' ),
									'description' => __( 'Displayed on all pages that don\'t have a more specific widget area.', 'woosidebars' )
									);

		$conditions['hierarchy']['search'] = array(
									'label' => __( 'Search Results', 'woosidebars' ),
									'description' => __( 'Displayed on search results screens.', 'woosidebars' )
									);

		$conditions['hierarchy']['home'] = array(
									'label' => __( 'Default "Your Latest Posts" Screen', 'woosidebars' ),
									'description' => __( 'Displayed on the default "Your Latest Posts" screen.', 'woosidebars' )
									);

		$conditions['hierarchy']['front_page'] = array(
									'label' => __( 'Front Page', 'woosidebars' ),
									'description' => __( 'Displayed on any front page, regardless of the settings under the "Settings -> Reading" admin screen.', 'woosidebars' )
									);

		$conditions['hierarchy']['single'] = array(
									'label' => __( 'Single Entries', 'woosidebars' ),
									'description' => __( 'Displayed on single entries of any public post type other than "Pages".', 'woosidebars' )
									);

		$conditions['hierarchy']['archive'] = array(
									'label' => __( 'All Archives', 'woosidebars' ),
									'description' => __( 'Displayed on all archives (category, tag, taxonomy, post type, dated, author and search).', 'woosidebars' )
									);

		$conditions['hierarchy']['author'] = array(
									'label' => __( 'Author Archives', 'woosidebars' ),
									'description' => __( 'Displayed on all author archive screens (that don\'t have a more specific sidebar).', 'woosidebars' )
									);

		$conditions['hierarchy']['date'] = array(
									'label' => __( 'Date Archives', 'woosidebars' ),
									'description' => __( 'Displayed on all date archives.', 'woosidebars' )
									);

		$conditions['hierarchy']['404'] = array(
									'label' => __( '404 Error Screens', 'woosidebars' ),
									'description' => __( 'Displayed on all 404 error screens.', 'woosidebars' )
									);

		$this->conditions_reference = (array)apply_filters( 'woo_conditions_reference', $conditions );
		$this->conditions_headings = (array)apply_filters( 'woo_conditions_headings', $conditions_headings );
	} // End setup_default_conditions_reference()

	/**
	 * is_hierarchy function.
	 *
	 * @description Is the current view a part of the default template heirarchy?
	 * @access public
	 * @return void
	 */
	function is_hierarchy () {
		if ( is_front_page() && ! is_home() ) {
			$this->conditions[] = 'static_front_page';
		}

		if ( ! is_front_page() && is_home() ) {
			$this->conditions[] = 'inner_posts_page';
		}

		if ( is_front_page() ) {
			$this->conditions[] = 'front_page';
		}

		if ( is_home() ) {
			$this->conditions[] = 'home';
		}

		if ( is_singular() ) {
			$this->conditions[] = 'singular';
		}

		if ( is_single() ) {
			$this->conditions[] = 'single';
		}

		if ( is_single() || is_singular() ) {
			$this->conditions[] = 'post-type-' . get_post_type();
			$this->conditions[] = get_post_type();

			// In Category conditions.
			$categories = get_the_category( get_the_ID() );

			if ( is_array( $categories ) && ! is_wp_error( $categories ) && ( 0 < count( $categories ) ) ) {
				foreach ( $categories as $k => $v ) {
					$this->conditions[] = 'in-term-' . $v->term_id;
				}
			}

			// Has Tag conditions.
			$tags = get_the_tags( get_the_ID() );

			if ( is_array( $tags ) && ! is_wp_error( $tags ) && ( 0 < count( $tags ) ) ) {
				foreach ( $tags as $k => $v ) {
					$this->conditions[] = 'has-term-' . $v->term_id;
				}
			}

			// Post-specific condition.
			$this->conditions[] = 'post' . '-' . get_the_ID();
		}

		if ( is_search() ) {
			$this->conditions[] = 'search';
		}

		if ( is_home() ) {
			$this->conditions[] = 'home';
		}

		if ( is_front_page() ) {
			$this->conditions[] = 'front_page';
		}

		if ( is_archive() ) {
			$this->conditions[] = 'archive';
		}

		if ( is_author() ) {
			$this->conditions[] = 'author';
		}

		if ( is_date() ) {
			$this->conditions[] = 'date';
		}

		if ( is_404() ) {
			$this->conditions[] = '404';
		}
	} // End is_hierarchy()

	/**
	 * is_taxonomy function.
	 *
	 * @description Is the current view an archive within a specific taxonomy, that doesn't have a specific sidebar?
	 * @access public
	 * @return void
	 */
	public function is_taxonomy () {
		if ( ( is_tax() || is_archive() ) && ! is_post_type_archive() ) {
			$obj = get_queried_object();

			if ( ! is_category() && ! is_tag() ) {
				$this->conditions[] = 'taxonomies';
			}

			if ( is_object( $obj ) ) {
				$this->conditions[] = 'archive-' . $obj->taxonomy;
				$this->conditions[] = 'term-' . $obj->term_id;
			}
		}
	} // End is_taxonomy()

	/**
	 * is_post_type_archive function.
	 *
	 * @description Is the current view an archive of a post type?
	 * @access public
	 * @return void
	 */
	public function is_post_type_archive () {
		if ( is_post_type_archive() ) {

			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) ){
				$post_type = reset( $post_type );
			}

			$this->conditions[] = 'post-type-archive-' . $post_type;
		}
	} // End is_post_type_archive()

	/**
	 * is_page_template function.
	 *
	 * @description Does the current view have a specific page template attached (used on single views)?
	 * @access public
	 * @return void
	 */
	public function is_page_template () {
		if ( is_singular() ) {
			global $post;
			$template = get_post_meta( $post->ID, '_wp_page_template', true );

			if ( $template != '' && $template != 'default' ) {
				$this->conditions[] = str_replace( '.php', '', 'page-template-' . $template );
			}
		}
	} // End is_page_template()

	/**
	 * meta_box_setup function.
	 *
	 * @access public
	 * @return void
	 */
	public function meta_box_setup () {
		add_meta_box( 'woosidebars-conditions', esc_html( $this->meta_box_settings['title'] ), array( $this, 'meta_box_content' ), $this->token, 'normal', 'low' );
	} // End meta_box_setup()

	/**
	 * meta_box_content function.
	 *
	 * @access public
	 * @return void
	 */
	public function meta_box_content () {
		global $post_id;

		if ( count( $this->conditions_reference ) <= 0 ) $this->setup_default_conditions_reference();

		$selected_conditions = get_post_meta( $post_id, '_condition', false );

		if ( $selected_conditions == '' ) {
			$selected_conditions = array();
		}

		$html = '';

		$html .= '<input type="hidden" name="woo_' . $this->token . '_conditions_noonce" id="woo_' . $this->token . '_noonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

		if ( count( $this->conditions_reference ) > 0 ) {

			// Separate out the taxonomy items for use as sub-tabs of "Taxonomy Terms".
			$taxonomy_terms = array();

			foreach ( $this->conditions_reference as $k => $v ) {
				if ( substr( $k, 0, 9 ) == 'taxonomy-' ) {
					$taxonomy_terms[$k] = $v;
					unset( $this->conditions_reference[$k] );
				}
			}

			$html .= '<div id="taxonomy-category" class="categorydiv tabs woo-conditions">' . "\n";

				$html .= '<ul id="category-tabs" class="conditions-tabs alignleft">' . "\n";

				$count = 0;

				// Determine whether or not to show advanced items, based on user's preference (default: false).
				$show_advanced = $this->show_advanced_items();

				foreach ( $this->conditions_reference as $k => $v ) {
					$count++;
					$class = '';
					if ( $count == 1 ) {
						$class = 'tabs';
					} else {
						$class = 'hide-if-no-js';
					}
					if ( in_array( $k, array( 'pages' ) ) ) {
						$class .= ' basic';
					} else {
							$class .= ' advanced';
							if ( ! $show_advanced ) { $class .= ' hide'; }
					}

					if ( isset( $this->conditions_headings[$k] ) ) {
						$html .= '<li class="' . esc_attr( $class ) . '"><a href="#tab-' . esc_attr( $k ) . '">' . esc_html( $this->conditions_headings[$k] ) . '</a></li>' . "\n";
					}

					if ( $k == 'taxonomies' ) {
						$html .= '<li class="' . esc_attr( $class ) . '"><a href="#tab-taxonomy-terms">' . __( 'Taxonomy Terms', 'woosidebars' ) . '</a></li>' . "\n";
					}
				}

				$class = 'hide-if-no-js advanced';
				if ( ! $show_advanced ) { $class .= ' hide'; }

				$html .= '</ul>' . "\n";

				$html .= '<ul class="conditions-tabs"><li class="advanced-settings alignright hide-if-no-js"><a href="#">' . __( 'Advanced', 'woosidebars' ) . '</a></li></ul>' . "\n";

			foreach ( $this->conditions_reference as $k => $v ) {
				$count = 0;

				$tab = '';

				$tab .= '<div id="tab-' . esc_attr( $k ) . '" class="condition-tab">' . "\n";
				if ( isset( $this->conditions_headings[$k] ) ) {
					$tab .= '<h4>' . esc_html( $this->conditions_headings[$k] ) . '</h4>' . "\n";
				}
				$tab .= '<ul class="alignleft conditions-column">' . "\n";
					foreach ( $v as $i => $j ) {
						$count++;

						$checked = '';
						if ( in_array( $i, $selected_conditions ) ) {
							$checked = ' checked="checked"';
						}
						$tab .= '<li><label class="selectit" title="' . esc_attr( $j['description'] ) . '"><input type="checkbox" name="conditions[]" value="' . $i . '" id="checkbox-' . $i . '"' . $checked . ' /> ' . esc_html( $j['label'] ) . '</label></li>' . "\n";

						if ( $count % 10 == 0 && $count < ( count( $v ) ) ) {
							$tab .= '</ul><ul class="alignleft conditions-column">';
						}
					}

				$tab .= '</ul>' . "\n";
				// Filter the contents of the current tab.
				$tab = apply_filters( 'woo_conditions_tab_' . esc_attr( $k ), $tab );
				$html .= $tab;
				$html .= '<div class="clear"></div>';
				$html .= '</div>' . "\n";
			}

			// Taxonomy Terms Tab
			$html .= '<div id="tab-taxonomy-terms" class="condition-tab inner-tabs">' . "\n";
					$html .= '<ul class="conditions-tabs-inner hide-if-no-js">' . "\n";

				foreach ( $taxonomy_terms as $k => $v ) {
					if ( ! isset( $this->conditions_headings[$k] ) ) { unset( $taxonomy_terms[$k] ); }
				}

				$count = 0;
				foreach ( $taxonomy_terms as $k => $v ) {
					$count++;
					$class = '';
					if ( $count == 1 ) {
						$class = 'tabs';
					} else {
						$class = 'hide-if-no-js';
					}

					$html .= '<li><a href="#tab-' . $k . '" title="' . __( 'Taxonomy Token', 'woosidebars' ) . ': ' . str_replace( 'taxonomy-', '', $k ) . '">' . esc_html( $this->conditions_headings[$k] ) . '</a>';
						if ( $count != count( $taxonomy_terms ) ) {
							$html .= ' |';
						}
					$html .= '</li>' . "\n";
				}

				$html .= '</ul>' . "\n";

			foreach ( $taxonomy_terms as $k => $v ) {
				$count = 0;

				$html .= '<div id="tab-' . $k . '" class="condition-tab">' . "\n";
				$html .= '<h4>' . esc_html( $this->conditions_headings[$k] ) . '</h4>' . "\n";
				$html .= '<ul class="alignleft conditions-column">' . "\n";
					foreach ( $v as $i => $j ) {
						$count++;

						$checked = '';
						if ( in_array( $i, $selected_conditions ) ) {
							$checked = ' checked="checked"';
						}
						$html .= '<li><label class="selectit" title="' . esc_attr( $j['description'] ) . '"><input type="checkbox" name="conditions[]" value="' . $i . '" id="checkbox-' . esc_attr( $i ) . '"' . $checked . ' /> ' . esc_html( $j['label'] ) . '</label></li>' . "\n";

						if ( $count % 10 == 0 && $count < ( count( $v ) ) ) {
							$html .= '</ul><ul class="alignleft conditions-column">';
						}
					}

				$html .= '</ul>' . "\n";
				$html .= '<div class="clear"></div>';
				$html .= '</div>' . "\n";
			}
			$html .= '</div>' . "\n";
		}

		// Allow themes/plugins to act here (key, args).
		do_action( 'woo_conditions_meta_box', $k, $v );

		$html .= '<br class="clear" />' . "\n";

		echo $html;
	} // End meta_box_content()

	/**
	 * meta_box_save function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	public function meta_box_save ( $post_id ) {
		global $post, $messages;

		// Verify
		if ( ! isset( $_POST['woo_' . $this->token . '_conditions_noonce'] ) || ( get_post_type() != $this->token ) || ! wp_verify_nonce( $_POST['woo_' . $this->token . '_conditions_noonce'], plugin_basename(__FILE__) ) ) {
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

		if ( isset( $_POST['conditions'] ) && ( 0 < count( $_POST['conditions'] ) ) ) {
			delete_post_meta( $post_id, '_condition' );

			foreach ( $_POST['conditions'] as $k => $v ) {
				add_post_meta( $post_id, '_condition', $v, false );
			}
		}
	} // End meta_box_save()

	/**
	 * show_advanced_itesm function.
	 *
	 * @access private
	 * @return boolean
	 */
	private function show_advanced_items () {
		$response = false;

		$setting = get_user_setting( 'woosidebarsshowadvanced', '0' );

		if ( $setting == '1' ) { $response = true; }

		return $response;
	} // End show_advanced_items()

	/**
	 * ajax_toggle_advanced_items function.
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_toggle_advanced_items () {
		//Add nonce security to the request
		if ( ( ! isset( $_POST['woosidebars_advanced_noonce'] ) || ! isset( $_POST['new_status'] ) ) || ! wp_verify_nonce( $_POST['woosidebars_advanced_noonce'], 'woosidebars_advanced_noonce' ) ) {
			die();
		}

		$response = set_user_setting( 'woosidebarsshowadvanced', $_POST['new_status'] );

		echo $response;
		die(); // WordPress may print out a spurious zero without this can be particularly bad if using JSON
	} // End ajax_toggle_advanced_items()

	/**
	 * enqueue_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_scripts () {
		global $pagenow;
		if ( get_post_type() != $this->token ) { return; }

		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			wp_register_script( $this->token . '-admin', $this->assets_url . '/js/admin.js', array( 'jquery', 'jquery-ui-tabs' ), '1.2.1', true );

			wp_enqueue_script( $this->token . '-admin' );

			wp_dequeue_script( 'jquery-ui-datepicker' );

			$translation_strings = array();

			$ajax_vars = array( 'woosidebars_advanced_noonce' => wp_create_nonce( 'woosidebars_advanced_noonce' ) );

			$data = array_merge( $translation_strings, $ajax_vars );

			wp_localize_script( $this->token . '-admin', 'woosidebars_localized_data', $data );
		}
	} // End enqueue_scripts()
} // End Class
?>