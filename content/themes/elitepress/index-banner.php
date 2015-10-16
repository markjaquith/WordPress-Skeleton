<!-- Page Title Section -->
<?php if (get_post_meta( get_the_ID(), 'banner_chkbx', true )) { ?>
<div class="page-title-section">		
	<div class="overlay">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="page-title">
					<?php if (get_post_meta( get_the_ID(), 'banner_title', true )) { ?>
						<h1><?php echo (get_post_meta (get_the_ID(),'banner_title', true)); ?></h1>
						<div class="page-title-seprator"></div>
					<?php } else { ?>
					<h1><?php the_title(); ?></h1>	
					<?php } ?>
					<?php if (get_post_meta( get_the_ID(), 'banner_description', true )) { ?>
						<p><?php echo (get_post_meta( get_the_ID(), 'banner_description', true )); ?></p>
						<?php } else { ?>
						<p><?php _e('Autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et dolore feugait.', 'elitepress'); ?></p>
						<?php }  ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<div class="clearfix"></div>
<!-- /Page Title Section -->