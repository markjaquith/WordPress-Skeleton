<?php

/* this is the totals TABLE in the cart */

?>
<table class="epl_totals_table">

    <tr class="epl_grand_total">

        <td class="epl_w200"><?php echo epl_e( 'Total' ); ?></td>
        <td class="epl_total_price epl_w100 epl_ta_r"> <?php echo epl_get_currency_symbol() . epl_get_formatted_curr( $money_totals['grand_total'] ); ?></td>
    </tr>


</table>