<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * MPP Gallery List Widget
 * 
 */
class MPP_Gallery_List_Widget extends WP_Widget {

	public function __construct( $id = false, $title = '' ) {

		if ( ! $title ) {
			$title = _x( '(MediaPress) Galleries List', 'mediapress gallery widget name', 'mediapress' );
		}

		parent::__construct( $id, $title );
	}

	public function widget( $args, $instance ) {

		extract( $args );

		$defaults = array(
			'type'			=> false, //gallery type, all,audio,video,photo etc
			'id'			=> false, //pass specific gallery id
			'in'			=> false, //pass specific gallery ids as array
			'exclude'		=> false, //pass gallery ids to exclude
			'slug'			=> false, //pass gallery slug to include
			'status'		=> false, //public,private,friends one or more privacy level
			'component'		=> false, //one or more component name user,groups, evenets etc
			'component_id'	=> false, // the associated component id, could be group id, user id, event id
			'per_page'		=> false, //how many items per page
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
			'meta_key'		=> '',
			'meta_value'	=> '',
			// 'meta_query'=>false,
			'fields'		=> false, //which fields to return ids, id=>parent, all fields(default)
		);

		$instance = (array) $instance;

		echo $before_widget;

		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . $instance['title'] . $after_title;
		}
		
		unset( $instance['title'] );

		$args = array_merge( $defaults, $instance );

		$query = new MPP_Gallery_Query( $args );

		mpp_widget_save_gallery_data( 'query', $query );

		mpp_get_template_part( 'widgets/gallery-list' ); //shortcodes/gallery-entry.php

		mpp_widget_reset_gallery_data( 'query' );
		
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		if ( mpp_is_active_component( $new_instance['component'] ) ) {
			$instance['component'] = $new_instance['component'];
		}

		if ( mpp_is_active_type( $new_instance['type'] ) ) {
			$instance['type'] = $new_instance['type'];
		}

		if ( mpp_is_active_status( $new_instance['status'] ) ) {
			$instance['status'] = $new_instance['status'];
		}

		$instance['per_page'] = absint( $new_instance['per_page'] );

		$instance['orderby'] = $new_instance['orderby'];

		$instance['order'] = $new_instance['order'];

		return $instance;
	}

	public function form( $instance ) {

		$defaults = array(
			'type'			=> false, //gallery type, all,audio,video,photo etc
			'id'			=> false, //pass specific gallery id
			'in'			=> false, //pass specific gallery ids as array
			'exclude'		=> false, //pass gallery ids to exclude
			'slug'			=> false, //pass gallery slug to include
			'status'		=> false, //public,private,friends one or more privacy level
			'component'		=> false, //one or more component name user,groups, evenets etc
			'component_id'	=> false, // the associated component id, could be group id, user id, event id
			'per_page'		=> false, //how many items per page
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
			'meta_key'		=> '',
			'meta_value'	=> '',
			// 'meta_query'=>false,
			'fields'		=> false, //which fields to return ids, id=>parent, all fields(default)
			'column'		=> 4,
			'title'			=> __( 'Recent Galleries', 'mediapress' )
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		extract( $instance );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mediapress' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" />
			</label>
		</p>
		<table>

			<tr>
				<td><label for="<?php echo $this->get_field_id( 'component' ); ?>"><?php _e( 'Select Component:', 'mediapress' ); ?></label></td>
				<td>

				<?php
					mpp_component_dd( array(
						'name'		=> $this->get_field_name( 'component' ),
						'id'		=> $this->get_field_id( 'component' ),
						'selected'	=> $component
					) );
				?>
				</td>
			</tr>
			<tr>
				<td><label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Select Type:', 'mediapress' ); ?></label></td>
				<td>

				<?php
					mpp_type_dd( array(
						'name'		=> $this->get_field_name( 'type' ),
						'id'		=> $this->get_field_id( 'type' ),
						'selected'	=> $type,
					) );
				?>
				</td>
			</tr>
			<tr>
				<td><label for="<?php echo $this->get_field_id( 'status' ); ?>"><?php _e( 'Select Status:', 'mediapress' ); ?></label></td>
				<td>
				<?php
					mpp_status_dd( array(
						'name'		=> $this->get_field_name( 'status' ),
						'id'		=> $this->get_field_id( 'status' ),
						'selected'	=> $status,
					) );
				?>
				</td>
			</tr>
			<tr>
				<td><label for="<?php echo $this->get_field_id( 'per_page' ); ?>"><?php _e( 'Per Page:', 'mediapress' ); ?></label></td>
				<td>
					<input class="" id="<?php echo $this->get_field_id( 'per_page' ); ?>" name="<?php echo $this->get_field_name( 'per_page' ); ?>" type="number" value="<?php echo absint( $per_page ); ?>" />

				</td>
			</tr>

			<tr>
				<td><label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order By:', 'mediapress' ); ?></label></td>
				<td>
					<select  id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>" >
						<option value="title" <?php selected( 'title', $orderby ); ?>><?php _e( 'Alphabet', 'mediapress' ); ?></option>
						<option value="date" <?php selected( 'date', $orderby ); ?>><?php _e( 'Date', 'mediapress' ); ?></option>
						<option value="rand" <?php selected( 'rand', $orderby ); ?>><?php _e( 'Random', 'mediapress' ); ?></option>
					</select>	
				</td>
			</tr>
			<tr>
				<td><label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Sort Order', 'mediapress' ); ?></label></td>
				<td>
					<select  id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" >
						<option value="ASC" <?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'mediapress' ); ?></option>
						<option value="DESC" <?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'mediapress' ); ?></option>
					</select>	
				</td>
			</tr>
		</table>

		<?php
	}

}

function mpp_register_gallery_list_widget() {

	register_widget( 'MPP_Gallery_List_Widget' );
}

add_action( 'mpp_widgets_init', 'mpp_register_gallery_list_widget' );
