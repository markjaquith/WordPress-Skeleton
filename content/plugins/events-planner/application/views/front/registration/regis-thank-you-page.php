<div class="epl_cart_wrapper epl_thank_you_page">
    <div class="section">
        <div class="thank_you_message epl_rounded_corners_5">
            <?php epl_e('Thank you.  Your registration is complete.'); ?>
        </div>
        <div class="event_name"><?php echo get_the_event_title(); ?></div>
        <div class="address_section">
            
            <div><strong>Regis. ID: <?php echo get_the_regis_id(); ?></strong></div>
            <?php echo get_the_location_name(); ?><br />
            <?php echo get_the_location_address(); ?> <?php echo get_the_location_address2(); ?><br />
            <?php echo get_the_location_city(); ?>, <?php echo get_the_location_state(); ?> <?php echo get_the_location_zip(); ?><br />
        </div>

        <div class="time_section">
           Tickets
            <?php echo get_the_regis_prices(); ?>
        </div>

        <div class="time_section">
            Time(s)
            <?php echo get_the_regis_times(); ?>
        </div>

        <div class="date_section">
            Date(s)
            <?php echo get_the_regis_dates(); ?>
        </div>


    </div>

        <?php if (isset($payment_details) && $payment_details != ''  && !epl_is_free_event())
                echo $payment_details;
            ?>
        <?php echo $regis_form; ?>
        


</div>