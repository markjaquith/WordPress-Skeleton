<div id="" class="epl_event_section" style="padding: 10px;">

    <?php if ( isset( $ticket_number ) ): ?>

        <b><?php echo epl__( 'Attendee #' ) . $ticket_number; ?>: <?php echo $price_name; ?></b>

    <?php endif; ?>
    <?php if ( isset( $form_label ) && $form_label !='' ): ?>

            <b><?php echo $form_label; ?></b>

    <?php endif; ?>

    <?php if ( isset( $form_descr ) && $form_descr !=''): ?>

                <p><?php echo $form_descr; ?></p>

    <?php endif; ?>

   <fieldset class="">
<table class="epl_form_data_table" cellspacing="0">
        <?php echo $fields; ?>

</table>
    </fieldset>
</div>
