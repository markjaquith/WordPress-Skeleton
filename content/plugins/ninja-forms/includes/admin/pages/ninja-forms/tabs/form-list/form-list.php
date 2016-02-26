<?php if ( ! defined( 'ABSPATH' ) ) exit;


function ninja_forms_register_form_export(){
	if( isset( $_REQUEST['export_form'] ) AND $_REQUEST['export_form'] == 1 ){
		$form_id = absint( $_REQUEST['form_id'] );
		ninja_forms_export_form( $form_id );
	}
}

add_action( 'admin_init', 'ninja_forms_register_form_export' );

function ninja_forms_register_form_duplicate(){
	if ( isset ( $_REQUEST['duplicate_form'] ) AND $_REQUEST['duplicate_form'] == 1 ) {
		$form_id = absint( $_REQUEST['form_id'] );
		$form_row = ninja_forms_serialize_form( $form_id );
		ninja_forms_import_form( $form_row );
		$url = esc_url_raw( remove_query_arg( array( 'duplicate_form', 'form_id' ) ) );
		wp_redirect( $url );
	}
}

add_action( 'admin_init', 'ninja_forms_register_form_duplicate' );

function ninja_forms_register_tab_form_list(){
	$new_link = esc_url( add_query_arg( array('form_id' => 'new', 'tab' => 'form_settings') ) );
	$args = array(
		'name' => __( 'All Forms', 'ninja-forms' ),
		'page' => 'ninja-forms',
		'display_function' => 'ninja_forms_tab_form_list',
		'save_function' => 'ninja_forms_save_form_list',
		'show_save' => false,
		'active_class' => 'form-list-active',
		'inactive_class' => 'form-list-inactive',
		'show_tab_links' => false,
		'show_this_tab_link' => false,
		// 'title' => '<h2>Forms <a href="'.$new_link.'" class="add-new-h2">'.__( 'Add New Form', 'ninja-forms' ).'</a></h2>',
	);
	ninja_forms_register_tab('form_list', $args);
}

add_action('admin_init', 'ninja_forms_register_tab_form_list');

