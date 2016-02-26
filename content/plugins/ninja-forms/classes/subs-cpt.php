<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Submission CPT.
 * This class adds our submission CPT and handles displaying submissions in the wp-admin.
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Submissions
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.7
*/

class NF_Subs_CPT {

	var $form_id;

	var $screen_options;

	var $filename;

	/**
	 * Get things started
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	function __construct() {
		
		// Register our submission custom post type.
		add_action( 'init', array( $this, 'register_cpt' ), 5 );

		// Populate our field settings var
		add_action( 'current_screen', array( $this, 'setup_fields' ) );

		// Filter our hidden columns by form ID.
		add_action( 'wp', array( $this, 'filter_hidden_columns' ) );

		// Add our submenu for the submissions page.
		add_action( 'admin_menu', array( $this, 'add_submenu' ), 10 );

		// Change our submission columns.
		add_filter( 'manage_nf_sub_posts_columns', array( $this, 'change_columns' ) );

		// Make our columns sortable.
		add_filter( 'manage_edit-nf_sub_sortable_columns', array( $this, 'sortable_columns' ) );

		// Actually do the sorting
		add_filter( 'request', array( $this, 'sort_columns' ) );

		// Add the appropriate data for our custom columns.
		add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );

		// Add our submission filters.
		add_action( 'restrict_manage_posts', array( $this, 'add_filters' ) );
		add_filter( 'parse_query', array( $this, 'table_filter' ) );
		add_filter( 'posts_clauses', array( $this, 'search' ), 20 );

		add_action( 'admin_footer', array( $this, 'jquery_remove_counts' ) );

		// Filter our post counts
		add_filter( 'wp_count_posts', array( $this, 'count_posts' ), 10, 3 );

		// Filter our bulk actions
		add_filter( 'bulk_actions-edit-nf_sub', array( $this, 'remove_bulk_edit' ) );
		add_action( 'admin_footer-edit.php', array( $this, 'bulk_admin_footer' ) );

		// Filter our bulk updated/trashed messages
		add_filter( 'bulk_post_updated_messages', array( $this, 'updated_messages_filter' ), 10, 2 );

		// Filter singular updated/trashed messages
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		// Add our metabox for editing field values
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );

		// Save our metabox values
		add_action( 'save_post', array( $this, 'save_sub' ), 10, 2 );

		// Save our hidden columns by form id.
		add_action( 'wp_ajax_nf_hide_columns', array( $this, 'hide_columns' ) );

		// Load any custom screen options
		add_filter( 'screen_settings', array( $this, 'output_screen_options' ), 10, 2 );
				
		// Listen for our exports button.
		add_action( 'load-edit.php', array( $this, 'export_listen' ) );

		// Filter our submission capabilities
		add_filter( 'user_has_cap', array( $this, 'cap_filter' ), 10, 3 );

	}

	/**
	 * Register our submission CPT
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function register_cpt() {
		if ( ! isset ( $_REQUEST['form_id'] ) || empty( $_REQUEST['form_id'] ) ) {
			$not_found = __( 'Please select a form to view submissions', 'ninja-forms' );
		} else {
			$not_found = __( 'No Submissions Found', 'ninja-forms' );
		}

		$name = _x( 'Submissions', 'post type general name', 'ninja-forms' );

		if ( ! empty ( $_REQUEST['form_id'] ) ) {
			$form_title = Ninja_Forms()->form( absint( $_REQUEST['form_id'] ) )->get_setting( 'form_title' );
			$name =$name . ' - ' . $form_title;
		}

		$labels = array(
		    'name' => $name,
		    'singular_name' => _x( 'Submission', 'post type singular name', 'ninja-forms' ),
		    'add_new' => _x( 'Add New', 'nf_sub' ),
		    'add_new_item' => __( 'Add New Submission', 'ninja-forms' ),
		    'edit_item' => __( 'Edit Submission', 'ninja-forms' ),
		    'new_item' => __( 'New Submission', 'ninja-forms' ),
		    'view_item' => __( 'View Submission', 'ninja-forms' ),
		    'search_items' => __( 'Search Submissions', 'ninja-forms' ),
		    'not_found' =>  $not_found,
		    'not_found_in_trash' => __( 'No Submissions Found In The Trash', 'ninja-forms' ),
		    'parent_item_colon' => ''
	  	);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => true,
			'_builtin' => false, // It's a custom post type, not built in!
			'query_var' => true,
			'has_archive' => false,
			'show_in_menu' => false,
			'hierarchical' => false,
			'menu_events' => null,
			'rewrite' => array( 'slug' => 'nf_sub' ), // Permalinks format
			'supports' => array( 'custom-fields' ),
			'capability_type' => 'nf_sub',
			'capabilities' => array(
				'publish_posts' => 'nf_sub',
				'edit_posts' => 'nf_sub',
				'edit_others_posts' => 'nf_sub',
				'delete_posts' => 'nf_sub',
				'delete_others_posts' => 'nf_sub',
				'read_private_posts' => 'nf_sub',
				'edit_post' => 'nf_sub',
				'delete_post' => 'nf_sub',
				'read_post' => 'nf_sub',
			),
		);

		register_post_type( 'nf_sub',$args );

	}

	/**
	 * Populate our fields var with all the fields. This keeps us from needing to ping the database later.
	 * 
	 * @access public
	 * @since 2.7
	 */
	public function setup_fields() {
		global $pagenow, $typenow;

		// Bail if we aren't on the edit.php page, we aren't editing our custom post type, or we don't have a form_id set.
		if ( ( $pagenow != 'edit.php' && $pagenow != 'post.php' ) || $typenow != 'nf_sub' )
			return false;

		if ( isset ( $_REQUEST['form_id'] ) ) {
			$form_id = absint( $_REQUEST['form_id'] );
		} else if ( isset ( $_REQUEST['post'] ) ) {
			$form_id = Ninja_Forms()->sub( absint( $_REQUEST['post'] ) )->form_id;
		} else {
			$form_id = '';
		}

		$this->form_id = $form_id;

		Ninja_Forms()->form( $form_id );
	}

