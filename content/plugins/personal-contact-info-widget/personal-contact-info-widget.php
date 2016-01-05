<?php

	/**
	 * Plugin Name: Personal Contact Info Widget
	 * Description: Custom Widget for displaying your photo and personal contact information.
	 * Version: 1.2
	 * Author: Juan Sanchez Jr.
	 * License: GPLv2 or later
	 */
	 
	/**  
	 * Copyright 2014  Juan Sanchez Jr. ( email : bringmesupport@gmail.com )
	 * This program is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License, version 2, as 
	 * published by the Free Software Foundation.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 */

	defined('ABSPATH') or die("No script kiddies please!");

	class jsjr_personal_contact_info extends WP_Widget {

		private $ver = '1.2';
		private $domain = 'pci_text_domain';
		private $social_icons = array(
				'fa-facebook-square' 	=> 'Facebook',
				'fa-youtube-square' 	=> 'YouTube',
				'fa-twitter-square' 	=> 'Twitter',
				'fa-linkedin-square' 	=> 'LinkedIn',
				'fa-google-plus-square' => 'Google Plus',
				'fa-skype'              => 'Skype',
				'fa-dropbox'		=> 'Dropbox',
				'fa-yelp'		=> 'Yelp',
				'fa-instagram'          => 'Instagram',
				'fa-pinterest'		=> 'Pinterest',
				'fa-wordpress'		=> 'WordPress',
				'fa-vine'		=> 'Vine',
				'fa-vimeo-square'	=> 'Vimeo',
				'fa-tumblr-square'	=> 'Tumblr',
				'fa-foursquare'		=> 'Foursquare',
				'fa-digg'		=> 'Digg',
				'fa-skype'		=> 'Skype',
				'fa-rss-square'         => 'RSS',
				'fa-github'		=> 'GitHub',
				'fa-bitbucket-square'	=> 'Bitbucket',
				'fa-stack-overflow'	=> 'Stack Overflow'
		);
		
                
		public function __construct() {
			$widget_ops = array(
				'description' => __( 'Custom Widget for displaying your photo, social media links and contact information.', $this->domain ),
				'customizer_support' => true
			);
			$control_ops = array();
			parent::__construct( false, __( 'Personal Contact Info' , $this->domain ), $widget_ops, $control_ops );
			add_action( 'wp_enqueue_scripts', array( $this, 'jsjr_pci_wp_styles_and_scripts' ));
			add_action( 'admin_enqueue_scripts', array( $this, 'jsjr_pci_admin_styles_and_scripts' ));
		}

		
		public function widget( $args, $instance ) {
			extract( $args );
			extract( $instance );
			
			echo $before_widget;
			
			echo '<div class="jsjr-pci-contact-section" style="text-align:', $jsjr_widget_align , '" >';
			
			if ( !empty( $title ) ) {
                            echo $before_title , $title , $after_title;
			}
			
			if( !empty( $profile_image_url ) && !empty( $profile_image ) && !empty( $profile_image_below ) && !empty( $profile_image_width ) ){
                            if ( $profile_image_below  === 'unchecked' ) {
                                
                                echo '<img src="' , $profile_image_url , '" class="jsjr-pci-photo ', $profile_image , '" style="width:', $profile_image_width , ' ; margin-top:', $profile_image_spacing_above , '; margin-bottom:' , $profile_image_spacing_below , ';" alt="Profile Photo" />';
                            } 
			} 
			
			if ( !empty( $full_name ) && !empty( $full_name_size ) && !empty( $full_name_spacing ) ) {
                            echo '<h2 class="jsjr-pci-name" style="font-size:', $full_name_size , '; margin-bottom:' , $full_name_spacing , '" >' , $full_name , '</h2>';
			}
			
			if ( !empty( $slogan ) && !empty( $slogan_size ) && !empty( $slogan_spacing ) ) {
                            echo '<p class="jsjr-pci-slogan" style="font-size:', $slogan_size , '; margin-bottom:' , $slogan_spacing , '" >' , $slogan , '</p>';
			}
			
			if ( $this->is_social_links( $instance ) ){
                            echo '<div class="jsjr-pci-social-icons" style="margin-bottom:' , $jsjr_social_spacing , '">';
                            foreach ( $this->social_icons as $fa_class => $icon ) {
                                if ( !empty( $instance[ $fa_class ] ) ) {
                                    echo '<a href="' , $instance[ $fa_class ] , '" class="fa ' , $fa_class , '" target="_blank" ></a>';
                                }
                            }
                            echo '</div>';
                        }
			
			if ( !empty( $email ) && !empty( $email_size ) && !empty( $email_spacing ) ){
                            echo '<p class="jsjr-pci-email" style="font-size:', $email_size , '; margin-bottom:' , $email_spacing , '">' , $email , '</p>';
			}
			
			if ( !empty( $phone ) && !empty( $phone_size ) && !empty( $phone_spacing ) ) {
                            echo '<p class="jsjr-pci-phone" style="font-size:', $phone_size , '; margin-bottom:' , $phone_spacing , '">' , $phone , '</p>';
			}
			
			if ( !empty( $website ) && !empty( $website_size ) && !empty( $website_spacing ) ) {
                            echo '<p class="jsjr-pci-website" style="font-size:', $website_size , '; margin-bottom:' , $website_spacing , '">' , $website , '</p>';
			}
			
			if( !empty( $profile_image_url ) && !empty( $profile_image ) && !empty( $profile_image_below ) && !empty( $profile_image_width ) ){
                            if ( $profile_image_below  === 'checked' ) {
                                    echo '<img src="' , $profile_image_url , '" class="jsjr-pci-photo ', $profile_image , '" alt="Profile Photo" />';
                            } 
			} 
				
			echo '</div>';
			
			echo $after_widget;
			
		}

		
		public function update( $new_instance, $old_instance ) {
			foreach ( $new_instance as $key => $value ) {
				$old_instance[ $key ] = trim( strip_tags( $value ) );
			}
			/*
                         * Since checkboxes do not return anything when unselected,
                         * if no value was returned we need to manually update the value.
                         * Otherwise the checkbox field could get stuck on checked when saving.
                         * */
			$old_instance[ 'profile_image_below' ] = isset( $new_instance[ 'profile_image_below' ] ) ? $new_instance[ 'profile_image_below' ] : 'unchecked';
			$old_instance[ 'fa_existing' ] = isset( $new_instance[ 'fa_existing' ] ) ? $new_instance[ 'fa_existing' ] : 'unchecked';
			return $old_instance;
		}
		
		
		public function form( $instance ) {	
		
                    foreach ( $instance as $key => $value ) {
                        $$key = esc_attr( $value );
                    }

                    $defaults = array (
                        'title'				=> '',
                        'profile_image'			=> 'jsjr-pci-photo-square',
                        'profile_image_url'		=> '',
                        'profile_image_width'		=> 'auto',
                        'profile_image_spacing_above'	=> '0px',
                        'profile_image_spacing_below'	=> '20px',
                        'profile_image_below'		=> 'unchecked',
                        'full_name'			=> '',
                        'full_name_size'		=> '25px',
                        'full_name_spacing'		=> '0px',
                        'slogan'			=> '',				
                        'slogan_size'			=> '15px',
                        'slogan_spacing'		=> '20px',
                        'email'				=> '',				
                        'email_size' 			=> '15px',
                        'email_spacing'			=> '20px',
                        'phone'				=> '',
                        'phone_size' 			=> '15px',
                        'phone_spacing'			=> '20px',
                        'website'			=> '',
                        'website_size'			=> '15px',				
                        'website_spacing'		=> '20px',
                        'fa_existing'			=> 'unchecked',
                        'jsjr_widget_align'		=> 'center',
                        'jsjr_social_spacing'           => '20px'
                    );

                    // Set default values for text fields
                    foreach ( $defaults as $key => $value ) {
                        ${$key} = isset( ${$key} ) ? ${$key} : $value;
                    }

                    $this->get_field_text( $args = array(
                        'field_name'        => 'title',
                        'instance_value'    => $title,
                        'description'       => 'Title:'
                    ));
                    ?>

                    <div class="jsjr-pci-accordion">

                        <h3 class="jsjr-pci-toggle" >Profile Photo</h3>
                        <div style="display:none;" >
                            <?php

                            $this->get_field_image( $args = array(
                                'field_name'        => 'profile_image_url',
                                'instance_value'    => $profile_image_url,
                                'description'       => 'Link to Profile Image (URL):'
                            ));                        

                            $this->get_field_select( $args = array(
                                'field_name'        => 'profile_image',
                                'instance_value'    => $profile_image,
                                'description'       => 'Image Style:',
                                'value_range'       => array (
                                    'jsjr-pci-photo-square'     => 'Square',
                                    'jsjr-pci-photo-circle'     => 'Round',
                                    'jsjr-pci-photo-rcorners'   => 'Rounded Corners',
                                    'jsjr-pci-photo-thumbnail'  => 'Thumbnail'
                                ),
                                'measurement_type'	=> ''
                            ));

                            $this->get_field_select( $args = array(
                                'field_name'		=> 'profile_image_spacing_above',
                                'instance_value'	=> $profile_image_spacing_above,
                                'description' 		=> 'Spacing Above Image',
                                'value_range' 		=> range( 0, 200, 10 ),
                                'measurement_type'	=> 'px'
                            ));
                            
                            $this->get_field_select( $args = array(
                                'field_name'		=> 'profile_image_spacing_below',
                                'instance_value'	=> $profile_image_spacing_below,
                                'description' 		=> 'Spacing Below Image',
                                'value_range' 		=> range( 0, 200, 10 ),
                                'measurement_type'	=> 'px'
                            ));

                            $this->get_field_select( $args = array(
                                'field_name'		=> 'profile_image_width',
                                'instance_value'	=> $profile_image_width,
                                'description' 		=> 'Width (percents based on widget area width):',
                                'value_range' 		=> array (
                                    'auto'  => 'Use Images\'s Width',
                                    '100%'  => '100%',
                                    '90%'   => '90%',
                                    '80%'   => '80%',
                                    '70%'   => '70%',
                                    '60%'   => '60%',
                                    '50%'   => '50%',
                                    '40%'   => '40%',
                                    '30%'   => '30%',
                                    '20%'   => '20%',
                                    '10%'   => '10%'
                                ),
                                'measurement_type'	=> ''
                            ));

                            $this->get_field_checkbox( $args = array(
                                'field_name'        => 'profile_image_below',
                                'instance_value'    => $profile_image_below,
                                'description'       => 'Move photo below Contact Details',
                                'return_value'      => 'checked'
                            ));

                            ?>
                        </div>

                        <h3 class="jsjr-pci-toggle" >Contact Details</h3>
                        <div class="contact-section" style="display:none;" >

                            <hr/>
                            <?php $this->get_contact_section( 'Full Name:', 'full_name', $full_name, $full_name_size, $full_name_spacing ); ?>

                            <hr />					
                            <?php $this->get_contact_section( 'Slogan:', 'slogan', $slogan, $slogan_size, $slogan_spacing ); ?>

                            <hr />					
                            <?php $this->get_contact_section( 'Email:', 'email', $email, $email_size, $email_spacing ); ?>

                            <hr />					
                            <?php $this->get_contact_section( 'Phone:', 'phone', $phone, $phone_size, $phone_spacing ); ?>

                            <hr />					
                            <?php $this->get_contact_section( 'Alternate Website:', 'website', $website, $website_size, $website_spacing ); ?>
                            <hr />

                        </div>

                        <h3 class="jsjr-pci-toggle" >Social Media Icons</h3>
                        <div style="display:none;">
                            <label for="" ><?php _e( 'Enter a URL for the Desired Social Media Icons:' ); ?></label>
                            <a href="#" class="jsjr-pci-question" title="<?php _e( 'Enter the internet links (URL) for your social media websites below ( I.E. http://facebook.com/myfacebookpage )', $this->domain ) ?>" >?</a>
                            <?php 
                            foreach ( $this->social_icons as $fa_class => $icon ) {
                                $this->get_field_text( $args = array(
                                    'field_name'	=> $fa_class,
                                    'instance_value'	=> ${$fa_class},
                                    'description' 	=> $icon.':'
                                ));
                            } 

                            $this->get_field_select( $args = array(
                                'field_name'		=> 'jsjr_social_spacing',
                                'instance_value'	=> $jsjr_social_spacing,
                                'description' 		=> 'Space Below Social Icons:',
                                'value_range' 		=> range( 0, 200, 10 ),
                                'measurement_type'	=> 'px'
                            )); 
                            ?>								
                        </div>

                            <h3 class="jsjr-pci-toggle" >Misc Options</h3>
                            <div style="display:none;" >
                                <?php 

                                $this->get_field_checkbox( $args = array(
                                    'field_name'	=> 'fa_existing',
                                    'instance_value'	=> $fa_existing,
                                    'description' 	=> 'Do not load Font Awesome.',
                                    'return_value'	=> 'checked',
                                    'question'		=> 'Some themes may already load Font Awesome (used for the social media icons). To avoid Font Awesome from being loaded twice on your website, check this box. If you are unsure if your theme already uses Font Awesome or not, try checking this box and see if the social media icons on your widget disappear. If it does, simply uncheck the box again and save the changes.'
                                )); 

                                $this->get_field_select( $args = array(
                                    'field_name'	=> 'jsjr_widget_align',
                                    'instance_value'	=> $jsjr_widget_align,
                                    'description' 	=> 'Widget Alignment:',
                                    'value_range' 	=> 	array (
                                        'left'		=> 'Align Left',
                                        'center'	=> 'Align Center',
                                        'right'		=> 'Align Right'
                                    ),
                                    'measurement_type'	=> ''
                                )); 

                                ?>
                            </div>	

                    </div>

                    <?php
		}
		
		/**
		 * Used in Action hook in Constructor method to insert styles and scripts into the admin head
		 *
		 * @since 1.0
		 *
		 */	
		public function jsjr_pci_admin_styles_and_scripts( $hook ){
                    if ( 'widgets.php' == $hook ) {
                        wp_enqueue_media();
                        wp_enqueue_script( 'jquery-ui-tooltip' );
                        wp_enqueue_script( 'jsjr-pci-admin-scripts' , plugin_dir_url( __FILE__ ) . 'js/admin-scripts.js', array('jquery'), $this->ver , false );
                        wp_enqueue_style( 'jsjr-pci-admin-css' , plugin_dir_url( __FILE__ ) . 'css/admin-styles.css' , array() , $this->ver , false );				
                    }			
		}
		
                
		/**
		 * Used in Action hook in Constructor method to insert styles ans scripts into the wordpress head
		 *
		 * @since 1.0
		 *
		 */			
		public function jsjr_pci_wp_styles_and_scripts(){
                    $instance = $this->get_settings();
                    if( !empty ( $instance ) ) {
                        $test = $instance[ $this->number ][ 'fa_existing' ];
                        $hello = $test;
                        if ( $instance[ $this->number ][ 'fa_existing' ] !== 'checked' ) {
                            wp_enqueue_style( 'jsjr-pci-font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '4.3.0', false );
                        }
                    }

                    wp_enqueue_style( 'jsjr-pci-wp-css' , plugin_dir_url( __FILE__ ) . 'css/wp-styles.css' , array() , $this->ver , false );
		}
		
		
		/**
		 * Renders a text field
		 *
		 * @since 1.2
		 *
		 * @param 	array 	Takes an array with the following arguments:
		 *	 - field_name: input name
		 *	 - instance_value: instance variable containing old value
		 *	 - description: Text displayed in the label (instance variable)
		 */				
		public function get_field_text( $args ) {
                    extract( $args );
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id( $field_name ); ?>" ><?php _e( $description ); ?></label>
                        <input class="widefat" id="<?php echo $this->get_field_id( $field_name ); ?>" name="<?php echo $this->get_field_name( $field_name ); ?>" type="text" value="<?php _e( $instance_value, $this->domain ); ?>" />
                    </p>
                    <?php			
		}
                
                		/**
		 * Renders a image field
		 *
		 * @since 1.2
		 *
		 * @param 	array 	Takes an array with the following arguments:
		 *	 - field_name: input name
		 *	 - instance_value: instance variable containing old value
		 *	 - description: Text displayed in the label (instance variable)
		 */				
		public function get_field_image( $args ) {
                    extract( $args );
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id( $field_name ); ?>" ><?php _e( $description ); ?></label>
                        <input class="widefat" id="<?php echo $this->get_field_id( $field_name ); ?>" name="<?php echo $this->get_field_name( $field_name ); ?>" type="text" value="<?php _e( $instance_value, $this->domain ); ?>" />
                        <input type="button" name="submit" id="submit" class="button-primary upload-button" value="Select image" >
                    </p>
                    <?php			
		}


                /**
		 * Renders whether or not there are social links to display. if 
                 * there are links, it will save the links as a sting in the 
                 * social_icon_links class property.
		 *
		 * @since 1.2
		 *
		 */		
		public function is_social_links( $instance ) {
                    foreach ( $this->social_icons as $fa_class => $icon ) {
                        if ( $instance[ $fa_class ] !== '' ) {
                            return true;
                            break;
                        }
                    }
                    return false;
                }
                
                
		/**
		 * Renders a checkbox
		 *
		 * @since 1.2
		 *
		 * @param 	array 	Takes an array with the following arguments:
		 *	 - field_name: input name
		 *	 - instance_value: instance variable containing old value (instance variable)
		 *	 - description: Text displayed in the label
		 *	 - return_value: if the box is checked, this value will be sent when Widget is saved
		 *	 - question: If present, will display a help icon with help information (dependency: jquery ui tooltip)
		 */		
		public function get_field_checkbox( $args ) {
                    extract( $args );
                    ?>
                    <p>
                        <input id="<?php echo $this->get_field_id( $field_name ); ?>" name="<?php echo $this->get_field_name( $field_name ); ?>" type="checkbox" value="checked" <?php checked( $return_value, $instance_value ); ?> />
                        <label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php _e( $description , $this->domain ); ?></label>
                        <?php if ( isset( $question ) ) { ?>
                            <a href="#" class="jsjr-pci-question" title="<?php _e( $question, $this->domain ) ?>" >?</a>
                        <?php } ?>
                    </p>
                    <?php
		}
		
		
		/**
		 * Renders a select field
		 *
		 * @since 1.2
		 *
		 * @param 	array 	Takes an array with the following arguments:
		 *	 - field_name: input name
		 *	 - instance_value: instance variable containing old value (instance variable)
		 *	 - description: Text displayed in the label
		 *	 - value_range: can be a range of numbers (I.E. range( 0, 2, 0.1 )), or an associate array
		 *	 - measurement_type: Example 'em' or 'px' or ''
		 */	
		public function get_field_select( $args ) {
                    extract( $args );
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php _e( $description ); ?></label>
                        <select name="<?php echo $this->get_field_name( $field_name ); ?>" id="<?php echo $this->get_field_id( $field_name ); ?>">
                            <?php
                            if ( is_array( $value_range ) && ( $value_range !== array_values( $value_range ) ) ){
                                foreach ( $value_range as $key => $value ) {
                                    echo '<option value="' , $key . $measurement_type , '" ', selected( $instance_value, $key . $measurement_type ) , '>', __( $value . $measurement_type, $this->domain ) , '</option>';
                                }
                            } elseif ( is_array( $value_range ) ) {
                                foreach ( $value_range as $value ) {
                                    echo '<option value="' , $value . $measurement_type , '" ', selected( $instance_value, $value . $measurement_type ) , '>', __( $value . $measurement_type, $this->domain ) , '</option>';
                                }					
                            }
                            ?>
                        </select>
                    </p>
                    <?php
		}
		
                
		/**
		 * Renders a contact section 
		 *
		 * @since 1.2
		 *
		 * @param 	string	$description
		 * @param 	string	$field_name
		 * @param 	string	$field_name_text
		 * @param 	string	$field_name_size
		 * @param 	string	$field_name_spacing
		 */	
		public function get_contact_section( $description, $field_name, $field_name_text , $field_name_size, $field_name_spacing ) {

                    $this->get_field_text( $args = array(
                        'field_name'		=> $field_name,
                        'instance_value'	=> $field_name_text,
                        'description' 		=> $description
                    ));

                    $this->get_field_select( $args = array(
                        'field_name'		=> $field_name.'_size',
                        'instance_value'	=> $field_name_size,
                        'description' 		=> 'Font Size:',
                        'value_range' 		=> range( 5, 35 ),
                        'measurement_type'	=> 'px'
                    ));

                    $this->get_field_select( $args = array(
                        'field_name'		=> $field_name.'_spacing',
                        'instance_value'	=> $field_name_spacing,
                        'description' 		=> 'Spacing Below Text:',
                        'value_range' 		=> range( 0, 200, 10 ),
                        'measurement_type'	=> 'px'
                    ));
		}
                
		
	}
	add_action('widgets_init', create_function('', 'return register_widget("jsjr_personal_contact_info");'));