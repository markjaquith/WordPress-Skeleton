<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * @package mediapress
 * 
 * Single Gallery template
 * If you need specific template for various types, you can copy this file and create new files with name like
 * This comes as the fallback template in our template hierarchy
 * Before loading this file, MediaPress will search for 
 * single-{type}-{status}.php
 * single-{type}.php
 * and then fallback to
 * single.php
 * Where type=photo|video|audio|any active type
 *		 status =public|private|friendsonly|groupsonly|any registered status
 *  	
 * 
 * Please create your template if you need specific templates for photo, video etc
 * 
 * 
 *	
 * Fallback single Gallery View
 */
?>
<?php if( mpp_have_media() ): ?>

    <?php while( mpp_have_media() ): mpp_the_media(); ?>

        <?php if( mpp_user_can_view_media( mpp_get_media_id() ) ) :?>

			<div class="<?php mpp_media_class( );?>" id="mpp-media-<?php mpp_media_id();?>">
					
					<?php do_action( 'mpp_before_single_media_item' ); ?>
				
					<div class="mpp-item-meta mpp-media-meta mpp-media-meta-top">
						<?php do_action( 'mpp_media_meta_top' );?>
					</div>
				
					<div class="mpp-item-title mpp-media-title"> <?php mpp_media_title() ;?></div>
					
					<?php do_action( 'mpp_after_single_media_title' ); ?>
					
					<div class="mpp-item-entry mpp-media-entry" >
						
						<?php do_action( 'mpp_before_single_media_content' );?>
						
						<?php mpp_load_media_view( mpp_get_media() );?>
						
								
						<?php do_action( 'mpp_after_single_media_content' );?>
						
					</div>
					
					<div class="mpp-item-meta mpp-media-meta mpp-media-meta-bottom">
						<?php do_action( 'mpp_media_meta' );?>
					</div>
					
					<?php if ( mpp_show_media_description() ): ?>

						<div class="mpp-item-description mpp-media-description mpp-single-media-description mpp-media-<?php mpp_media_type(); ?>-description mpp-clearfix">
								<?php mpp_media_description(); ?>
						</div>

					<?php endif; ?>
					
				<?php do_action( 'mpp_after_single_media_item' ); ?>
					
            </div>

        <?php else:?>

            <div class="mpp-notice mpp-gallery-prohibited">

                <p><?php printf( __( 'The privacy policy does not allow you to view this.', 'mediapress' ) ); ?></p>
            </div>

        <?php endif;?>

    <?php endwhile; ?>
	
	<?php  mpp_previous_media_link();?>
    <?php  mpp_next_media_link();?>
   

	<?php mpp_locate_template( array('buddypress/members/media/activity.php'), true ); ?>

<?php else:?>

<div class="mpp-notice mpp-no-gallery-notice">
    <p> <?php _ex( 'There is nothing to see here!', 'No media message', 'mediapress' ); ?> 
</div>

<?php endif;?>