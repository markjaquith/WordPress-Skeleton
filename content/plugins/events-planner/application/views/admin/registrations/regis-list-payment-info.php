<div class="epl_regis_list_payment_info_wrapper_<?php echo $post_ID; ?>">
    <table class="epl_regis_list_payment_info <?php echo $status_class; ?>">

        <tr>

            <td><?php echo $snapshot_link; ?></td>
            <td><span class="small"><?php epl_e( 'Total' ); ?> </span><span  class="amount"><?php echo $grand_total; ?></span></td>
            <td><span class="small"><?php epl_e( 'Paid' ); ?> </span><span  class="amount"><?php echo $amount_paid; ?></span></td>

        </tr>
        <tr><td colspan="3"><?php echo $regis_status; ?> - <?php echo $payment_method; ?></td></tr>

    </table>
</div>