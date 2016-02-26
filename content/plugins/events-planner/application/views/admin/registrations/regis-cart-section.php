<div class="epl_cart_wrapper">

    <input name="event_id" type ="hidden" value ="<?php echo $event_id; ?>" />

    <?php

    echo epl_show_ad( 'Editable registrations, manual emails are available in PRO version.' );
    /*
     * This loop grabs each one of the forms and displays.
     */
    if ( is_array( $cart_data['cart_items'] ) ):
        foreach ( $cart_data['cart_items'] as $event ):
            ?>
            <div class="epl_cart_section">

                <div class="event_name"><h1><?php echo $event['title']; ?></h1></div>
                <div class="epl_event_section">


                    <p class="message"></p>
                    <div class="content epl_event_dates">


                        <?php echo $event['event_dates']; ?>
                    </div>

                </div>
                <div class="epl_event_section">


                    <div class="content">
                        <?php echo $event['event_time_and_prices']; ?>
                    </div>


                </div>


            </div>



            <?php

        endforeach;
    endif;
    ?>

</div>
