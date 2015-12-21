<?php

/**
 * The Template for displaying a single location
 *
 * @package WordPress
 * @subpackage Events Planner  www.wpeventsplanner.com
 */
get_header();


the_post(); //required
the_location_details(); //puts the location details in a global variable $location_details
global $location_details; //This is an array with all the information from the db
?>


<div id="primary">
    <div id="content">

        <?php

//You can uncomment this line to see what is in $location_details
//echo "<pre class='prettyprint'>LOCATION DETAILS " . print_r( $location_details, true ) . "</pre>";
        ?>


        <h1 class="entry-title"><?php the_title(); ?></h1>
        <div class="entry-content">



            <?php the_content(); ?>




            <div class="event_location_wrapper">

                <div style="width:250px;margin:0 auto;text-align: center;">


                    <div class ="event_location">

                        <h2><?php echo get_the_location_name(); ?></h2>

                        <?php echo get_the_location_address(); ?><br />
                        <?php echo get_the_location_city(); ?>, <?php echo get_the_location_state(); ?> <?php echo get_the_location_zip(); ?>
                        <?php echo get_the_location_phone(); ?><br />
                    </div>

                </div>


            </div>


        </div>





    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>