function ninja_forms_tab_form_list(){

	do_action( 'nf_admin_before_form_list' );

	$all_forms = Ninja_Forms()->forms()->get_all();

	$form_count = count($all_forms);

	if( isset( $_REQUEST['limit'] ) ){
		$saved_limit = absint( $_REQUEST['limit'] );
		$limit = absint( $_REQUEST['limit'] );
	}else{
		$saved_limit = 20;
		$limit = 20;
	}

	if( $form_count < $limit ){
		$limit = $form_count;
	}

	if( isset( $_REQUEST['paged']) AND !empty( $_REQUEST['paged'] ) ){
		$current_page = absint( $_REQUEST['paged'] );
	}else{
		$current_page = 1;
	}

	if( $form_count > $limit ){
		$page_count = ceil( $form_count / $limit );
	}else{
		$page_count = 1;
	}

	if( $current_page > 1 ){
		$start = ( ( $current_page - 1 ) * $limit );
		if( $form_count < $limit ){
			$end = $form_count;
		}else{
			$end = $current_page * $limit;
			// $end = $end - 1;
		}

		if( $end > $form_count ){
			$end = $form_count;
		}
	}else{
		$start = 0;
		$end = $limit;
	}

	?>
	<ul class="subsubsub">
		<li class="all"><a href="" class="current"><?php _e( 'All', 'ninja-forms' ); ?> <span class="count">(<?php echo $form_count;?>)</span></a>
	</ul>
	<div id="" class="tablenav top">
		<div class="alignleft actions">
			<select id="" class="" name="bulk_action">
				<option value=""><?php _e( 'Bulk Actions', 'ninja-forms' );?></option>
				<option value="delete"><?php _e( 'Delete', 'ninja-forms' );?></option>
			</select>
			<input type="submit" name="submit" value="<?php _e( 'Apply', 'ninja-forms' ); ?>" class="button-secondary">
		</div>
		<div class="alignleft actions">
			<select id="" name="limit">
				<option value="20" <?php selected($saved_limit, 20);?>>20</option>
				<option value="50" <?php selected($saved_limit, 50);?>>50</option>
				<option value="100" <?php selected($saved_limit, 100);?>>100</option>
			</select>
			<?php _e( 'Forms Per Page', 'ninja-forms' ); ?>
			<input type="submit" name="submit" value="<?php _e( 'Go', 'ninja-forms' ); ?>" class="button-secondary">
		</div>
		<div id="" class="alignright navtable-pages">
			<?php
			if($form_count != 0 AND $current_page <= $page_count){
			?>
			<span class="displaying-num"><?php if($start == 0){ echo 1; }else{ echo $start; }?> - <?php echo $end;?> <?php _e( 'of', 'ninja-forms' ); ?> <?php echo $form_count;?> <?php if($form_count == 1){ _e( 'Form', 'ninja-forms' ); }else{ _e( 'Forms', 'ninja-forms' ); }?></span>
			<?php
			}
				if($page_count > 1){

					$first_page = esc_url( remove_query_arg( 'paged' ) );
					$last_page = esc_url( add_query_arg( array( 'paged' => $page_count ) ) );

					if($current_page > 1){
						$prev_page = $current_page - 1;
						$prev_page = esc_url( add_query_arg( array('paged' => $prev_page ) ) );
					}else{
						$prev_page = $first_page;
					}
					if($current_page != $page_count){
						$next_page = $current_page + 1;
						$next_page = esc_url( add_query_arg( array('paged' => $next_page ) ) );
					}else{
						$next_page = $last_page;
					}

			?>
			<span class="pagination-links">
				<a class="first-page disabled" title="<?php _e( 'Go to the first page', 'ninja-forms' ); ?>" href="<?php echo $first_page;?>">«</a>
				<a class="prev-page disabled" title="<?php _e( 'Go to the previous page', 'ninja-forms' ); ?>" href="<?php echo $prev_page;?>">‹</a>
				<span class="paging-input"><input class="current-page" title="<?php _e( 'Current page', 'ninja-forms' ); ?>" type="text" name="paged" value="<?php echo $current_page;?>" size="2"> <?php _e( 'of', 'ninja-forms' ); ?> <span class="total-pages"><?php echo $page_count;?></span></span>
				<a class="next-page" title="<?php _e( 'Go to the next page', 'ninja-forms' ); ?>" href="<?php echo $next_page;?>">›</a>
				<a class="last-page" title="<?php _e( 'Go to the last page', 'ninja-forms' ); ?>" href="<?php echo $last_page;?>">»</a>
			</span>
			<?php
				}
			?>
		</div>
	</div>
	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" id="" class="ninja-forms-select-all" title="ninja-forms-bulk-action"></th>
				<th><?php _e( 'Form Title', 'ninja-forms' );?></th>
				<th><?php _e( 'Shortcode', 'ninja-forms' );?></th>
				<th><?php _e( 'Template Function', 'ninja-forms' );?></th>
				<th><?php _e( 'Date Updated', 'ninja-forms' );?></th>
			</tr>
		</thead>
		<tbody>
	<?php
	if(is_array($all_forms) AND !empty($all_forms) AND $current_page <= $page_count){
		for ($i = $start; $i < $end; $i++) {
			$form_id = $all_forms[$i];
			$data = Ninja_Forms()->form( $form_id )->get_all_settings();
			$date_updated = $data['date_updated'];
			$date_updated = strtotime( $date_updated );
			$date_updated = date_i18n( 'F d, Y', $date_updated );
			$link = esc_url( remove_query_arg( array( 'paged' ) ) );
			$edit_link = esc_url( add_query_arg( array( 'tab' => 'builder', 'form_id' => $form_id ), $link ) );
			$subs_link = admin_url( 'edit.php?post_status=all&post_type=nf_sub&action=-1&m=0&form_id=' . $form_id . '&paged=1&mode=list&action2=-1' );
			$duplicate_link = esc_url( add_query_arg( array( 'duplicate_form' => 1, 'form_id' => $form_id ), $link ) );
			$shortcode = apply_filters ( "ninja_forms_form_list_shortcode", "[ninja_forms id=" .  $form_id . "]", $form_id );
			$template_function = apply_filters ( "ninja_forms_form_list_template_function", "<pre>if( function_exists( 'ninja_forms_display_form' ) ){ ninja_forms_display_form( " . "$form_id" . " ); }</pre>", $form_id );
			?>
			<tr id="ninja_forms_form_<?php echo $form_id;?>_tr">
				<th scope="row" class="check-column">
					<input type="checkbox" id="" name="form_ids[]" value="<?php echo $form_id;?>" class="ninja-forms-bulk-action">
				</th>
				<td class="post-title page-title column-title">
					<strong>
						<a href="<?php echo $edit_link;?>"><?php echo stripslashes( $data['form_title'] );?></a>
					</strong>
					<div class="row-actions">
						<span class="edit"><a href="<?php echo $edit_link;?>"><?php _e( 'Edit', 'ninja-forms' ); ?></a> | </span>
						<span class="trash"><a class="ninja-forms-delete-form" title="<?php _e( 'Delete this form', 'ninja-forms' ); ?>" href="#" id="ninja_forms_delete_form_<?php echo $form_id;?>"><?php _e( 'Delete', 'ninja-forms' ); ?></a> | </span>
						<span class="duplicate"><a href="<?php echo $duplicate_link;?>" title="<?php _e( 'Duplicate Form', 'ninja-forms' ); ?>"><?php _e( 'Duplicate', 'ninja-forms' ); ?></a> | </span>
						<span class="bleep"><?php echo ninja_forms_preview_link( $form_id ); ?> | </span>
						<span class="subs"><a href="<?php echo $subs_link;?>" class="" title="<?php _e( 'View Submissions', 'ninja-forms' ); ?>"><?php _e( 'View Submissions', 'ninja-forms' ); ?></a></span>
					</div>
				</td>
				<td>
					<?php echo $shortcode; ?>
				</td>
				<td>
					<?php echo $template_function; ?>
				</td>
				<td>
					<?php echo $date_updated;?>
				</td>
			</tr>

			<?php
		}
	}else{


	}	//End $all_forms if statement
	?>
		</tbody>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" id="" class="ninja-forms-select-all" title="ninja-forms-bulk-action"></th>
				<th><?php _e( 'Form Title', 'ninja-forms' );?></th>
				<th><?php _e( 'Shortcode', 'ninja-forms' );?></th>
				<th><?php _e( 'Template Function', 'ninja-forms' );?></th>
				<th><?php _e( 'Date Updated', 'ninja-forms' );?></th>
			</tr>
		</tfoot>
	</table>
	<?php
}

function ninja_forms_save_form_list( $data ){
	if( isset( $data['bulk_action'] ) AND $data['bulk_action'] != '' ){
		if( isset( $data['form_ids'] ) AND is_array( $data['form_ids'] ) AND !empty( $data['form_ids'] ) ){
			foreach( $data['form_ids'] as $form_id ){
				switch( $data['bulk_action'] ){
					case 'delete':
						Ninja_Forms()->form( $form_id )->delete();
						$ninja_forms_admin_update_message = count( $data['form_ids'] ).' ';
						if( count( $data['form_ids'] ) > 1 ){
							$update_message = __( 'Forms Deleted', 'ninja-forms' );
						}else{
							$update_message = __( 'Form Deleted', 'ninja-forms' );
						}
						break;
					case 'export':
						ninja_forms_export_form( $form_id );
						break;
				}
			}
		}
		$debug = ! empty ( $_REQUEST['debug'] ) ? true : false;
		Ninja_Forms()->forms()->update_cache( $debug );
		return $update_message;
	}
}
