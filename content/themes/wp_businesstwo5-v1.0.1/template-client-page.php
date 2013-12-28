<?php
/*
 * Template Name: Clients Page
 */
?>

<?php get_header(); ?>

<section id="page-content">
	<div class="container">
		<article class="row">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<div id="single-head" class="sixteen columns">
					<h1><?php the_title(); ?></h1>
					<?php if (has_excerpt()) { the_excerpt(); } ?>
				</div>
	
				<article id="post-<?php the_ID(); ?>" <?php post_class('entry group'); ?>>
					<div class="sixteen columns">
						<?php if ( ci_has_image_to_show() ) : ?>
							<figure class="entry-thumb <?php ci_e_setting('featured_single_align'); ?>">
								<a href="<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'large') ); ?>" class="fancybox">
									<?php ci_the_post_thumbnail_full(array( "class" => "scale-with-grid" ) ); ?>
								</a>
							</figure>
						<?php endif; ?>
	
						<?php ci_e_content(); ?>
	
						<div id="client-array">
							<?php $clients = new WP_Query(array(
								'post_type' => 'client',
								'posts_per_page' => -1
							));?>
							<?php
								$cols = ci_setting('client_home_cols');
								switch($cols){
									case 'four':
										$cols = 4;
										break;
									case 'one-third':
										$cols = 3;
										break;
									case 'eight':
									default:
										$cols = 2;
										break;
								}
							?>
							<?php ci_column_classes($cols, 16, true); ?>
							<?php while ( $clients->have_posts() ) : $clients->the_post(); ?>
								<article class="client-item <?php echo ci_column_classes($cols, 16); ?> columns">

									<?php $link = get_post_meta($post->ID, 'ci_cpt_link_url', true); ?>
									<?php if(!empty($link)) echo '<a href="'.$link.'">'; ?>
										<?php the_post_thumbnail('ci_portfolio_slider', array('class'=>'scale-with-grid client-thumb')); ?>
									<?php if(!empty($link)) echo '</a>'; ?>

									<span class="client-name"><?php the_title(); ?></span>

								</article>
							<?php endwhile; ?>
							<?php wp_reset_postdata(); ?>
						</div>
					</div>
				</article>

			<?php endwhile; endif; ?>
		</article>
	</div> <!-- .container -->
</section> <!-- #page-content -->

<?php get_footer(); ?>
