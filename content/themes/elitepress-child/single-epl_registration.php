<?php

/**
 * The Template for displaying a single registration
 *
 * @package WordPress
 * @subpackage Events Planner  www.wpeventsplanner.com
 */
get_header();
?>


<div id="primary">
    <div id="content">


        <div class="entry-content">
            <!-- COPY AND PASTE THE CONTENT BETWEEN THESE COMMENT BLOCKS INTO YOUR OWN single-epl_registration.php -->
            <h1 class="entry-title"><?php epl_e( "Registration Details" ); ?></h1>

            <div class="event_registration_wrapper">

                <?php echo get_the_registration_details(); ?>

            </div>
            <!-- END COPY AREA-->

        </div>





    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>