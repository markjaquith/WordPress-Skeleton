<div class="section epl_regis_field_wrapper regis_form">


    <div class="header">
    

        <!-- form label -->
    <?php if ( isset( $form_label ) && $form_label != '' ): ?>

            <h1><?php echo $form_label; ?></h1>

    <?php endif; ?>
            <!-- selected ticket name -->
    <?php if ( isset( $ticket_number ) && $ticket_number !='' ): ?>

        <h2><?php echo epl__( 'Attendee #' ) . $ticket_number; ?>: <?php echo $price_name; ?></h2>

    <?php endif; ?>

            <!-- form description -->
    <?php if ( isset( $form_descr ) && $form_descr != ''): ?>

                <p><?php echo $form_descr; ?></p>

    <?php endif; ?>
    </div>
                <!-- registration form -->
                <fieldset class="epl_fieldset">

        <?php echo $fields; ?>


    </fieldset>
</div>