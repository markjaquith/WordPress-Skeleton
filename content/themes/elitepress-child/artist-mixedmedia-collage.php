<?php 
/* Template Name: artist mixed_mediacollage */ 
get_header();
?>
<div class="content-section">
	<div class="title-section">
	</div>
	<div class="container">
		<div class="col-md-12">
			<?php echo do_shortcode('[mpp-list-gallery 
component=groups component_id=6 view="grid"]'); ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>
