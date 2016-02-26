<?php
    //This view is used for the Payment Info meta box and
    //the ajax payment info modification
    //If called by ajax, a form wrapper is introduced, along with nonce, regis post_ID

    if ( isset( $save_button ) ): ?>
    <form class="epl_regis_payment_meta_box_form" action="#" method="post">
        <input type="hidden" name ="post_ID" value="<?php echo (int) $_POST['post_ID']; ?>" />
    <?php

        endif;

        wp_nonce_field( 'epl_form_nonce', '_epl_nonce' );
    ?>

    <table class="epl_form_data_table epl_regis_payment_meta_box" cellspacing="0">


        <?php
        //Print the fields
        echo current( $epl_regis_payment_fields );
        ?>
        <?php if ( isset( $save_button ) ): ?>

            <tr><td><input type="submit" name="Submit" value ="Save" class="epl_save_payment_ajax" /><td></tr>

<?php endif; ?>

        </table>
<?php if ( isset( $save_button ) ): ?>
        </form>

<?php endif; ?>

