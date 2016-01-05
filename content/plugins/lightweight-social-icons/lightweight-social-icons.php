<?php
/*
Plugin Name: Lightweight Social Icons
Plugin URI: http://generatepress.com/lightweight-social-icons
Description: Add simple icon font social media buttons. Choose the order, colors, size and more for 39 different icons!
Version: 0.7
Author: Thomas Usborne
Author URI: http://edge22.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


/**
 * Load plugin textdomain.
 *
 * @since 0.1
 */
add_action( 'plugins_loaded', 'lsi_load_textdomain' );
function lsi_load_textdomain() {
  load_plugin_textdomain( 'lsi', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}

class lsi_Widget extends WP_Widget {

	/**
	 * Register the widget
	 * @since 0.1
	 */
	function __construct() {
		parent::__construct(
			'lsi_Widget', // Base ID
			__('Lightweight Social Icons', 'lsi'), // Name
			array(
				'description' => __( 'Add social icons to your website.', 'lsi' ), 
			),
			array(
				'width' => 550,
			)
		);
		
		$this->scripts['lsi_scripts'] = false;
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_footer-widgets.php', array( $this, 'print_admin_scripts' ), 9999 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_admin_customizer_scripts' ), 9999 );
	}

	/**
	 * Front-end display of widget.
	 * @since 0.1
	 */
	public function widget( $args, $instance ) {
		
		$this->scripts['lsi_scripts'] = true;
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		$options = lsi_icons();
		$defaults = lsi_option_defaults();
		$unique_id = $args['widget_id'];
		$output = '';
		
		echo $args['before_widget'];
		
		echo ( ! empty( $title ) ) ? $args['before_title'] . $title . $args['after_title'] : '';
		
		$new_window = ( isset( $instance['new_window'] ) && '' !== $instance['new_window'] ) ? 'target="_blank"' : $defaults['new_window'];
		$font_size = ( isset( $instance['font_size'] ) && '' !== $instance['font_size'] ) ? $instance['font_size'] : $defaults['font_size'];
		$border_radius = ( isset( $instance['border_radius'] ) && '' !== $instance['border_radius'] ) ? $instance['border_radius'] : $defaults['border_radius'];
		$background = ( isset( $instance['background'] ) && '' !== $instance['background'] ) ? $instance['background'] : $defaults['background'];
		$color = ( isset( $instance['color'] ) && '' !== $instance['color'] ) ? $instance['color'] : $defaults['color'];
		$background_hover = ( isset( $instance['background_hover'] ) && '' !== $instance['background_hover'] ) ? $instance['background_hover'] : $defaults['background_hover'];
		$color_hover = ( isset( $instance['color_hover'] ) && '' !== $instance['color_hover'] ) ? $instance['color_hover'] : $defaults['color_hover'];
		$alignment = ( isset( $instance['alignment'] ) && '' !== $instance['alignment'] ) ? $instance['alignment'] : $defaults['alignment'];
		$tooltip = ( isset( $instance['tooltip'] ) && '' !== $instance['tooltip'] ) ? $instance['tooltip'] : $defaults['tooltip'];
		
		$count = 0;
		foreach ( $options as $option ) {
		
			$input = 'input' . $count++;
			$select = 'select' . $count++;
		
			$id = (!empty( $instance[$option['id']] ) ) ? $instance[$option['id']] : '';
			$name = (!empty( $instance[$select] ) ) ? $instance[$select] : '';
			$value = (!empty( $instance[$input] ) ) ? $instance[$input] : '';

			if ( !empty( $value ) && !empty( $name ) ) :
				if ( is_email( $value ) ) :
					$the_value = 'mailto:' . $value;
				elseif ( 'phone' == $name ) :
					$the_value = 'tel:' . $value;
				elseif ( 'skype' == $name ) :
					$the_value = 'skype:' . $value;
				else:
					$the_value = esc_url( $value );
				endif;
				
				$show_tooltip = ( ! empty( $tooltip ) ) ? 'tooltip' : '';
				$rel_attribute = apply_filters( 'lsi_icon_rel_attribute','rel="nofollow"' );
				$title_attribute = apply_filters( 'lsi_icon_title_attribute','title="' . $options[$name]['name'] . '"' );
				
				$output .= sprintf( 
					'<li class="lsi-social-%3$s"><a class="%4$s" %5$s %6$s href="%1$s" %2$s><i class="lsicon lsicon-%3$s"></i></a></li>',
					$the_value,
					$new_window,
					$name,
					$show_tooltip,
					$rel_attribute,
					$title_attribute
				);

			endif;
		}
		if ( $output ) :
			printf( 
				'<ul class="lsi-social-icons icon-set-%1$s">%2$s</ul>', 
				$unique_id,
				apply_filters( 'lsi_icon_output', $output )
			);
		endif;
		
		global $css;
		$css = '
			.icon-set-' . $unique_id . ' {
				text-align:' . $alignment . ' !important;
			}
			.icon-set-' . $unique_id . ' a, 
			.icon-set-' . $unique_id . ' a:visited, 
			.icon-set-' . $unique_id . ' a:focus {
				border-radius: ' . $border_radius . 'px;
				background: ' . $background . ' !important;
				color: ' . $color . ' !important;
				font-size: ' . $font_size . 'px !important;
			}
			.icon-set-' . $unique_id . ' a:hover {
				background: ' . $background_hover . ' !important;
				color: ' . $color_hover . ' !important;
			}';
			wp_enqueue_style( 'lsi-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), null, 'all' );
			wp_add_inline_style( 'lsi-style', $css, 99 );
		if ( !empty( $tooltip ) ) :
			wp_enqueue_script( 'lsi-tooltipster', plugin_dir_url( __FILE__ ) . 'js/jquery.tooltipster.min.js', array('jquery'), '1.0', true );
		endif;
			
		echo $args['after_widget'];
	}
	

	/**
	 * Build the actual widget in the Dashboard
	 * @since 0.1
	 */
	public function form( $instance ) {
	
		$options = lsi_icons();
		
		$defaults = lsi_option_defaults();
		
		$title = ( isset( $instance[ 'title' ] ) && '' !== $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
		$border_radius = ( isset( $instance[ 'border_radius' ] ) && '' !== $instance[ 'border_radius' ] ) ? $instance[ 'border_radius' ] : $defaults['border_radius'];
		$font_size = ( isset( $instance[ 'font_size' ] ) && '' !== $instance[ 'font_size' ] ) ? $instance[ 'font_size' ] : $defaults['font_size'];
		$background = ( isset( $instance[ 'background' ] ) && '' !== $instance[ 'background' ] ) ? $instance[ 'background' ] : $defaults['background'];
		$color = ( isset( $instance[ 'color' ] ) && '' !== $instance[ 'color' ] ) ? $instance[ 'color' ] : $defaults['color'];
		$background_hover = ( isset( $instance[ 'background_hover' ] ) && '' !== $instance[ 'background_hover' ] ) ? $instance[ 'background_hover' ] : $defaults['background_hover'];
		$color_hover = ( isset( $instance[ 'color_hover' ] ) && '' !== $instance[ 'color_hover' ] ) ? $instance[ 'color_hover' ] : $defaults['color_hover'];
		$alignment = ( isset( $instance[ 'alignment' ] ) && '' !== $instance[ 'alignment' ] ) ? $instance[ 'alignment' ] : $defaults['alignment'];
		$tooltip = ( isset( $instance[ 'tooltip' ] ) && '' !== $instance[ 'tooltip' ] ) ? $instance[ 'tooltip' ] : $defaults['tooltip'];
		
		$c = 0;
		foreach ( $options as $option ) {
			$defaults['input' . $c++] = '';
			$defaults['select' . $c++] = '';
		}
			
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		
		<p class="lsi-admin-label-left">
			<label>
				<input class="widefat" style="max-width:65px;" id="<?php echo $this->get_field_id( 'font_size' ); ?>" name="<?php echo $this->get_field_name( 'font_size' ); ?>" type="text" value="<?php echo intval( $font_size ); ?>">
				<span class="pixels">px</span>
				<?php esc_html_e( 'Icon Size', 'lsi' ); ?>
			</label>
		</p>
		
		<p class="lsi-admin-label-right">
			<label>
				<input class="widefat" style="max-width:65px;" id="<?php echo $this->get_field_id( 'border_radius' ); ?>" name="<?php echo $this->get_field_name( 'border_radius' ); ?>" type="text" value="<?php echo intval( $border_radius ); ?>"> 
				<span class="pixels">px</span>
				<?php esc_html_e( 'Border Radius', 'lsi' ); ?>
			</label>
		</p>
		
		<div class="lsi-divider"></div>
		
		<p class="lsi-admin-label-left">
			<label>
				<input class="widefat color-picker" style="max-width:75px;" id="<?php echo $this->get_field_id( 'background' ); ?>" name="<?php echo $this->get_field_name( 'background' ); ?>" type="text" value="<?php echo $background; ?>"> 
				<br /><?php esc_html_e( 'Background Color', 'lsi' ); ?>
			</label>
		</p>
		
		<p class="lsi-admin-label-right">
			<label>
				<input class="widefat color-picker" style="max-width:75px;" id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" type="text" value="<?php echo $color; ?>"> 
				<br /><?php esc_html_e( 'Text Color', 'lsi' ); ?>
			</label>
		</p>
		
		<div class="lsi-divider"></div>
		
		<p class="lsi-admin-label-left">
			<label>
				<input class="widefat color-picker" style="max-width:75px;" id="<?php echo $this->get_field_id( 'background_hover' ); ?>" name="<?php echo $this->get_field_name( 'background_hover' ); ?>" type="text" value="<?php echo $background_hover; ?>"> 
				<br /><?php esc_html_e( 'Background Hover Color', 'lsi' ); ?>
			</label>
		</p>
		
		<p class="lsi-admin-label-right">
			<label>
				<input class="widefat color-picker" style="max-width:75px;" id="<?php echo $this->get_field_id( 'color_hover' ); ?>" name="<?php echo $this->get_field_name( 'color_hover' ); ?>" type="text" value="<?php echo $color_hover; ?>"> 
				<br /><?php esc_html_e( 'Text Hover Color', 'lsi' ); ?>
			</label>
		</p>
		
		<div class="lsi-divider"></div>
		
		<p>
			<label>
				<input id="<?php echo $this->get_field_id( 'new_window' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'new_window' ); ?>" value="1" <?php checked( 1, $instance['new_window'] ); ?>/> 
				<?php esc_html_e( 'Open links in new window?', 'lsi' ); ?>
			</label>
		</p>
		
		<p>
			<label>
				<input id="<?php echo $this->get_field_id( 'tooltip' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'tooltip' ); ?>" value="1" <?php checked( 1, $instance['tooltip'] ); ?>/> 
				<?php esc_html_e( 'Enable tooltips?', 'lsi' ); ?>
			</label>
		</p>
		
		<p>
			<select name="<?php echo $this->get_field_name( 'alignment' );?>" id="<?php echo $this->get_field_id( 'alignment' );?>">
				<option value="left" <?php selected( $instance['alignment'], 'left' ); ?>><?php _e('Left','lsi');?></option>
				<option value="center" <?php selected( $instance['alignment'], 'center' ); ?>><?php _e('Center','lsi');?></option>
				<option value="right" <?php selected( $instance['alignment'], 'right' ); ?>><?php _e('Right','lsi');?></option>
			</select>
			<?php esc_html_e( 'Alignment', 'lsi' ); ?>
		</p>
		<div class="lsi-divider"></div>
		<div class="social-icon-fields">
		<?php 
		$count = 0;
		foreach ( $options as $option ) {
			
			$input = 'input' . $count++;
			$select = 'select' . $count++;
			?>
			<div class="lsi-container">
				<select class="left choose-icon" name="<?php echo $this->get_field_name( $select );?>" id="<?php echo $this->get_field_id( $select );?>">
					<option value=""></option>
					<?php foreach ( $options as $option ) {  ?>
						<option value="<?php echo $option['id']; ?>" <?php selected( $instance[$select], $option['id'] ); ?>><?php echo $option['name']; ?></option>
					<?php } ?>
				</select>
				
				<input class="widefat right social-item" id="<?php echo $this->get_field_id( $input ); ?>" name="<?php echo $this->get_field_name( $input ); ?>" type="text" value="<?php echo esc_attr( $instance[$input] ); ?>">
			</div>
		<?php
		}
		?>
		</div>
		<?php
	}

	/**
	 * Save and sanitize values
	 * @since 0.1
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$options = lsi_icons();
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['border_radius'] = intval( $new_instance['border_radius'] );
		$instance['font_size'] = intval( $new_instance['font_size'] );
		$instance['background'] = lsi_sanitize_hex_color( $new_instance['background'] );
		$instance['color'] = lsi_sanitize_hex_color( $new_instance['color'] );
		$instance['background_hover'] = lsi_sanitize_hex_color( $new_instance['background_hover'] );
		$instance['color_hover'] = lsi_sanitize_hex_color( $new_instance['color_hover'] );
		$instance['new_window'] = strip_tags( $new_instance['new_window'] );
		$instance['alignment'] = strip_tags( $new_instance['alignment'] );
		$instance['tooltip'] = strip_tags( $new_instance['tooltip'] );
		$count = 0;
		foreach ( $options as $option ) {

			$input = 'input' . $count++;
			$select = 'select' . $count++;
			
			$instance[$select] = strip_tags( $new_instance[$select] );

			// If Skype is set, strip tags
			if ( 'skype' == $new_instance[$select] ) :
				$instance[$input] = strip_tags( $new_instance[$input] );
			
			// If Phone is set, strip tags
			elseif ( 'phone' == $new_instance[$select] ) :
				$instance[$input] = strip_tags( $new_instance[$input] );
				
			// If Email is set, sanitize the address
			elseif ( 'email' == $new_instance[$select] ) :
			
				if ( is_email( $new_instance[$input] ) ) {
					$instance[$input] = sanitize_email( $new_instance[$input] );
				} else {
					$instance[$input] = esc_url( $new_instance[$input] );
				}
				
			// For everything else, sanitize the URL
			else :
				$instance[$input] = esc_url( $new_instance[$input] );
			endif;

		}

		return $instance;
	}
	
	/**
	 * Enqueue the front-end CSS
	 * @since 0.1
	 */
	function enqueue_scripts() {
	
		
		
	}
	
	/**
	 * Enqueue the admin scripts
	 * @since 0.1
	 */
	function enqueue_admin_scripts( $hook ) {
	
		 if ( 'widgets.php' != $hook ) {
			return;
		}
		
		wp_enqueue_style( 'wp-color-picker' );        
		wp_enqueue_script( 'wp-color-picker' ); 
		wp_enqueue_script( 'lsi-script', plugin_dir_url( __FILE__ ) . 'js/scripts.js', array('jquery'), '1.0', true );
		wp_enqueue_style( 'lsi-admin-script', plugin_dir_url( __FILE__ ) . 'css/admin.css' );
		wp_localize_script( 'lsi-script', 'lsiPlaceholder', array(
			'phone'  => __( '1 (123)-456-7890','lsi'),
			'email' => __( 'you@yourdomain.com or http://', 'lsi' ),
			'username' => __( 'Username', 'lsi' ),
		) );
	}
	
	public function print_admin_scripts() {
	 ?>
		<script>
			jQuery(document).ready(function($){
				function lsi_updateColorPickers(){
					
					$('#widgets-right .color-picker, #accordion-panel-widgets .color-picker').each(function(){
						$(this).wpColorPicker({
							// you can declare a default color here,
							// or in the data-default-color attribute on the input
							defaultColor: false,
							// a callback to fire whenever the color changes to a valid color
							change: function(event, ui){},
							// a callback to fire when the input is emptied or an invalid color
							clear: function() {},
							// hide the color picker controls on load
							hide: true,
						});
					}); 
				}
				lsi_updateColorPickers();  
				
				$(document).ajaxSuccess(function(e, xhr, settings) {

					if(settings.data.search('action=save-widget') != -1 ) { 
						$('.color-field .wp-picker-container').remove();    
						lsi_updateColorPickers();       
					}
				});
			});
			
		</script>
		<?php
	}
	
	public function print_admin_customizer_scripts() {
	 ?>
		<script>
			( function( $ ){
				function lsi_initColorPicker( widget ) {
					widget.find( '.color-picker' ).wpColorPicker( {
							change: _.throttle( function() { // For Customizer
									$(this).trigger( 'change' );
							}, 3000 )
					});
				}

				function lsi_onFormUpdate( event, widget ) {
					lsi_initColorPicker( widget );
				}

				$( document ).on( 'widget-added widget-updated', lsi_onFormUpdate );

				$( document ).ready( function() {
					$( '#accordion-panel-widgets .widget:has(.color-picker)' ).each( function () {
							lsi_initColorPicker( $( this ) );
					} );
				} );
			}( jQuery ) );
			
			jQuery(document).ready(function($){
				function lsi_updatePlaceholders(){
					$('#accordion-panel-widgets .choose-icon').each(function(){
						jQuery(this).change(function() {
							var select = jQuery(this);
										
							if ( jQuery(this).attr('value') == 'phone' ) {
								jQuery(this).next('input').attr('placeholder',lsiPlaceholder.phone);
							} else if ( jQuery(this).attr('value') == 'email' ) {
								jQuery(this).next().attr('placeholder',lsiPlaceholder.email);
							} else if ( jQuery(this).attr('value') == 'skype' ) {
								jQuery(this).next().attr('placeholder',lsiPlaceholder.username);
							}else if ( jQuery(this).attr('value') == '' ) {
								jQuery(this).next().attr('placeholder','');
							} else {
								jQuery(this).next().attr('placeholder','http://');
							}
						});
					}); 
				}
				lsi_updatePlaceholders();   
				$(document).ajaxSuccess(function(e, xhr, settings) {

					if(settings.data.search('action=save-widget') != -1 ) {  
						lsi_updatePlaceholders();       
					}
				});
				
				$( document ).on( 'widget-added widget-updated', lsi_updatePlaceholders );
				
				
			});
		</script>
		<?php
	}

}

/**
 * Register the widget
 * @since 0.1
 */
function lsi_register_widget() {
    register_widget( 'lsi_Widget' );
}
add_action( 'widgets_init', 'lsi_register_widget' );

/**
 * Set the widget option defaults
 * @since 0.1
 */
function lsi_option_defaults()
{
	$defaults = array(
		'title' => '',
		'new_window' => '',
		'border_radius' => 2,
		'font_size' => 20,
		'background' => '#1E72BD',
		'color' => '#FFFFFF',
		'background_hover' => '#777777',
		'color_hover' => '#FFFFFF',
		'alignment' => 'left',
		'tooltip' => ''
	);
	
	return apply_filters( 'lsi_option_defaults', $defaults );
}

/**
 * Set the default widget icons
 * @since 0.1
 */
function lsi_icons( $options = '' ) {
	$options = array (
		'facebook' => array(
			'id' => 'facebook',
			'name' => __( 'Facebook', 'lsi' )
		),
		'twitter' => array(
			'id' => 'twitter',
			'name' => __( 'Twitter', 'lsi' )
		),
		'gplus' => array(
			'id' => 'gplus',
			'name' => __( 'Google+', 'lsi' )
		),
		'instagram' => array(
			'id' => 'instagram',
			'name' => __( 'Instagram', 'lsi' )
		),
		'linkedin' => array(
			'id' => 'linkedin',
			'name' => __( 'LinkedIn', 'lsi' )
		),
		'pinterest' => array(
			'id' => 'pinterest',
			'name' => __( 'Pinterest', 'lsi' )
		),
		'flickr' => array(
			'id' => 'flickr',
			'name' => __( 'Flickr', 'lsi' )
		),
		'email' => array(
			'id' => 'email',
			'name' => __( 'Contact', 'lsi' )
		),
		'rss' => array(
			'id' => 'rss',
			'name' => __( 'RSS', 'lsi' )
		),
		'stumbleupon' => array(
			'id' => 'stumbleupon',
			'name' => __( 'Stumbleupon', 'lsi' )
		),
		'tumblr' => array(
			'id' => 'tumblr',
			'name' => __( 'Tumblr', 'lsi' )
		),
		'vimeo' => array(
			'id' => 'vimeo',
			'name' => __( 'Vimeo', 'lsi' )
		),
		'youtube' => array(
			'id' => 'youtube',
			'name' => __( 'YouTube', 'lsi' )
		),
		'github' => array(
			'id' => 'github',
			'name' => __( 'Github', 'lsi' )
		),
		'soundcloud' => array(
			'id' => 'soundcloud',
			'name' => __( 'Soundcloud', 'lsi' )
		),
		'deviantart' => array(
			'id' => 'deviantart',
			'name' => __( 'DeviantArt', 'lsi' )
		),
		'phone' => array(
			'id' => 'phone',
			'name' => __( 'Phone', 'lsi' )
		),
		'skype' => array(
			'id' => 'skype',
			'name' => __( 'Skype', 'lsi' )
		),
		'dribbble' => array(
			'id' => 'dribbble',
			'name' => __( 'Dribbble', 'lsi' )
		),
		'foursquare' => array(
			'id' => 'foursquare',
			'name' => __( 'Foursquare', 'lsi' )
		),
		'reddit' => array(
			'id' => 'reddit',
			'name' => __( 'Reddit', 'lsi' )
		),
		'spotify' => array(
			'id' => 'spotify',
			'name' => __( 'Spotify', 'lsi' )
		),
		'digg' => array(
			'id' => 'digg',
			'name' => __( 'Digg', 'lsi' )
		),
		'vine' => array(
			'id' => 'vine',
			'name' => __( 'Vine', 'lsi' )
		),
		'codepen' => array(
			'id' => 'codepen',
			'name' => __( 'Codepen', 'lsi' )
		),
		'delicious' => array(
			'id' => 'delicious',
			'name' => __( 'Delicious', 'lsi' )
		),
		'jsfiddle' => array(
			'id' => 'jsfiddle',
			'name' => __( 'JSFiddle', 'lsi' )
		),
		'stackoverflow' => array(
			'id' => 'stackoverflow',
			'name' => __( 'Stack Overflow', 'lsi' )
		),
		'wordpress' => array(
			'id' => 'wordpress',
			'name' => __( 'WordPress', 'lsi' )
		),
		'dropbox' => array(
			'id' => 'dropbox',
			'name' => __( 'Dropbox', 'lsi' )
		),
		'steam' => array(
			'id' => 'steam',
			'name' => __( 'Steam', 'lsi' )
		),
		'behance' => array(
			'id' => 'behance',
			'name' => __( 'Behance', 'lsi' )
		),
		'itunes' => array(
			'id' => 'itunes',
			'name' => __( 'iTunes', 'lsi' )
		),
		'yelp' => array(
			'id' => 'yelp',
			'name' => __( 'Yelp', 'lsi' )
		),
		'twitch' => array(
			'id' => 'twitch',
			'name' => __( 'Twitch', 'lsi' )
		),
		'fivehundredpx' => array(
			'id' => 'fivehundredpx',
			'name' => __( '500px', 'lsi' )
		),
		'angellist' => array(
			'id' => 'angellist',
			'name' => __( 'AngelList', 'lsi' )
		),
		'bloglovin' => array(
			'id' => 'bloglovin',
			'name' => __( "Blog Lovin'", 'lsi' )
		),
		'paper-plane' => array(
			'id' => 'paper-plane',
			'name' => __( "Newsletter", 'lsi' )
		),
		'vkontakte' => array(
			'id' => 'vkontakte',
			'name' => __( "VK", 'lsi' )
		)
	);
		
	return apply_filters( 'lsi_icons_defaults', $options );
}

/**
 * Function to sanitize the hex values
 * @since 0.1
 */
function lsi_sanitize_hex_color( $color ) {
	if ( '' === $color )
		return '';

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
		return $color;

	return null;
}