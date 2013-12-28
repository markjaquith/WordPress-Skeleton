<?php get_header(); ?>

<section id="page-content">
	<div class="container">
		<article class="row">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php
				$args = array(
					'post_type' => 'attachment',
					'numberposts' => -1,
					'post_status' => null,
					'post_parent' => $post->ID,
					'orderby' => 'date',
					'order' => 'ASC'
				);
				$attachments = get_posts($args);
				$image_count = count($attachments);

			?>

			<div id="single-head" class="sixteen columns">
				<h1><?php the_title(); ?></h1>
				<?php if (has_excerpt()) { the_excerpt(); } ?>
			</div>

			<div class="twelve columns">

				<article id="post-<?php the_ID(); ?>" <?php post_class('entry group'); ?>>

				<?php if( get_post_meta($post->ID, 'ci_cpt_work_internal_slider', true) != 'disabled') : ?>
					<div class="row slider-gallery">
						<div id="slider-gallery" class="flexslider">
							<ul class="slides">
								<?php
								foreach ( $attachments as $attachment )
								{
									$ci_img_large = wp_get_attachment_image_src( $attachment->ID, 'large' );
									$ci_img = wp_get_attachment_image_src( $attachment->ID, 'ci_portfolio_slider' );
									echo '<li><a href="' . $ci_img_large[0] . '" class="fancybox" data-fancybox-group="p'.$post->ID.'"><img src="' . $ci_img[0] .'" /><div class="overlay2" style="display: none;"></div></a></li>';
								}
								?>
							</ul><!-- /slides -->
						</div><!-- /#room-gallery -->

						<?php if($image_count > 1): ?>
						<div id="slider-carousel" class="flexslider">
							<ul class="slides">
								<?php
								foreach ( $attachments as $attachment )
								{
									$ci_img = wp_get_attachment_image( $attachment->ID, 'ci_portfolio_slider' );
									echo '<li>' . $ci_img .'</li>';
								}
								?>
							</ul><!-- /slides -->
						</div>
						<?php endif; ?>

					</div><!-- /.room-gallery -->


				<?php else : //work slider not enabled ?>



				<ul class="job-slides row">
						<?php
						$count = '0';
						foreach ( $attachments as $attachment )	{
							if ( $count == '0' ) {
								$alt_text = trim(strip_tags( get_post_meta($attachment->ID, '_wp_attachment_image_alt', true) ));
								$attr = array(
									'alt'   => $alt_text,
									'title' => trim(strip_tags( $attachment->post_title )),
									'class' => 'scale-with-grid'
								);
								$img_attrs = wp_get_attachment_image_src( $attachment->ID, 'large' );
								echo '<li class="twelve columns alpha"><a href="'.$img_attrs[0].'" class="fancybox"
data-fancybox-group="p['.get_the_ID().']" title="'.esc_attr($alt_text).'">'.wp_get_attachment_image( $attachment->ID, 'ci_portfolio_list_big', false, $attr ).'</a></li>';

								$count++;
							}
							else
							{
								if ( $count%2 ) { $col_class = ' alpha'; } else { $col_class = ' omega'; }
								$alt_text = trim(strip_tags( get_post_meta($attachment->ID, '_wp_attachment_image_alt', true) ));
								$attr = array(
									'alt'   => $alt_text,
									'title' => trim(strip_tags( $attachment->post_title )),
									'class' => 'scale-with-grid'
								);
								$img_attrs = wp_get_attachment_image_src( $attachment->ID, 'large' );
								echo '<li class="six columns'.$col_class.'"><a href="'.$img_attrs[0].'" class="fancybox" data-fancybox-group="p['.get_the_ID().']" title="'.esc_attr($alt_text).'">'.wp_get_attachment_image( $attachment->ID, 'ci_portfolio_list_small', false, $attr ).'</a></li>';
								$count++;
							}

						}
						?>
					</ul>

			<?php endif; // if work slider enabled ?>
				</article>

			</div>

			<div id="sidebar" class="four columns">
				<h3 class="widget-title"><?php _e('About this Work', 'ci_theme'); ?></h3>
				<p><?php the_content(); ?></p>

					<?php
						$project_fields = get_post_meta(get_the_ID(), 'ci_cpt_project_info_fields', true);
						$work_url = get_post_meta(get_the_ID(), 'ci_cpt_work_url', true);
						$content_cols = 'ten';
						if( (!is_array($project_fields) or empty($project_fields[0]) or empty($project_fields[1]) ) and empty($work_url))
						{
							$content_cols = 'sixteen';
						}
					?>

					<?php if( ( is_array($project_fields) and !empty($project_fields[0]) and !empty($project_fields[1]) ) or !empty($work_url) ): ?>
					<div id="work-desc">
						<h3 class="widget-title"><?php _e('Work Info', 'ci_theme'); ?></h3>
						<ul class="work-credits">
							<?php if (is_array($project_fields) and count($project_fields) > 0 and !empty($project_fields[0]) and !empty($project_fields[1]) ) : ?>
							<?php for( $i = 0; $i < count($project_fields); $i+=2 ): ?>
								<li><b><?php echo htmlspecialchars($project_fields[$i]); ?></b>
									<?php echo $project_fields[$i+1]; ?></li>
								<?php endfor; ?>
							<?php endif; ?>
							<?php if (!empty($work_url)): ?>
							<li><a href="<?php echo $work_url; ?>" class="btn visit-btn"><?php _e("visit", 'ci_theme'); ?></a></li>
							<?php endif; ?>
						</ul>
					</div> <!-- #work-desc -->
				<?php endif; ?>
			</div> <!--#sidebar -->

			<?php endwhile; endif; ?>

			<?php if ( !ci_setting('disable_work_related') ) : ?>
			<?php
			// Related Work Query
			$term_list = array();
			$terms = get_the_terms(get_the_ID(), 'skill');
			if(is_array($terms))
			{
				foreach($terms as $term)
				{
					$term_list[] = $term->slug;
				}
			}

			$term_list = !empty($term_list) ? $term_list : array('');

			$args = array(
				'post_type' => 'work',
				'numberposts' => 4,
				'post_status' => 'publish',
				'post__not_in' => array(get_the_ID()),
				'orderby' => 'rand',
				'tax_query' => array(
					array(
						'taxonomy' => 'skill',
						'field' => 'slug',
						'terms' => $term_list
					)
				)
			);
			$related_posts = get_posts($args);
			?>
			<?php
				if ( count($related_posts) ) {
			?>
		<hr class="sixteen columns separator">

			<section class="related-items sixteen columns">
				<h3 class="widget-title"><?php _e("Related Items", 'ci_theme'); ?></h3>
				<?php
				foreach($related_posts as $rpost) {
					$attr = array(
						'title' => trim(strip_tags( $rpost->post_title )),
						'class' => 'scale-with-grid'
					);
					?>
					<article class="<?php echo ci_column_classes(4); ?> columns portfolio-item">

						<a href="<?php echo get_permalink($rpost->ID); ?>" title="<?php echo esc_attr(get_the_title($rpost->ID)); ?>">
							<?php echo get_the_post_thumbnail($rpost->ID, 'ci_portfolio_slider', $attr); ?>
							<div class="portfolio-desc">
								<a href="<?php echo get_permalink($rpost->ID); ?>" title="<?php echo esc_attr(get_the_title($rpost->ID)); ?>">
								<h3><?php echo esc_attr(get_the_title($rpost->ID)); ?></h3>
								</a>
							</div>
						</a>
					</article><!-- /portfolio-item -->

				<?php }
					}
				wp_reset_postdata();
				?>
			</section>
			<?php endif; ?>

		</article>
	</div> <!-- .container < #page-content -->
</section> <!-- #page-content -->

<?php get_footer(); ?>
