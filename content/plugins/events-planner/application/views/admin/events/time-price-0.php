
<table class="epl_form_data_table  epl_w300" cellspacing ="0" id="event_times_table">
    <thead>
    <th></th>
    <th><?php epl_e('Start Time'); ?></th>
    <th><?php epl_e('End Time'); ?></th>
   
    <th></th>
</thead>
<tfoot>
    <tr>
        <td colspan="3">
            <a href="#" class="add_table_row"><img src ="<?php echo EPL_FULL_URL ?>images/add.png" /></a>
        </td>
    </tr>
</tfoot>
<tbody class="events_planner_tbody">

    <?php foreach ( $time_fields as $row ) : ?>

        <tr>
            <td><div class="handle"></div></td>
        <?php

        echo $row;
        ?>

        <td>

            <div class="epl_action epl_delete"></div>
        </td>

    </tr>

    <?php endforeach; ?>

    </tbody>


    </table>
    <div class="epl_box epl_warning epl_w400">
        <div class="epl_box_content">

        <?php epl_e( "At lease one price is required, even if it's a free event" ); ?>
    </div>
</div>

<table class="epl_form_data_table epl_w400" cellspacing ="0" id="epl_prices_table">
    <thead>
    <th></th>
    <th><?php epl_e('Price Type'); ?></th>
    <th>Price (<?php echo epl_get_currency_symbol(); ?>)</th>

    <th></th>
</thead>
<tfoot>
    <tr><td colspan ="8">
            <a href="#" class="add_table_row"><img src ="<?php echo EPL_FULL_URL ?>images/add.png" /></a>
        </td></tr>
</tfoot>
<tbody class="events_planner_tbody">
    <?php foreach ( $price_fields as $price_field_row ) : ?>

            <tr>
                <td><div class="handle"></div></td>
        <?php

            echo $price_field_row;
        ?>

            <td>
                <div class="epl_action epl_delete"></div>

            </td>

        </tr>
    <?php endforeach; ?>
</tbody>


</table>


<script>

    jQuery(document).ready(function($){

        $("table#event_times_table > tbody").sortable();

    });

</script>