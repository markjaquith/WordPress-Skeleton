<?php

/**
 * The Template for displaying a single event
 *
 * @package WordPress
 * @subpackage Events Planner
 */
get_header();
?>

<div id="primary">
    <div class="container" id="content" role="main">
        <?php the_post(); ?>
        <div class="row">
			<div class="col-md-12">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</div>
		</div>
		<div class="row"><!-- start of row -->
			
		<div class="event_wrapper clearfix">

		  
			<div class="col-md-6 col-sm-12 col-xs-12">
             <div>

                <div class="event_description clearfix">

                    <?php the_content(); //the content from the event editor?>

                </div>

                <div class ="event_dates">
                    <p class="">Dates</p>
                    <?php echo get_the_event_dates( ); ?>

                </div>
                <div class ="event_times">
                    <p class="">Times</p>
                    <?php echo get_the_event_times( ); ?>
                </div>

                <div class ="event_prices" style="clear:both;">
                    <p class="">Ticket Prices</p>
                    <?php echo get_the_event_prices( ); ?>
                </div>
				<div class ="register_button_wrapper pull-left" style="clear:both;">
					<?php echo get_the_register_button(); ?>
                </div>
            </div>
			</div>
			
			<div class="col-md-6 col-sm-12 col-xs-12">
			<?php if($event_details['_epl_event_location'] || $event_details['_epl_event_organization']) { ?>	
            <div class="col_right"><!--start of col_right-->

                <?php

                    //location id is stored in $event_details['_epl_event_location']
                ?>
					<?php if($event_details['_epl_event_location']) { ?>
                    <div class ="event_location">
                        <p>Location</p>
                        <a href="<?php echo get_permalink( $event_details['_epl_event_location'] ); ?>" title="<?php echo get_the_location_name(); ?>">
                        <?php echo get_the_location_name(); ?>
                    </a><br />

                    <?php echo get_the_location_address(); ?><br />
                    <?php echo get_the_location_city(); ?>, <?php echo get_the_location_state(); ?> <?php echo get_the_location_zip(); ?>
                    <?php echo get_the_location_phone(); ?><br />
                    </div>
					<?php } ?>

                <?php

                        //organization id is stored in $event_details['_epl_event_organization']
                ?>
                        <?php if($event_details['_epl_event_organization']) { ?>
						<div class ="event_organization">
                            <span class="heading">Hosted By</span>
                            <a href="<?php echo get_permalink( $event_details['_epl_event_organization'] ); ?>" title="<?php echo get_the_organization_name(); ?>"><?php echo get_the_organization_name(); ?></a><br />
                    <?php echo get_the_organization_address(); ?><br />
                    <?php echo get_the_organization_city(); ?>  <?php echo get_the_organization_state(); ?> <?php echo get_the_organization_zip(); ?><br />
                        <?php echo get_the_organization_phone(); ?><br />
                        
                    </div>
					<?php } ?>	
                </div><!-- End of col_right-->
				
				<?php }else { ?>
					<div class="col-right">
						<div class ="event_location">
							<p class="heading">Location:</p>
							
							The Arches Project
							<br />
							Addereley Street<br/>
							Digbeth<br/>
							Birmingham B9 4EP<br/>
							0121 772 0852<br/>
						</div>
						<div class ="event_organization">
                            <p class="heading">Hosted By:</p>
                             The Arches Project<br />    
						</div>
					</div>	
					<?php } ?> 
					</div>
                </div><!-- End of event wrapper-->
				</div><!-- End of row-->	
            </div><!-- #content -->
        </div><!-- #primary -->

<?php get_footer(); ?>