<?php

//THESE TWO LINES ARE REQUIRED IN ALL EVENT LIST TEMPLATES

the_event_list();
global $event_list;
?>

<div id="event_list_wrapper">


    <?php
    global $event_details;
    /* custom event list loop */
    if ( $event_list->have_posts() ):

        while ( $event_list->have_posts() ) :

            $event_list->the_post();

            /*
             * after $event_list->the_post(), a global var called $event_details is created with all the event
             * meta information (dates, times, ...).  The template tags below go off of that variable.  You can uncomment the next line to see what is in the variable
             */
            

            /*
             * As you can see, all the information is wrappeed in divs.  The styling comes from events-planner > css > events-planner-style1.css
             * You can copy the style into your theme and modify
             */
            ?>
            <!-- individual event wrapper -->
            <div class="event_wrapper clearfix">


                <div class="col_left">

                    <div class="event_title clearfix">

                        <a href ="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>


                    </div>


                    <div class="event_description clearfix">

                        <?php the_content(); //the content from the event editor?>

                    </div>

                    <div class ="event_dates">
                        <span class="">Dates</span>
                        <?php echo get_the_event_dates(); ?>

                    </div>


                    <div class ="event_times">
                        <span class="">Times</span>
                        <?php echo get_the_event_times(); ?>
                    </div>

                    <div class ="event_prices" >
                        <span class="">Prices</span>
                        <?php echo get_the_event_prices(); ?>
                    </div>
                </div>


                <div class="col_right">

                    <?php

                    //location id is stored in $event_details['_epl_event_location']

                    if ( $event_details['_epl_event_location'] != '' ):
                        ?>

                        <div class ="event_location">
                            <span class="heading">Location</span>
                            <a href="<?php echo get_permalink( $event_details['_epl_event_location'] ); ?>" title="<?php echo get_the_location_name(); ?>">
                                <?php echo get_the_location_name(); ?>
                            </a><br />

                            <?php echo get_the_location_address(); ?><br />
                            <?php echo get_the_location_city(); ?>, <?php echo get_the_location_state(); ?> <?php echo get_the_location_zip(); ?><br />
                            <?php echo get_the_location_phone(); ?><br />
                        </div>


                        <?php

                    endif;
                    //organization id is stored in $event_details['_epl_event_organization']

                    if ( epl_nz( epl_get_event_property( '_epl_display_org_info' ), 10 ) != 0 ):
                        ?>

                        <div class ="event_organization">
                            <span class="heading">Hosted By</span>
                            <a href="<?php echo get_permalink( $event_details['_epl_event_organization'] ); ?>" title="<?php echo get_the_organization_name(); ?>"><?php echo get_the_organization_name(); ?></a><br />
                            <?php echo get_the_organization_address(); ?><br />
                            <?php echo get_the_organization_city(); ?>,  <?php echo get_the_organization_state(); ?> <?php echo get_the_organization_zip(); ?><br />
                            <?php echo get_the_organization_phone(); ?><br />
                            <?php echo epl_anchor( get_the_organization_website(), 'Visit Website' ); ?><br />
                        </div>

                    <?php endif; ?>

                </div>

                <div class ="register_button_wrapper" style="clear:both;">

                    <?php echo get_the_register_button(); ?>
                </div>
            </div>
            <?php

        endwhile;
    else:
        ?>
        <div> Sorry, there are no events currently available</div>
    <?php endif; ?>

</div>