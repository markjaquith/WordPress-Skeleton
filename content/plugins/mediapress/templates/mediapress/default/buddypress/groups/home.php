<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Groups Component Gallery list(MediaPress landing page) template
 *  Used by /groups/group_name/mediapress/
 */
?>
<div role="navigation" id="subnav" class="item-list-tabs no-ajax mpp-group-nav">
	<ul>
		<?php do_action( 'mpp_group_nav' );?>
	</ul>
</div>
<div class="mpp-container mpp-clearfix" id="mpp-container">
	
	<div class="mpp-breadcrumbs mpp-clearfix"><?php mpp_gallery_breadcrumb();?></div>
<?php
	//main file loaded by MediaPress
	//it loads the requested file

	if ( mpp_is_gallery_create() ) {
		$template = 'gallery/create.php';

	} elseif ( mpp_is_gallery_management() ) {
		$template = 'buddypress/groups/gallery/manage.php';
	} elseif ( mpp_is_media_management() ) {
		$template = 'buddypress/groups/media/manage.php';
	} elseif ( mpp_is_single_media() ) {
		$template = 'buddypress/groups/media/single.php';
	} elseif ( mpp_is_single_gallery() ) {
		$template = 'buddypress/groups/gallery/single.php';
	} elseif ( mpp_is_gallery_home() ) {
		$template = 'gallery/loop-gallery.php';
	} else {
		$template = 'gallery/404.php';//not found
	}


	$template = apply_filters( 'mpp_get_groups_gallery_template', $template );

	mpp_get_template( $template );
?>
</div>  <!-- end of mpp-container -->