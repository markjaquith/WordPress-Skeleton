<?php $current_options = get_option('elitepress_lite_options',theme_data_setup()); ?>
<!-- Service Section -->

<div class="service-section">
	<div class="container">
<?php
	if($current_options['service_section_enabled'] == 'on') {
	$service_one_title = $current_options['service_one_title'];
	$service_one_description = $current_options['service_one_description'];
	$service_two_title = $current_options['service_two_title'];
	$service_two_description = $current_options['service_two_description'];
	$service_three_title = $current_options['service_three_title'];
	$service_three_description = $current_options['service_three_description'];
	$service_four_title = $current_options['service_four_title'];
	$service_four_description = $current_options['service_four_description'];
?>	
<!-- Service Section -->
<div class="service-section">
	<div class="container">
		
		<!-- Section Title -->
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div class="section-info">
				<?php if($current_options['service_title']) { ?>
					<h3 class="section-title"><?php echo esc_html($current_options['service_title']); ?></h3>
					<?php } ?>
					<?php if($current_options['service_description']) { ?>
					<p class="section-description"><?php echo esc_html($current_options['service_description']); ?></p>
					<?php } ?>
				</div>
			</div>		
		</div>
		<!-- /Section Title -->
		
		<!-- Service Area -->
		<div class="row">
		
			<div class="col-md-6 col-sm-6">
				<div class="media service-area">
				<?php if($current_options['service_one_icon']) { ?>
					<div class="service-box"><i class="<?php echo $current_options['service_one_icon']; ?>"></i></div> <?php } ?>
					<div class="media-body">
					<?php if($service_one_title) { ?>
						<h4><a href="#"><?php echo esc_html($service_one_title); ?></a></h4>
						<?php } if($service_one_description) { ?>
						<p><?php echo esc_html($service_one_description); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
		
			<div class="col-md-6 col-sm-6">
				<div class="media service-area">
				<?php if($current_options['service_two_icon']) { ?>
					<div class="service-box"><i class="<?php echo $current_options['service_two_icon']; ?>"></i></div><?php } ?>
					<div class="media-body">
					<?php if($service_two_title) { ?>
						<h4><a href="#"><?php echo esc_html($service_two_title); ?></a></h4>
						<?php }
						if($service_two_description) { ?>
						<p><?php echo esc_html($service_two_description); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
			
			<div class="clearfix"></div>
			
			<div class="col-md-6 col-sm-6">
				<div class="media service-area">
				<?php if($current_options['service_three_icon']) { ?>
					<div class="service-box"><i class="<?php echo $current_options['service_three_icon']; ?>"></i></div> <?php }?>
					<div class="media-body">
						<?php if($service_three_title) { ?>
						<h4><?php echo esc_html($service_three_title); ?></h4>
						<?php }
						if($service_three_description) { ?>
						<p><?php echo esc_html($service_three_description); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
			
			<div class="col-md-6 col-sm-6">
				<div class="media service-area">
				<?php if($current_options['service_four_icon']) { ?>
					<div class="service-box"><i class="<?php echo $current_options['service_four_icon']; ?>"></i></div> <?php } ?>
					<div class="media-body">
						<?php if($service_four_title) { ?>
						<h4><?php echo esc_html($service_four_title); ?></h4>
						<?php }
						if($service_four_description) { ?>
						<p><?php echo esc_html($service_four_description); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>	
			
		</div>
		<!-- /Service Area -->

	</div>
</div>
<!-- /Service Section -->
<?php } ?>
</div>
</div>
<div class="clearfix"></div>		