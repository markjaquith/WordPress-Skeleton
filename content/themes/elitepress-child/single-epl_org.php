<?php

/**
 * The Template for displaying a single organization
 *
 * @package WordPress
 * @subpackage Events Planner  www.wpeventsplanner.com
 */
get_header();

the_post(); //required
the_organization_details(); //puts the location details in a global variable $organization_details
global $organization_details; //This is an array with all the information from the db
?>

<div id="primary">
    <div id="content">


        <?php

//You can uncomment this line to see what is in $organization_details
//echo "<pre>ORGANIZATION DETAILS " . print_r( $organization_details, true ) . "</pre>";
        ?>


        <h1 class="entry-title"><?php the_title(); ?></h1>
        <div class="entry-content">



            <?php the_content(); ?>




            <div class="event_organization_wrapper">



                <div style="width:250px;margin:0 auto;text-align: center;">

                    <div class ="event_organization">

                        <h2><?php echo get_the_organization_name(); ?></h2>
                        <?php echo get_the_organization_address(); ?><br />
                        <?php echo get_the_organization_city(); ?>,  <?php echo get_the_organization_state(); ?> <?php echo get_the_organization_zip(); ?><br />
                        <?php echo get_the_organization_phone(); ?><br />
                        <?php echo epl_anchor( get_the_organization_website(), 'Visit Website' ); ?><br />
                    </div>

                </div>

            </div>


        </div>





    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>