	/**
	 * Add our submissions submenu
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function add_submenu() {
		// Add our submissions submenu
		$sub_page = add_submenu_page( 'ninja-forms', __( 'Submissions', 'ninja-forms' ), __( 'Submissions', 'ninja-forms' ), apply_filters( 'ninja_forms_admin_submissions_capabilities', 'manage_options' ), 'edit.php?post_type=nf_sub' ); 
		// Enqueue our JS on the edit page.
		//add_action( 'load-' . $sub_page, array( $this, 'load_js' ) );
		add_action( 'admin_print_styles', array( $this, 'load_js' ) );
		add_action( 'admin_print_styles', array( $this, 'load_css' ) );
		// Remove the publish box from the submission editing page.
		remove_meta_box( 'submitdiv', 'nf_sub', 'side' );

	}

	/**
	 * Enqueue our submissions JS file.
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function load_js() {
		global $pagenow, $typenow;
		// Bail if we aren't on the edit.php page or we aren't editing our custom post type.
		if ( ( $pagenow != 'edit.php' && $pagenow != 'post.php' ) || $typenow != 'nf_sub' )
			return false;

		$form_id = isset ( $_REQUEST['form_id'] ) ? absint( $_REQUEST['form_id'] ) : '';

		if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
			$suffix = '';
			$src = 'dev';
		} else {
			$suffix = '.min';
			$src = 'min';
		}

		$suffix = '';
		$src = 'dev';

		$plugin_settings = nf_get_settings();
		$date_format = ninja_forms_date_to_datepicker( $plugin_settings['date_format'] );

		$datepicker_args = array();
		if ( !empty( $date_format ) ) {
			$datepicker_args['dateFormat'] = $date_format;
		}

		wp_enqueue_script( 'subs-cpt',
			NF_PLUGIN_URL . 'assets/js/' . $src .'/subs-cpt' . $suffix . '.js',
			array('jquery', 'jquery-ui-datepicker') );

		wp_localize_script( 'subs-cpt', 'nf_sub', array( 'form_id' => $form_id, 'datepicker_args' => apply_filters( 'ninja_forms_admin_submissions_datepicker_args', $datepicker_args ) ) );

	}

	/**
	 * Enqueue our submissions CSS file.
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function load_css() {
		global $pagenow, $typenow;

		// Bail if we aren't on the edit.php page or the post.php page.
		if ( ( $pagenow != 'edit.php' && $pagenow != 'post.php' ) || $typenow != 'nf_sub' )
			return false;
		
		wp_enqueue_style( 'nf-sub', NF_PLUGIN_URL .'assets/css/cpt.css' );
		wp_enqueue_style( 'nf-jquery-ui-freshness', NF_PLUGIN_URL .'assets/css/jquery-ui-fresh.min.css' );
	}

	/**
	 * Modify the columns of our submissions table.
	 * 
	 * @access public
	 * @since 2.7
	 * @return array $cols
	 */
	public function change_columns( $cols ) {
		// Compatibility with old field registration system. Can be removed when the new one is in place.
		global $ninja_forms_fields;
		// End Compatibility

		$cols = array(
			'cb'    => '<input type="checkbox" />',
			'id' => __( '#', 'ninja-forms' ),
		);

		// Compatibility with old field registration system. Can be removed when the new one is in place.
		if ( isset ( $_GET['form_id'] ) && $_GET['form_id'] != '' ) {
			$form_id = $_GET['form_id'];
			if ( is_object( Ninja_Forms()->form( $this->form_id ) ) && is_array ( Ninja_Forms()->form( $this->form_id )->fields ) ) {
				foreach ( Ninja_Forms()->form( $this->form_id )->fields as $field ) {
					$field_id = $field['id'];
					$field_type = $field['type'];
					if ( isset ( $ninja_forms_fields[ $field_type ] ) ) {
						$reg_field = $ninja_forms_fields[ $field_type ];
						$process_field = $reg_field['process_field'];
					} else {
						$process_field = false;
					}
					if ( isset ( $field['data']['admin_label'] ) && ! empty ( $field['data']['admin_label'] ) ) {
						$label = $field['data']['admin_label'];
					} else if ( isset ( $field['data']['label'] ) ) {
						$label = $field['data']['label'];
					} else {
						$label = '';
					}

					if ( strlen( $label ) > 140 )
						$label = substr( $label, 0, 140 );

					if ( isset ( $field['data']['label'] ) && $process_field )
						$cols[ 'form_' . $form_id . '_field_' . $field_id ] = $label;
				}
			}
		} else {
			$form_id = '';
		}
		// End Compatibility
		// Add our date column
		$cols['sub_date'] = __( 'Date', 'ninja-forms' );

		return apply_filters( 'nf_sub_table_columns', $cols, $form_id );
	}

	/**
	 * Make our columns sortable
	 * 
	 * @access public
	 * @since 2.7
	 * @return array
	 */
	public function sortable_columns() {
		// Get a list of all of our fields.
		$columns = get_column_headers( 'edit-nf_sub' );
		$tmp_array = array();
		foreach ( $columns as $slug => $c ) {
			if ( $slug != 'cb' ) {
				$tmp_array[ $slug ] = $slug;				
			}
		}
		return $tmp_array;
	}

