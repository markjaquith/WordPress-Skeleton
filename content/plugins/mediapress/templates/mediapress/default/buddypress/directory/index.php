<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php do_action( 'bp_before_directory_mediapress_page' ); ?>

<div id="buddypress" class="mpp-directory-contents">

	<?php do_action( 'bp_before_directory_mediapress_items' ); ?>

	

	<div id="mpp-dir-search" class="dir-search" role="search">
		<?php mpp_directory_gallery_search_form(); ?>
	</div><!-- #mpp-dir-search -->

	<?php do_action( 'mpp_before_directory_gallery_tabs' ); ?>

	<form action="" method="post" id="mpp-directory-form" class="dir-form">

		<div class="item-list-tabs" role="navigation">
			<ul>
				<li class="selected" id="mpp-all">
					<a href="<?php echo get_permalink( buddypress()->pages->mediapress->id ); ?>"><?php printf( __( 'All Galleries <span>%s</span>', 'mediapress' ), mpp_get_total_gallery_count() ) ?></a>
				</li>

                <?php do_action( 'mpp_directory_types' ) ?>

				<li id="mpp-order-select" class="last filter">

					<?php _e( 'Filter By:', 'mediapress' ) ?>
					<select>
						<option value=""><?php _e( 'All Galleries', 'mediapress' ) ?></option>
						<?php $active_types = mpp_get_active_types(); ?>
						<?php foreach( $active_types as $type => $type_object ):?>
							
							<option value="<?php echo $type;?>"><?php echo $type_object->get_label();?> </option>
														
						<?php endforeach;?>	
							
						<?php do_action( 'mpp_gallery_directory_order_options' ) ?>
					</select>
				</li>
					
			</ul>

			
		</div><!-- .item-list-tabs -->

		<div id="mpp-dir-list" class="mpp mpp-dir-list dir-list">
			<?php
				mpp_get_template( 'gallery/loop-gallery.php' );
			?>
		</div><!-- #mpp-dir-list -->

		<?php do_action( 'mpp_directory_gallery_content' ); ?>

		<?php wp_nonce_field( 'directory_mpp', '_wpnonce-mpp-filter' ); ?>

		<?php do_action( 'mpp_after_directory_gallery_content' ); ?>

	</form><!-- #mpp-directory-form -->

	<?php do_action( 'mpp_after_directory_gallery' ); ?>

</div><!-- #buddypress -->

<?php do_action( 'mpp_after_directory_gallery_page' ); ?>
