<?php
/*
Template Name: Contact Page
*/
?>

<?php get_header(); ?>

<section id="page-content">
	<div class="container">
		<article class="row">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<div id="single-head" class="sixteen columns">
					<h1><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) { the_excerpt(); } ?>
				</div>

				<div class="sixteen columns">

					<article id="post-<?php the_ID(); ?>" <?php post_class('entry group'); ?>>
						<?php if ( !ci_setting('disable_map') ): ?>
						<div id="map"></div>
						<?php endif; ?>
	
						<div class="ten columns alpha">
							<?php ci_e_content(); ?>
						</div>
	
						<div id="work-desc" class="six columns omega">
							<ul class="work-credits contact-details">
								<?php if ( ci_setting('contact_addr') ) : ?>
									<li><b><?php _e('Address', 'ci_theme'); ?></b> <?php ci_e_setting('contact_addr'); ?></li>
								<?php endif; ?>
	
								<?php if ( ci_setting('contact_email') ) : ?>
									<li><b><?php _e('Email', 'ci_theme'); ?></b> <?php ci_e_setting('contact_email'); ?></li>
								<?php endif; ?>
	
								<?php if ( ci_setting('contact_tel') ) : ?>
									<li><b><?php _e('Phone', 'ci_theme'); ?></b> <?php ci_e_setting('contact_tel'); ?></li>
								<?php endif; ?>
	
							</ul>
						</div> <!-- #work-desc -->
					</article>
				</div>
			<?php endwhile; endif; ?>
		</article>
	</div> <!-- .container -->
</section> <!-- #page-content -->

<?php get_footer(); ?>
