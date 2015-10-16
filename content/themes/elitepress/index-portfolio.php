<?php $current_options = get_option('elitepress_lite_options',theme_data_setup()); 

if($current_options['portfolio_section_enabled'] == 'on') { ?>
<!-- Portfolio Section -->
<div class="portfolio-section">
	<div class="container">
	
		<!-- Section Title -->
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div class="section-info">
				<?php if($current_options['front_portfolio_title']) { ?>
				<h3 class="section-title"><?php echo esc_html($current_options['front_portfolio_title']); ?></h3>
				<?php }
				if($current_options['front_portfolio_description']) { ?>
				<p class="section-description"><?php echo esc_html($current_options['front_portfolio_description']); ?></p>
				<?php } ?>	
				</div>
			</div>		
		</div>
		<!-- /Section Title -->
		
		<!-- Portfolio Area -->
		<div class="row">

			<div class="col-md-4 col-sm-6 portfolio-area">
				<div class="portfolio_image">
				<?php if($current_options['portfolio_one_image']) { ?>
					<img class="img-responsive" src="<?php echo esc_url($current_options['portfolio_one_image']); ?>">
					<?php } ?>
					<div class="home_portfolio_showcase_overlay">
						<div class="home_portfolio_showcase_overlay_inner">
							<div class="home_portfolio_showcase_icons">
							</div>
						</div>
					</div>
				</div>
				<div class="home_portfolio_caption">
				<?php if($current_options['portfolio_one_title']){ ?>
					<h4><?php echo esc_html($current_options['portfolio_one_title']); ?></h4>
					<?php } ?>
					<?php if($current_options['portfolio_one_description']){ ?>
					<p><?php echo esc_html($current_options['portfolio_one_description']);?></p>
					<?php } ?>
					
				</div>
			</div>
			
			<div class="col-md-4 col-sm-6 portfolio-area">
				<div class="portfolio_image">
					<?php if($current_options['portfolio_two_image']) { ?>
					<img class="img-responsive" src="<?php echo esc_url($current_options['portfolio_two_image']); ?>">
					<?php } ?>
					<div class="home_portfolio_showcase_overlay">
						<div class="home_portfolio_showcase_overlay_inner">
							<div class="home_portfolio_showcase_icons">
							</div>
						</div>
					</div>
				</div>
				<div class="home_portfolio_caption">
				<?php if($current_options['portfolio_two_title']){ ?>
					<h4><?php echo esc_html($current_options['portfolio_two_title']); ?></h4>
					<?php } ?>
					<?php if($current_options['portfolio_two_description']){ ?>
					<p><?php echo esc_html($current_options['portfolio_two_description']);?></p>
					<?php } ?>
					
				</div>
			</div>
			
			<div class="col-md-4 col-sm-6 portfolio-area">
				<div class="portfolio_image">
					<?php if($current_options['portfolio_three_image']) { ?>
					<img class="img-responsive" src="<?php echo esc_url($current_options['portfolio_three_image']); ?>">
					<?php } ?>
					<div class="home_portfolio_showcase_overlay">
						<div class="home_portfolio_showcase_overlay_inner">
							<div class="home_portfolio_showcase_icons">
							</div>
						</div>
					</div>
				</div>
				<div class="home_portfolio_caption">
				<?php if($current_options['portfolio_three_title']){ ?>
					<h4><?php echo esc_html($current_options['portfolio_three_title']); ?></h4>
					<?php } ?>
					<?php if($current_options['portfolio_three_description']){ ?>
					<p><?php echo esc_html($current_options['portfolio_three_description']);?></p>
					<?php } ?>
					
				</div>
			</div>
			
			<div class="clearfix"></div>
		</div>
		<div class="row">
			<div class="col-md-12"></div>	
		</div>
		<!-- /Portfolio Area -->
				
	</div>
</div>
<!-- /Portfolio Section -->
<?php } ?>
<div class="clearfix"></div>