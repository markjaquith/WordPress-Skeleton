<?php
/**
 * @package MediaPress
 * 
 * Sitewide gallery single gallery home page
 * 
 */
?>
<?php 

$gallery = mpp_get_current_gallery();
if( mpp_user_can_upload( $gallery->component, $gallery->component_id ) ):?>
<div class="mpp-menu mpp-menu-open  mpp-menu-horizontal mpp-gallery-admin-menu">
	<?php mpp_gallery_admin_menu( mpp_get_current_gallery(), mpp_get_current_edit_action() );?>
</div>
<hr />
<?php endif; ?>
<div class="mpp-container mpp-clearfix mpp-sitewide-component" id="mpp-container">
	<div class="mpp-breadcrumbs"><?php mpp_gallery_breadcrumb();?></div>
	<?php 
	if( is_super_admin() ) {
		mpp_display_space_usage();
	}	
	?>
<?php
//main file loaded by MediaPress
//it loads the requested file

if ( mpp_is_gallery_create() ) {
	$template = 'gallery/create.php';
} elseif ( mpp_is_gallery_management() ) {
	$template = 'sitewide/gallery/manage.php';
} elseif ( mpp_is_media_management() ) {
	$template = 'sitewide/media/manage.php';
} elseif ( mpp_is_single_media() ) {
	$template = 'sitewide/media/single.php';
} elseif ( mpp_is_single_gallery() ) {
	$template = 'sitewide/gallery/single.php';
} elseif ( mpp_is_gallery_home() ) {
	$template = 'gallery/loop-gallery.php';
} else {
	$template = 'gallery/404.php';//not found
}

$template = apply_filters( 'mpp_get_sitewide_gallery_template', $template );

mpp_get_template( $template );
?>
<?php setup_postdata( mpp_get_current_gallery() );?> 
</div>  <!-- end of mpp-container -->