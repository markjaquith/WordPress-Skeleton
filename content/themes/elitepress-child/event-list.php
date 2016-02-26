<?php

/**
 * Description: A Page Template for the Events Planner event list.
 *
 * @package WordPress
 * @subpackage Events Planner (wpeventsplanner.com)
 * @since Events Planner 1.1
 *
 * !!!! PLEASE DO NOT CHANGE THE FILE NAME !!!!
 */

/*
 * Since different Themes use different container names, you can modify the wrapper div ids and classes below with the appropriate name
 *
 */

global $event_list;
?>

<div id="event_list_wrapper">


    <?php

    /* custom event list loop */
    if ( $event_list->have_posts() ):

        while ( $event_list->have_posts() ) :

            $event_list->the_post();
            
            /*
             * after $event_list->the_post(), a global var called $event_details is created with all the event
             * meta information (dates, times, ...).  The template tags below go off of that variable.
             * You can uncomment the next line to see what is in the variable and echo out individuall pieces of information if you desire.
             * echo "<pre>" . print_r($event_details, true). "</pre>";
             */
            global $event_details;

            /*
             * As you can see, all the information is wrappeed in divs.  The styling comes from events-planner > css > events-planner-style1.css
             * You can copy the style into your theme and modify
             */

            //echo "THIS MESSAGE COMES FROM THE EVENT SUBPAGE TEMPLATE"

          /*
          * #########  BEGIN CUSTOMIZABLE SECTION ##########
          */
    ?>

        
			<div class="row">
				<div class="col-md-12">
					<h3><a href ="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
				</div>
			</div>
			<div class="row">
					
					<div class="col-md-9">
						
						<a href ="<?php echo get_permalink(); ?>" class="thumbnail"><?php the_post_thumbnail("large"); ?></a>
						
						<?php the_excerpt(); //the content from the event editor?>
							
					</div>


					<!--<div class="col-md-4">

						<?php
							//location id is stored in $event_details['_epl_event_location']
						?>
						<?php if($event_details['_epl_event_location']){ ?>
						<div class ="event_location">
							<p>Location:</p>
							<a href="<?php echo get_permalink( $event_details['_epl_event_location'] ); ?>" title="<?php echo get_the_location_name(); ?>">
								<?php echo get_the_location_name(); ?></a><br />

							<?php echo get_the_location_address(); ?><br />
							<?php echo get_the_location_city(); ?>, <?php echo get_the_location_state(); ?> <?php echo get_the_location_zip(); ?>
							<?php echo get_the_location_phone(); ?><br />
						</div>
						<?php } ?>	

					<?php
						//organization id is stored in $event_details['_epl_event_organization']
					?>
					<?php if($event_details['_epl_event_organization']){?>
						<div class ="event_organization">
								<span class="heading">Hosted By</span>
								<a href="<?php echo get_permalink( $event_details['_epl_event_organization'] ); ?>" title="<?php echo get_the_organization_name(); ?>"><?php echo get_the_organization_name(); ?></a><br />
							<?php echo get_the_organization_address(); ?><br />
							<?php echo get_the_organization_city(); ?>,  <?php echo get_the_organization_state(); ?> <?php echo get_the_organization_zip(); ?><br />
							<?php echo get_the_organization_phone(); ?><br />
							<?php echo epl_anchor( get_the_organization_website(), 'Visit Website' ); ?><br />
						</div>
					<?php } ?>
					<div class="row">
					  <div class="col-md-12">	
						
					  </div>	
					</div>	
					</div>
			</div>-->	
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="register_button_wrapper">	
					<a href="<?php echo get_permalink(); ?>" class="btn btn-default">Read More</a>
					<?php echo get_the_register_button(); ?>
				</div>
			</div>
		</div>
    <?php
          /*
          * #########  END CUSTOMIZABLE SECTION ##########
          */
                endwhile;
            else:
    ?>
                <div> Sorry, there are no events currently available</div>
    <?php endif; ?>




