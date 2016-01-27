<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * Used for Image field type
 */
class MPP_Admin_Settings_Field_Image extends MPP_Admin_Settings_Field {
    
    
    public function __construct( $field ) {
		
        parent::__construct( $field );
		
    }
    
    
    public function render( $args ) {
        wp_enqueue_media();
		wp_enqueue_script( 'mpp_settings_uploader' );
        $value = esc_attr( $args['value'] );//atgtachment url
        $size = $this->get_size();
        
        //we need to show this imaage
        if( $value ) {
           $image = "<img src='{$value}' />";
        }else {
			
            $image = "<img src='' />";
        }
        
        $id = $args['option_key'];
		
		?>

		<div class='settings-image-placeholder'>
			<?php 
				if( $value )
					$class = 'settings-image-action-visible';
               
                echo $image;
            ?>
			<br />
			<a href="#" class="delete-settings-image <?php echo $class;?>"><?php _e( 'Remove' );?></a> 
		</div>

		<?php
			echo  "<input type='hidden' class='hidden-image-url' id='{$id}' name='{$id}' value='{$value}'/>";
			echo  '<input type="button" class="button settings-upload-image-button" id="'. $id .'_button" value="Browse" data-id="'.$id.'" data-btn-title="Select" data-uploader-title="Select" />';
       
			echo '<span class="description">'. $this->get_desc() . '</span>';

        
    }
}
