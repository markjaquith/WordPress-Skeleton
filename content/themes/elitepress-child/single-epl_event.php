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
    <div id="content" role="main">


        <?php the_post(); ?>
        <h1 class="entry-title"><?php the_title(); ?></h1>

        <div class="event_wrapper clearfix">


            <div class="col_left">

                <div class="event_description clearfix">

                    <?php the_content(); //the content from the event editor?>

                </div>

                <div class ="event_dates">
                    <span class="">Dates</span>
                    <?php echo get_the_event_dates( ); ?>

                </div>
                <div class ="event_times">
                    <span class="">Times</span>
                    <?php echo get_the_event_times( ); ?>
                </div>

                <div class ="event_prices" style="clear:both;">
                    <span class="">Ticket Prices</span>
                    <?php echo get_the_event_prices( ); ?>
                </div>
            </div>


            <div class="col_right">

                <?php

                    //location id is stored in $event_details['_epl_event_location']
                ?>
                    <div class ="event_location">
                        <span class="heading">Location</span>
                        <a href="<?php echo get_permalink( $event_details['_epl_event_location'] ); ?>" title="<?php echo get_the_location_name(); ?>">
                        <?php echo get_the_location_name(); ?>
                    </a><br />

                    <?php echo get_the_location_address(); ?><br />
                    <?php echo get_the_location_city(); ?>, <?php echo get_the_location_state(); ?> <?php echo get_the_location_zip(); ?>
                    <?php echo get_the_location_phone(); ?><br />
                    </div>


                <?php

                        //organization id is stored in $event_details['_epl_event_organization']
                ?>
                        <div class ="event_organization">
                            <span class="heading">Hosted By</span>
                            <a href="<?php echo get_permalink( $event_details['_epl_event_organization'] ); ?>" title="<?php echo get_the_organization_name(); ?>"><?php echo get_the_organization_name(); ?></a><br />
                    <?php echo get_the_organization_address(); ?><br />
                    <?php echo get_the_organization_city(); ?>,  <?php echo get_the_organization_state(); ?> <?php echo get_the_organization_zip(); ?><br />
                        <?php echo get_the_organization_phone(); ?><br />
                        <?php echo epl_anchor( get_the_organization_website(), 'Visit Website' ); ?><br />
                    </div>

                </div>

                <div class ="register_button_wrapper" style="clear:both;">

<?php echo get_the_register_button(); ?>
                    </div>
                </div>

            </div><!-- #content -->
        </div><!-- #primary -->

<?php get_footer(); ?>