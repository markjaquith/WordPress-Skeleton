<?php
/* Template Name: contact us */
get_header();
?>
<div class="content-section">
	<div class="container">
		<div class="title-section">
			<div class="row">
				<div class="col-md-12">
					<h1>Contact Us</h1>
				</div>
			</div>
		</div>
			<div class="row">
				<div class="col-md-6">
					<?php the_content(); ?>
				</div>
				<div class="col-md-5 col-md-offset-1">
					<address>
						The Arches Project
						</br>
						Adderley Street
						</br>
						Digbeth
						</br>
						Birmingham
						</br>
						B9 4EE
					</address>
					
						Telephone:
						0121 772 0852
						</br>
						Mobile:
						07932 418 359
						</br>
						Email:
						enquiries@thearchesproject.org
						<hr>
					<?php echo do_shortcode('[wpgmza id="1"]'); ?>		
				</div>
			</div>
  </div>
</div>
<?php get_footer(); ?>
