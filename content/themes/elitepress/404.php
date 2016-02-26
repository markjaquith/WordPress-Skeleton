<?php get_header(); ?>
<!-- HC Page Header Section -->
<?php get_template_part('banner','header'); ?>
<!-- /HC Page Header Section -->
<!-- 404 Error Section -->
<div class="error-section">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="error_404">
					<div class="text-center"><i class="fa fa-bug"></i></div>
				<h1><?php _e('404','elitepress'); ?></h1>
				<h4><?php _e('Oops! Page not found','elitepress'); ?></h4>
				<p><?php _e('We`re sorry, but the page you are looking for doesn`t exist.','elitepress'); ?></p>
				<div class="project-btn-div"><a href="<?php echo esc_html(site_url());?>" class="project-btn"><?php _e('Go Back','elitepress'); ?></a></div>
				</div>
		</div>
	</div>
</div>
</div>
<?php get_footer(); ?>
<!-- /404 Error Section -->