	/**
	 * Actually sort our columns
	 * 
	 * @access public
	 * @since 2.7
	 * @return array $vars
	 */
	public function sort_columns( $vars ) {
		global $pagenow, $typenow;
		if( array_key_exists( 'orderby', $vars ) ) {
           if( strpos( $vars['orderby'], 'form_' ) !== false ) {
           		$args = explode( '_', $vars['orderby'] );
           		$field_id = $args[3];

           		if ( isset ( Ninja_Forms()->form( $this->form_id )->fields[ $field_id ]['data']['num_sort'] ) && Ninja_Forms()->form( $this->form_id )->fields[ $field_id ]['data']['num_sort'] == 1 ) {
           			$orderby = 'meta_value_num';
           		} else {
           			$orderby = 'meta_value';
           		}

                $vars['orderby'] = $orderby;
                $vars['meta_key'] = '_field_' . $field_id;
           } else if ( $vars['orderby'] == 'id' ) {
				$vars['orderby'] = 'meta_value_num';
                $vars['meta_key'] = '_seq_num';
           }
		} else if( is_admin() && $typenow == 'nf_sub' && $pagenow == 'edit.php' ) {
			$vars['orderby'] = 'meta_value_num';
            $vars['meta_key'] = '_seq_num';
            $vars['order'] = 'DESC';
		}
		return $vars;
	}

	/**
	 * Add our custom column data
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function custom_columns( $column, $sub_id ) {
		if ( isset ( $_GET['form_id'] ) ) {
			$form_id = $_GET['form_id'];
			if ( $column == 'id' ) {
				echo apply_filters( 'nf_sub_table_seq_num', Ninja_Forms()->sub( $sub_id )->get_seq_num(), $sub_id, $column );
				echo '<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>';
				if ( !isset ( $_GET['post_status'] ) || $_GET['post_status'] == 'all' ) {
					echo '<div class="row-actions">';
					do_action( 'nf_sub_table_before_row_actions', $sub_id, $column );
					echo '<span class="edit"><a href="post.php?post=' . $sub_id . '&action=edit&ref=' . urlencode( esc_url(  add_query_arg( array() ) ) ) . '" title="' . __( 'Edit this item', 'ninja-forms' ) . '">' . __( 'Edit', 'ninja-forms' ) . '</a> | </span> 
						<span class="edit"><a href="' . esc_url( add_query_arg( array( 'export_single' => $sub_id ) ) ) . '" title="' . __( 'Export this item', 'ninja-forms' ) . '">' . __( 'Export', 'ninja-forms' ) . '</a> | </span>';
					$row_actions = apply_filters( 'nf_sub_table_row_actions', array(), $sub_id, $form_id );
					if ( ! empty( $row_actions ) ) {
						echo implode(" | ", $row_actions);
						echo '| ';
					}
					echo '<span class="trash"><a class="submitdelete" title="' . __( 'Move this item to the Trash', 'ninja-forms' ) . '" href="' . get_delete_post_link( $sub_id ) . '">' . __( 'Trash', 'ninja-forms' ) . '</a> </span>';
					do_action( 'nf_sub_table_after_row_actions', $sub_id, $column );
					echo '</div>';
				} else {
					echo '<div class="row-actions">';
					do_action( 'nf_sub_table_before_row_actions_trash', $sub_id, $column );
					echo '<span class="untrash"><a title="' . esc_attr( __( 'Restore this item from the Trash' ) ) . '" href="' . wp_nonce_url( sprintf( get_edit_post_link( $sub_id ) . '&amp;action=untrash', $sub_id ) , 'untrash-post_' . $sub_id ) . '">' . __( 'Restore' ) . '</a> | </span> 
					<span class="delete"><a class="submitdelete" title="' . esc_attr( __( 'Delete this item permanently' ) ) . '" href="' . get_delete_post_link( $sub_id, '', true ) . '">' . __( 'Delete Permanently' ) . '</a></span>';
					do_action( 'nf_sub_table_after_row_actions_trash', $sub_id, $column );
					echo '</div>';
				}
			} else if ( $column == 'sub_date' ) {
				$post = get_post( $sub_id );
				if ( '0000-00-00 00:00:00' == $post->post_date ) {
					$t_time = $h_time = __( 'Unpublished' );
					$time_diff = 0;
				} else {
					$t_time = get_the_time( 'Y/m/d g:i:s A' );
					$m_time = $post->post_date;
					$time = get_post_time( 'G', true, $post );

					$time_diff = time() - $time;

					if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS )
						$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
					else
						$h_time = mysql2date( 'Y/m/d', $m_time );
				}

				$t_time = apply_filters( 'nf_sub_title_time', $t_time );
				$h_time = apply_filters( 'nf_sub_human_time', $h_time );
				
				/** This filter is documented in wp-admin/includes/class-wp-posts-list-table.php */
				echo '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';

