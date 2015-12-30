<?php

if ( isset( $epl_pay_types ) ) {
    echo $epl_pay_types['label'];
    echo $epl_pay_types['field'];
}
?>

<div id="epl_pay_profile_fields_wrapper" class="meta_box_content rounded_corners">
    <?php if ( isset( $epl_pay_profile_fields ) ): ?>
    <?php echo current( ( array ) $epl_pay_profile_fields ); ?>
<?php endif; ?>
    </div>

<?php echo epl_show_ad( 'Would you like to get access to more payment options, gateways, and use filters to add fields in this section?' ); ?>
