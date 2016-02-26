<?php 
/*
 Contributors: Shital Patel, Shine Infoware
 Plugin Name: Contact Information Widget
 Plugin URI: http://shineinfoware.com
 Description: This plugin use for contact information like: Company Name, Address, Phone Number, Email Address. You can use widgets to display it anywhere you want.
 Version: 1.0.0
 Author: Shine Infoware
 Author URI: http://shineinfoware.com
 Text Domain: contact-information-widget
 License: GPL2
 Tags: contact-information, Contact Information Widget, company name, company address, company phone number, company email address.
*/
// Block direct requests
if ( !defined('ABSPATH') ) {
	exit();
}

define( 'PLUGINS_PATH', plugin_dir_url(__FILE__) );

add_action( 'wp_enqueue_scripts', 'add_script_css_js_fonts' );
function add_script_css_js_fonts() {
	wp_enqueue_style('font-awesome', PLUGINS_PATH . '/fonts/font-awesome.css'); 
	wp_enqueue_style('style', PLUGINS_PATH . '/style.css'); 
	
}


// ------------- Plugin Init ------------- 

/* Create Widget Start Here */
/**
 * Adds ContactInformation_Widget widget.
 */
class ContactInformation_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'contactinformation_widget', // Base ID
			__( 'Contact Information Widget', 'text_domain' ), // Name
			array( 'description' => __( 'Display your company name and contact information. ', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		//$show_count = $instance['post_limit'];
		$output='';
		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		
		if ( ! empty( $instance['title'] ) || ! empty( $instance['address'] ) || ! empty( $instance['phone'] ) || ! empty( $instance['contact_email'] )) {
			$output .='<div class="ciw_contactinformation">';
								$output .='<div class="ciw_address">
											<i class="fa fa-map-marker"></i>	
												<div class="ciw_address_content">
													<div class="ciw_company_name">'.$instance['company_name'].',</div>
													<div class="ciw_contact_address">'.nl2br(stripslashes($instance['address'])).'	</div>
												</div>
										</div>';
								$output .='<div class="ciw_phone">
												<i class="fa fa-mobile"></i>
												<div class="ciw_contact_phone"><a href="tel:'.$instance['phone'].'">'.$instance['phone'].'</a></div>
											</div>';
								$output .='<div class="ciw_email">
												<i class="fa fa-envelope"></i>
												<div class="ciw_contact_email"><a href="mailto:'.$instance['contact_email'].'" target="_blank">'.$instance['contact_email'].'</a></div>
											</div>';					
			$output .='</div>';
		}
		
		
	//$output .='</ul>';
			echo $output;
		//echo __( $output, 'text_domain' );
			
		
	echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Contact Us', 'text_domain' );
		$company_name = ! empty( $instance['company_name'] ) ? $instance['company_name'] : __( '', 'text_domain' );
		$address = ! empty( $instance['address'] ) ? $instance['address'] : __( '', 'text_domain' );
		$phone = ! empty( $instance['phone'] ) ? $instance['phone'] : __( '', 'text_domain' );
		$contact_email = ! empty( $instance['contact_email'] ) ? $instance['contact_email'] : __( '', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 'company_name' ); ?>"><?php _e( 'Company Name:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'company_name' ); ?>" name="<?php echo $this->get_field_name( 'company_name' ); ?>" type="text" value="<?php echo esc_attr( $company_name ); ?>">
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 'address' ); ?>"><?php _e( 'Address:' ); ?></label> 
        <textarea class="widefat" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>"><?php echo esc_attr( $address ); ?></textarea>
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 'phone' ); ?>"><?php _e( 'Phone No.:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'phone' ); ?>" name="<?php echo $this->get_field_name( 'phone' ); ?>" type="text" value="<?php echo esc_attr( $phone ); ?>">
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 'contact_email' ); ?>"><?php _e( 'Email ID:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'contact_email' ); ?>" name="<?php echo $this->get_field_name( 'contact_email' ); ?>" type="email" value="<?php echo esc_attr( $contact_email ); ?>">
		</p>
      	<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['company_name'] = ( ! empty( $new_instance['company_name'] ) ) ? strip_tags( $new_instance['company_name'] ) : '';
		$instance['address'] = ( ! empty( $new_instance['address'] ) ) ?  $new_instance['address']  : '';
		$instance['phone'] = ( ! empty( $new_instance['phone'] ) ) ? strip_tags( $new_instance['phone'] ) : '';
		$instance['contact_email'] = ( ! empty( $new_instance['contact_email'] ) ) ? strip_tags( $new_instance['contact_email'] ) : '';
		//$instance['post_limit'] = ( ! empty( $new_instance['post_limit'] ) ) ? strip_tags( $new_instance['post_limit'] ) : '';

		return $instance;
	}

} // class ContactInformation_Widget
// register ContactInformation_Widget widget
function contactinformation_foo_widget() {
    register_widget( 'ContactInformation_Widget' );
}
add_action( 'widgets_init', 'contactinformation_foo_widget' );
/* Create Widget End Here */