				echo '<br />';
				echo apply_filters( 'nf_sub_table_status', __( 'Submitted', 'ninja-forms' ), $sub_id );

			} else if ( strpos( $column, '_field_' ) !== false ) {
				global $ninja_forms_fields;

				$field_id = str_replace( 'form_' . $form_id . '_field_', '', $column );
				//if ( apply_filters( 'nf_add_sub_value', Ninja_Forms()->field( $field_id )->type->add_to_sub, $field_id ) ) {
					$field = Ninja_Forms()->form( $form_id )->fields[ $field_id ];
					$field_type = $field['type'];
					if ( isset ( $ninja_forms_fields[ $field_type ] ) ) {
						$reg_field = $ninja_forms_fields[ $field_type ];
					} else {
						$reg_field = array();
					}

					if ( isset ( $reg_field['sub_table_value'] ) ) {
						$edit_value_function = $reg_field['sub_table_value'];
					} else {
						$edit_value_function = 'nf_field_text_sub_table_value';
					}

					$user_value = Ninja_Forms()->sub( $sub_id )->get_field( $field_id );

					$args['field_id'] = $field_id;
					$args['user_value'] = ninja_forms_esc_html_deep( $user_value );
					$args['field'] = $field;

					call_user_func_array( $edit_value_function, $args );
				//}
			}
		}
	}

	/**
	 * Add our submission filters
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function add_filters() {
		global $typenow;

		// Bail if we aren't in our submission custom post type.
		if ( $typenow != 'nf_sub' )
			return false;

		// Add our Form selection dropdown.
		// Get our list of forms
		$forms = Ninja_Forms()->forms()->get_all();

		$form_id = isset( $_GET['form_id'] ) ? $_GET['form_id'] : '';

		$begin_date = isset ( $_GET['begin_date'] ) ? $_GET['begin_date'] : '';
		$end_date = isset ( $_GET['end_date'] ) ? $_GET['end_date'] : '';

		// Add begin date and end date filter fields.
		$html = '<div style="float:left;">';
		$html .= '<span style="float:left;" class="spinner"></span>';
		$html .= '<select name="form_id" id="form_id" class="nf-form-jump">';
		$html .= '<option value="">- ' . __( 'Select a form', 'ninja-forms' ) . '</option>';
		if ( is_array( $forms ) ) {
			foreach ( $forms as $f_id ) {
				$form_title = Ninja_Forms()->form( $f_id )->get_setting( 'form_title' );
				$html .= '<option value="' . $f_id . '" ' . selected( $form_id, $f_id, false ) . '>' . $form_title . '</option>';
			}
		}
		$html .= '</select>';

		$html .= '<input name="begin_date" type="text" class="datepicker" placeholder="' . __( 'Begin Date', 'ninja-forms' ) . '" value="' . $begin_date . '" /> ';
		$html .= '<input name="end_date" type="text" class="datepicker" placeholder="' . __( 'End Date', 'ninja-forms' ) . '" value="' . $end_date . '" />';
		$html .= '</div>';

		echo $html;

	}

	/**
	 * Filter our submission list by form_id
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function table_filter( $query ) {
		global $pagenow;

		if( $pagenow == 'edit.php' && is_admin() && ( isset ( $query->query['post_type'] ) && $query->query['post_type'] == 'nf_sub' ) && is_main_query() ) {

		    $qv = &$query->query_vars;

		    if( !empty( $_GET['form_id'] ) ) {
		    	$form_id = $_GET['form_id'];
		    } else {
		    	$form_id = 0;
		    }

		    $plugin_settings = nf_get_settings();
		    $date_format = $plugin_settings['date_format'];

		    if ( !empty ( $_GET['begin_date'] ) ) {
		    	$begin_date = nf_get_begin_date( $_GET['begin_date'] )->format("Y-m-d G:i:s");
		    } else {
		    	$begin_date = '';
		    }

			if ( !empty ( $_GET['end_date'] ) ) {
		    	$end_date = nf_get_end_date( $_GET['end_date'] )->format("Y-m-d G:i:s");
		    } else {
		    	$end_date = '';
		    }

		    if ( $begin_date > $end_date ) {
		    	 $begin_date = new DateTime( $begin_date );
		    	 $end_date = new DateTime( $end_date );
			     $end_date_temp = $begin_date;
			     $begin_date_temp = $end_date;
			     $begin_date = $begin_date_temp;
			     $end_date = $end_date_temp;
			     $_GET['begin_date'] = $begin_date->format('m/d/Y');
			     $_GET['end_date'] = $end_date->format('m/d/Y');
			     $begin_date = $begin_date->format("Y-m-d G:i:s");
			     $end_date = $end_date->format("Y-m-d G:i:s");
		    }
		    
		    if ( ! isset ( $qv['date_query'] ) ) {
			    $qv['date_query'] = array(
			    	'after' => $begin_date,
			    	'before' => $end_date,
			    );		    	
		    }

		    if ( ! isset ( $qv['meta_query'] ) ) {
			     $qv['meta_query'] = array(
			    	array(
			    		'key' => '_form_id',
			    		'value' => $form_id,
			    		'compare' => '=',
			    	),
			    );
		    }

		    $qv = apply_filters( 'nf_subs_table_qv', $qv, $form_id );
		}
	}

	/**
	 * Filter our search
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function search( $pieces ) {
		global $typenow;
	    // filter to select search query
	    if ( is_search() && is_admin() && $typenow == 'nf_sub' && isset ( $_GET['s'] ) ) { 
	        global $wpdb;

	        $keywords = explode(' ', get_query_var('s'));
	        $query = "";

	        foreach ($keywords as $word) {

	             $query .= " (mypm1.meta_value  LIKE '%{$word}%') OR ";
	         }

	        if (!empty($query)) {
	            // add to where clause
	            $pieces['where'] = str_replace("((({$wpdb->posts}.post_title LIKE '%", "( {$query} (({$wpdb->posts}.post_title LIKE '%", $pieces['where']);

	            $pieces['join'] = $pieces['join'] . " INNER JOIN {$wpdb->postmeta} AS mypm1 ON ({$wpdb->posts}.ID = mypm1.post_id)";
	        	
	        }
	    }
	    return ($pieces);
	}

	/**
	 * Filter our bulk updated/trashed messages so that it uses "submission" rather than "post"
	 * 
	 * @access public
	 * @since 2.7
	 * @return array $bulk_messages
	 */
	public function updated_messages_filter( $bulk_messages, $bulk_counts ) {
	    $bulk_messages['nf_sub'] = array(
	        'updated'   => _n( '%s submission updated.', '%s submissions updated.', $bulk_counts['updated'], 'ninja-forms' ),
	        'locked'    => _n( '%s submission not updated, somebody is editing it.', '%s submissions not updated, somebody is editing them.', $bulk_counts['locked'], 'ninja-forms' ),
	        'deleted'   => _n( '%s submission permanently deleted.', '%s submissions permanently deleted.', $bulk_counts['deleted'], 'ninja-forms' ),
	        'trashed'   => _n( '%s submission moved to the Trash.', '%s submissions moved to the Trash.', $bulk_counts['trashed'], 'ninja-forms' ),
	        'untrashed' => _n( '%s submission restored from the Trash.', '%s submissions restored from the Trash.', $bulk_counts['untrashed'], 'ninja-forms' ),
	    );

	    return $bulk_messages;
	}

	/**
	 * Filter our updated/trashed post messages
	 * 
	 * @access public
	 * @since 2.7
	 * @return array $messages
	 */
	function post_updated_messages( $messages ) {

		global $post, $post_ID;
		$post_type = 'nf_sub';

		$obj = get_post_type_object( $post_type );
		$singular = $obj->labels->singular_name;

		$messages[$post_type] = array(
			0 => 	'', // Unused. Messages start at index 1.
	 		1 => 	sprintf( __( '%s updated.', 'ninja-forms' ), $singular ),
	 		2 => 	__( 'Custom field updated.' ),
	 		3 => 	__( 'Custom field deleted.' ),
	 		4 => 	sprintf( __( '%s updated.', 'ninja-forms' ), $singular ),
			/* translators: %s: date and time of the revision */
	 		5 => 	isset($_GET['revision']) ? sprintf( __( '%1$s restored to revision from %2$s.' ), $singular, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	 		6 => 	sprintf( __( '%s published.', 'ninja-forms' ), $singular ),
	 		7 => 	sprintf( __( '%s saved.', 'ninja-forms' ), $singular ),
	 		8 => 	sprintf( __( '%1$s submitted. <a href="%2$s" target="_blank">Preview %3$s</a>', 'ninja-forms' ), $singular, esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ), $singular ),
	 		9 => 	sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>. <a href="%3$s" target="_blank">Preview %4$s</a>', 'ninja-forms' ), $singular, date_i18n( get_option( 'data_format' ) . ' ' . get_option( 'time_format' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID ) ), $singular ),
			10 => 	sprintf( __( '%1$s draft updated. <a href="%2$s" target="_blank">Preview %3$s</a>', 'ninja-forms' ), $singular, esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ), $singular ),
		);

		return $messages;
	}

	/**
	 * Remove the 'edit' bulk action
	 * 
	 * @access public
	 * @since 2.7
	 * @return array $actions
	 */
	public function remove_bulk_edit( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}

	/**
	 * Add our "export" bulk action
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function bulk_admin_footer() {
		global $post_type;
 		
		if ( ! is_admin() )
			return false;

		if( $post_type == 'nf_sub' && isset ( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] == 'all' ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('<option>').val('export').text('<?php _e('Export')?>').appendTo("select[name='action']");
					jQuery('<option>').val('export').text('<?php _e('Export')?>').appendTo("select[name='action2']");
					<?php
					if ( ( isset ( $_POST['action'] ) && $_POST['action'] == 'export' ) || ( isset ( $_POST['action2'] ) && $_POST['action2'] == 'export' ) ) {
						?>
						setInterval(function(){
							jQuery( "select[name='action'" ).val( '-1' );
							jQuery( "select[name='action2'" ).val( '-1' );
							jQuery( '#posts-filter' ).submit();
						},5000);
						<?php
					}

					if ( isset ( $_REQUEST['form_id'] ) && ! empty ( $_REQUEST['form_id'] ) ) {
						$redirect = urlencode( remove_query_arg( array( 'download_all', 'download_file' ) ) );
						$url = admin_url( 'admin.php?page=nf-processing&action=download_all_subs&form_id=' . absint( $_REQUEST['form_id'] ) . '&redirect=' . $redirect );
						$url = esc_url( $url );
						?>
						var button = '<a href="<?php echo $url; ?>" class="button-secondary nf-download-all"><?php echo __( 'Download All Submissions', 'ninja-forms' ); ?></a>';
						jQuery( '#doaction2' ).after( button );
						<?php
					}
					
					if ( isset ( $_REQUEST['download_all'] ) && $_REQUEST['download_all'] != '' ) {
						$redirect = esc_url_raw( add_query_arg( array( 'download_file' => esc_html( $_REQUEST['download_all'] ) ) ) );
						$redirect = remove_query_arg( array( 'download_all' ), $redirect );
						?>
						document.location.href = "<?php echo $redirect; ?>";
						<?php
					}

					?>
				});
			</script>
			<?php
		}
	}

	/**
	 * jQuery that hides some of our post-related page items.
	 * Also adds the active class to All and Trash links, and changes those
	 * links to match the current filter.
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function jquery_remove_counts() {
		global $typenow, $pagenow;
		if ( $typenow == 'nf_sub' && $pagenow == 'edit.php' ) {
			// Remove our transient
			delete_transient( 'nf_sub_edit_ref' );

			if ( ! isset ( $_GET['post_status'] ) || $_GET['post_status'] == 'all' ) {
				$active = 'all';
			} else if ( $_GET['post_status'] == 'trash' ) {
				$active = 'trash';
			}

			$all_url = esc_url_raw( add_query_arg( array( 'post_status' => 'all' ) ) );
			$all_url = remove_query_arg( 's', $all_url );
			$trash_url = esc_url_raw( add_query_arg( array( 'post_status' => 'trash' ) ) );
			$trash_url = remove_query_arg( 's', $trash_url );
			if ( isset ( $_GET['form_id'] ) ) {
				$trashed_sub_count = nf_get_sub_count( $_GET['form_id'], 'trash' );	
			} else {
				$trashed_sub_count = 0;
			}

			?>
			<script type="text/javascript">
				jQuery(function(){
					jQuery( "li.all" ).find( "a" ).attr( "href", "<?php echo $all_url; ?>" );
					jQuery( "li.<?php echo $active; ?>" ).addClass( "current" );
					jQuery( "li.<?php echo $active; ?>" ).find( "a" ).addClass( "current" );
					jQuery( "li.trash" ).find( "a" ).attr( "href", "<?php echo $trash_url; ?>" );
					jQuery( ".view-switch" ).remove();
					<?php
					if ( $trashed_sub_count == 0 ) {
						?>
						var text = jQuery( "li.all" ).prop( "innerHTML" );
						text = text.replace( " |", "" );
						jQuery( "li.all" ).prop( "innerHTML", text );
						<?php
					}
					?>
				});
			</script>

			<style>
				.add-new-h2 {
					display:none;
				}
				li.publish {
					display:none;
				}
				select[name=m] {
					display:none;
				}
			</style>
			<?php			
		} else if ( $typenow == 'nf_sub' && $pagenow == 'post.php' ) {
			if ( isset ( $_REQUEST['ref'] ) ) {
				$back_url = esc_url_raw( $_REQUEST['ref'] );
			} else {
				$back_url = get_transient( 'nf_sub_edit_ref' );
			}
			
			if ( $back_url ) {
				$back_url = urldecode( $back_url );
			} else {
				$back_url = '';
			}
			?>
			<script type="text/javascript">
				jQuery(function(){
					var html = '<a href="<?php echo $back_url; ?>" class="back button-secondary"><?php _e( 'Back to list', 'ninja-forms' ); ?></a>';
					jQuery( 'div.wrap' ).children( 'h2:first' ).append( html );
					jQuery( 'li#toplevel_page_ninja-forms' ).children( 'a' ).removeClass( 'wp-not-current-submenu' );
					jQuery( 'li#toplevel_page_ninja-forms' ).removeClass( 'wp-not-current-submenu' );
					jQuery( 'li#toplevel_page_ninja-forms' ).addClass( 'wp-menu-open wp-has-current-submenu' );
					jQuery( 'li#toplevel_page_ninja-forms' ).children( 'a' ).addClass( 'wp-menu-open wp-has-current-submenu' );

				});
			</script>
			<style>
				.add-new-h2 {
					display:none;
				}
			</style>	

			<?php
		}
	}

	/**
	 * Filter our post counts for the submission listing page
	 * 
	 * @access public
	 * @since 2.7
	 * @return int $count
	 */
	public function count_posts( $count, $post_type, $perm ) {
		
		// Bail if we aren't working with our custom post type.
		if ( $post_type != 'nf_sub' )
			return $count;

		if ( isset ( $_GET['form_id'] ) ) {
			$sub_count = nf_get_sub_count( $_GET['form_id'] );
			$trashed_sub_count = nf_get_sub_count( $_GET['form_id'], 'trash' );
			$count->publish = $sub_count;
			$count->trash = $trashed_sub_count;
		} else {
			$count->publish = 0;
			$count->trash = 0;
		}

		return $count;
	}

	/**
	 * Add our field editing metabox to the CPT editing page.
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function add_metaboxes() {
		// Remove the 'custom fields' metabox from our CPT edit page
		remove_meta_box( 'postcustom', 'nf_sub', 'normal' );
		// Remove the 'slug' metabox from our CPT edit page.
		remove_meta_box( 'slugdiv', 'nf_sub', 'normal' );
		// Add our field editing metabox.
		add_meta_box( 'nf_fields', __( 'User Submitted Values', 'ninja-forms' ), array( $this, 'edit_sub_metabox' ), 'nf_sub', 'normal', 'default');
		// Add our save field values metabox
		add_meta_box( 'nf_fields_save', __( 'Submission Stats', 'ninja-forms' ), array( $this, 'save_sub_metabox' ), 'nf_sub', 'side', 'default');

	}

	/**
	 * Output our field editing metabox to the CPT editing page.
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function edit_sub_metabox( $post ) {
		global $ninja_forms_fields;
		// Get all the post meta
		$form_id = Ninja_Forms()->sub( $post->ID )->form_id;
		$fields = Ninja_Forms()->form( $this->form_id )->fields;
		
		if ( isset ( $_REQUEST['ref'] ) ) {
			$ref = esc_url_raw( $_REQUEST['ref'] );
		} else if ( get_transient( 'nf_sub_edit_ref' ) ) {
			$ref = get_transient( 'nf_sub_edit_ref' );
		} else {
			$ref = '';
		}
		?>
		<input type="hidden" name="ref" value="<?php echo $ref; ?>" />
		<div id="postcustomstuff">
			<table id="list-table">
				<thead>
					<tr>
						<th class="left"><?php _e( 'Field', 'ninja-forms' ); ?></th>
						<th><?php _e( 'Value', 'ninja-forms' ); ?></th>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php
					// Loop through our post meta and keep our field values
					foreach ( $fields as $field_id => $field ) {
						$user_value = Ninja_Forms()->sub( $post->ID )->get_field( $field_id );
						$field_type = $field['type'];

						if ( isset ( $field['data']['admin_label'] ) && $field['data']['admin_label'] != '' ) {
							$label = $field['data']['admin_label'];
						} else if ( isset ( $field['data']['label'] ) ) {
							$label = $field['data']['label'];
						} else {
							$label = '';
						}

						if ( isset ( $ninja_forms_fields[ $field_type ] ) ) {
							$reg_field = $ninja_forms_fields[ $field_type ];
							$process_field = $reg_field['process_field'];
						} else {
							$process_field = false;
						}

						if ( isset ( Ninja_Forms()->form( $this->form_id )->fields[ $field_id ] ) && $process_field ) {
							?>
							<tr>
								<td class="left"><?php echo $label; ?></td>
								<td>
									<div class="nf-sub-edit-value type-<?php echo $field_type; ?>">
									<?php
										if ( isset ( $reg_field['edit_sub_value'] ) ) {
											$edit_value_function = $reg_field['edit_sub_value'];
										} else {
											$edit_value_function = 'nf_field_text_edit_sub_value';
										}
										$args['field_id'] = $field_id;
										$args['user_value'] = nf_wp_kses_post_deep( $user_value );
										$args['field'] = $field;
										$args['sub_id'] = $post->ID;

										call_user_func_array( $edit_value_function, $args );

									?>
									</div>
								</td>
							</tr>
							<?php
						}

					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Output our field editing metabox to the CPT editing page.
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function save_sub_metabox( $post ) {
		$date_submitted = apply_filters( 'nf_edit_sub_date_submitted', date( 'M j, Y @ h:i', strtotime( $post->post_date ) ), $post->ID );
		$date_modified = apply_filters( 'nf_edit_sub_date_modified', date( 'M j, Y @ h:i', strtotime( $post->post_modified ) ), $post->ID );

		if ( $post->post_author != 0 ) {
			$user_data = get_userdata( $post->post_author );
			
			$first_name = $user_data->first_name;
			$last_name = $user_data->last_name;

			if ( $first_name != '' && $last_name != '' ) {
				$name = $first_name . ' ' . $last_name;
			} else if ( $user_data->display_name != '' ) {
				$name = $user_data->display_name;
			} else {
				$name = $user_data->user_login;
			}

			$name = apply_filters( 'nf_edit_sub_username', $name, $post->post_author );
		}

		$form_id = Ninja_Forms()->sub( $post->ID )->form_id;
		$form_title = Ninja_Forms()->form( $form_id )->get_setting( 'form_title' );
		?>
		<input type="hidden" name="nf_edit_sub" value="1">
		<div class="submitbox" id="submitpost">
			<div id="minor-publishing">
				<div id="misc-publishing-actions">
					<div class="misc-pub-section misc-pub-post-status">
						<label for="post_status"><?php _e( '#', 'ninja-forms' ); ?>:</label>
						<span id="sub-seq-num-display"><?php echo Ninja_Forms()->sub( $post->ID )->get_seq_num(); ?></span>
					</div>
					<div class="misc-pub-section misc-pub-post-status">
						<label for="post_status"><?php _e( 'Status', 'ninja-forms' ); ?>:</label>
						<span id="sub-status-display"><?php echo apply_filters( 'nf_sub_edit_status', __( 'Submitted', 'ninja-forms' ), $post->ID ); ?></span>
						<?php do_action( 'nf_sub_edit_after_status', $post ); ?>
					</div>
					<div class="misc-pub-section misc-pub-post-status">
						<label for="post_status"><?php _e( 'Form', 'ninja-forms' ); ?>:</label>
						<span id="sub-form-title-display"><?php echo $form_title; ?></span>
					</div>
					<div class="misc-pub-section curtime misc-pub-curtime">
						<span id="timestamp">
							<?php _e( 'Submitted on', 'ninja-forms' ); ?>: <b><?php echo $date_submitted; ?></b>
						</span>
						<?php do_action( 'nf_sub_edit_date_submitted', $post ); ?>
					</div>
					<div class="misc-pub-section curtime misc-pub-curtime">
						<span id="timestamp">
							<?php _e( 'Modified on', 'ninja-forms', $post ); ?>: <b><?php echo $date_modified; ?></b>
						</span>
						<?php do_action( 'nf_sub_edit_date_modified', $post ); ?>
					</div>
					<?php
					if ( $post->post_author != 0 ) {
						?>
						<div class="misc-pub-section misc-pub-visibility" id="visibility">
							<?php _e( 'Submitted By', 'ninja-forms' ); ?>: <span id="post-visibility-display"><?php echo $name; ?></span>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<div id="major-publishing-actions">
				<div id="delete-action">

				<!-- <a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>">Move to Trash</a>--></div> 

				<div id="publishing-action">
				<span class="spinner"></span>
						<input name="original_publish" type="hidden" id="original_publish" value="<?php _e( 'Update', 'ninja-forms' ); ?>">
						<input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php _e( 'Update', 'ninja-forms' ); ?>">
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save our submission user values
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function save_sub( $sub_id, $post ) {
		global $pagenow;

		if ( ! isset ( $_POST['nf_edit_sub'] ) || $_POST['nf_edit_sub'] != 1 )
			return $sub_id;

		// verify if this is an auto save routine.
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		  return $sub_id;

		if ( $pagenow != 'post.php' )
			return $sub_id;

		if ( $post->post_type != 'nf_sub' )
			return $sub_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $sub_id ) )
	    	return $sub_id;

	    foreach ( $_POST['fields'] as $field_id => $user_value ) {
	    	$user_value = nf_wp_kses_post_deep( apply_filters( 'nf_edit_sub_user_value', $user_value, $field_id, $sub_id ) );
	    	Ninja_Forms()->sub( $sub_id )->update_field( $field_id, $user_value );
	    }

	    set_transient( 'nf_sub_edit_ref', esc_url_raw( $_REQUEST['ref'] ) );
	}

	/**
	 * Filter our hidden columns so that they are handled on a per-form basis.
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function filter_hidden_columns() {
		global $pagenow;
		// Bail if we aren't on the edit.php page, we aren't editing our custom post type, or we don't have a form_id set.
		if ( $pagenow != 'edit.php' || ! isset ( $_REQUEST['post_type'] ) || $_REQUEST['post_type'] != 'nf_sub' || ! isset ( $_REQUEST['form_id'] ) )
			return false;

		// Grab our current user.
		$user = wp_get_current_user();
		// Grab our form id.
		$form_id = absint( $_REQUEST['form_id'] );
		// Get the columns that should be hidden for this form ID.
		$hidden_columns = get_user_option( 'manageedit-nf_subcolumnshidden-form-' . $form_id );
		
		if ( $hidden_columns === false ) {
			// If we don't have custom hidden columns set up for this form, then only show the first five columns.
			// Get our column headers
			$columns = get_column_headers( 'edit-nf_sub' );
			$hidden_columns = array();
			$x = 0;
			foreach ( $columns as $slug => $name ) {
				if ( $x > 5 ) {
					if ( $slug != 'sub_date' )
						$hidden_columns[] = $slug;
				}
				$x++;
			}
		}
		update_user_option( $user->ID, 'manageedit-nf_subcolumnshidden', $hidden_columns, true );
	}

	/**
	 * Save our hidden columns per form id.
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function hide_columns() {
		// Grab our current user.
		$user = wp_get_current_user();
		// Grab our form id.
		$form_id = absint( $_REQUEST['form_id'] );
		$hidden = isset( $_POST['hidden'] ) ? explode( ',', esc_html( $_POST['hidden'] ) ) : array();
		$hidden = array_filter( $hidden );
		update_user_option( $user->ID, 'manageedit-nf_subcolumnshidden-form-' . $form_id, $hidden, true );
		die();
	}

	/**
	 * Add custom screen options
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function output_screen_options( $status, $args ) {
		if ( $args->base == 'edit' && $args->post_type == 'nf_sub' ) {
			$status .= '<span id="nf-subs-screen-options">' . $this->screen_options . '</span>';
		}
		return $status;
	}

	/**
	 * Listen for exporting subs
	 * 
	 * @access public
	 * @since 2.7.3
	 * @return void
	 */
	public function export_listen() {
		// Bail if we aren't in the admin
		if ( ! is_admin() )
			return false;

		if ( ! isset ( $_REQUEST['form_id'] ) || empty ( $_REQUEST['form_id'] ) )
			return false;

		if ( isset ( $_REQUEST['export_single'] ) && ! empty( $_REQUEST['export_single'] ) )
			Ninja_Forms()->sub( esc_html( $_REQUEST['export_single'] ) )->export();

		if ( ( isset ( $_REQUEST['action'] ) && $_REQUEST['action'] == 'export' ) || ( isset ( $_REQUEST['action2'] ) && $_REQUEST['action2'] == 'export' ) ) {
			Ninja_Forms()->subs()->export( ninja_forms_esc_html_deep( $_REQUEST['post'] ) );
		}

		if ( isset ( $_REQUEST['download_file'] ) && ! empty( $_REQUEST['download_file'] ) ) {
			// Open our download all file
			$filename = esc_html( $_REQUEST['download_file'] );
			
			$upload_dir = wp_upload_dir();

			$file_path = trailingslashit( $upload_dir['path'] ) . $filename . '.csv';

			if ( file_exists( $file_path ) ) {
				$myfile = file_get_contents ( $file_path );
			} else {
				$redirect = esc_url_raw( remove_query_arg( array( 'download_file', 'download_all' ) ) );
				wp_redirect( $redirect );
				die();
			}
			
			unlink( $file_path );

			$form_name = Ninja_Forms()->form( absint( $_REQUEST['form_id'] ) )->get_setting( 'form_title' );
			$form_name = sanitize_title( $form_name );

			$today = date( 'Y-m-d', current_time( 'timestamp' ) );

			$filename = apply_filters( 'nf_download_all_filename', $form_name . '-all-subs-' . $today );

			header( 'Content-type: application/csv');
			header( 'Content-Disposition: attachment; filename="'.$filename .'.csv"' );
			header( 'Pragma: no-cache');
			header( 'Expires: 0' );

			echo $myfile;

			die();
		}
	}
	
	/**
	 * Filter user capabilities
	 * 
	 * @access public
	 * @since 2.7.7
	 * @return void
	 */
	public function cap_filter( $allcaps, $cap, $args ) {

		$sub_cap = apply_filters( 'ninja_forms_admin_submissions_capabilities', 'manage_options' );

		if ( ! empty( $allcaps[ $sub_cap ] ) ) {
			$allcaps['nf_sub'] = true;
		}

		return $allcaps;
	}

}
