<?php
/**
 * The template for displaying the venue page
 *
 ***************** NOTICE: *****************
 *  Do not make changes to this file. Any changes made to this file
 * will be overwritten if the plug-in is updated.
 *
 * To overwrite this template with your own, make a copy of it (with the same name)
 * in your theme directory. See http://docs.wp-event-organiser.com/theme-integration for more information
 *
 * WordPress will automatically prioritise the template in your theme directory.
 ***************** NOTICE: *****************
 *
 * @package Event Organiser (plug-in)
 * @since 1.0.0
 */

//Call the template header
get_header(); ?>

<!-- This template follows the TwentyTwelve theme-->
<div id="primary" class="site-content">
	<div id="content" role="main">

	<!-- Page header, display venue title-->
	<header class="page-header">	
		
		<?php $venue_id = get_queried_object_id(); ?>
		
		<h1 class="page-title"><?php printf( __( 'Events at: %s', 'eventorganiser' ), '<span>' .eo_get_venue_name($venue_id). '</span>' );?></h1>

		<?php if( $venue_description = eo_get_venue_description( $venue_id ) ){
			 echo '<div class="venue-archive-meta">'.$venue_description.'</div>';
		} ?>
		
		<!-- Display the venue map. If you specify a class, ensure that class has height/width dimensions-->
		<?php echo eo_get_venue_map( $venue_id, array('width'=>"100%") ); ?>
	</header><!-- end header -->

	<?php if ( have_posts() ) : ?>

		<!-- Navigate between pages -->
		<!-- In TwentyEleven theme this is done by twentyeleven_content_nav -->
		<?php 
			if ( $wp_query->max_num_pages > 1 ) : ?>
				<nav id="nav-above">
					<div class="nav-next events-nav-newer"><?php next_posts_link( __( 'Later events <span class="meta-nav">&rarr;</span>' , 'eventorganiser' ) ); ?></div>
					<div class="nav-previous events-nav-newer"><?php previous_posts_link( __( ' <span class="meta-nav">&larr;</span> Newer events', 'eventorganiser' ) ); ?></div>
				</nav><!-- #nav-above -->
		<?php endif; ?>

		<!-- This is the usual loop, familiar in WordPress templates-->
		<?php while ( have_posts()) : the_post(); ?>
	
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">

				<h1 class="entry-title" style="display: inline;">
				<a href="<?php the_permalink(); ?>">
					<?php 
						//If it has one, display the thumbnail
						if( has_post_thumbnail() )
							the_post_thumbnail('thumbnail', array('style'=>'float:left;margin-right:20px;'));

						//Display the title
						the_title()
					;?>
				</a>
				</h1>
		
				<div class="event-entry-meta">

					<!-- Output the date of the occurrence-->
					<?php
					//Format date/time according to whether its an all day event.
					//Use microdata https://support.google.com/webmasters/bin/answer.py?hl=en&answer=176035
 					if( eo_is_all_day() ){
						$format = 'd F Y';
						$microformat = 'Y-m-d';
					}else{
						$format = 'd F Y '.get_option('time_format');
						$microformat = 'c';
					}?>
					<time itemprop="startDate" datetime="<?php eo_the_start($microformat); ?>"><?php eo_the_start($format); ?></time>

					<!-- Display event meta list -->
					<?php echo eo_get_event_meta_list(); ?>

					<!-- Show Event text as 'the_excerpt' or 'the_content' -->
					<?php the_excerpt(); ?>
			
				</div><!-- .event-entry-meta -->
		
				<div style="clear:both;"></div>
			</header><!-- .entry-header -->
			</article><!-- #post-<?php the_ID(); ?> -->

    		<?php endwhile; ?><!--The Loop ends-->

			<!-- Navigate between pages-->
			<?php 
			if ( $wp_query->max_num_pages > 1 ) : ?>
				<nav id="nav-below">
					<div class="nav-next events-nav-newer"><?php next_posts_link( __( 'Later events <span class="meta-nav">&rarr;</span>' , 'eventorganiser' ) ); ?></div>
					<div class="nav-previous events-nav-newer"><?php previous_posts_link( __( ' <span class="meta-nav">&larr;</span> Newer events', 'eventorganiser' ) ); ?></div>
				</nav><!-- #nav-below -->
			<?php endif; ?>


	<?php else : ?>
			<!-- If there are no events -->
			<article id="post-0" class="post no-results not-found">
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Nothing Found', 'eventorganiser' ); ?></h1>
				</header><!-- .entry-header -->
				<div class="entry-content">
					<p><?php _e( 'Apologies, but no events were found for the requested venue. ', 'eventorganiser' ); ?></p>
				</div><!-- .entry-content -->
			</article><!-- #post-0 -->
	<?php endif; ?>

	</div><!-- #content -->
</div><!-- #primary -->

<!-- Call template sidebar and footer -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
