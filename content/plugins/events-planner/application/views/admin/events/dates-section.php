<div class="epl_mh_300 epl_ov_a" style="">
<?php echo epl_show_ad ('Control Registration Start and End times in the pro version.'); ?>
    <table class="epl_form_data_table" cellspacing ="0" id="epl_dates_table">
        <thead>
        <th></th>
        <th>Event Start Date</th>
        <th>Event End Date</th>
        <th>Regis. Starts On</th>

        <th>Regis. Ends On</th>
 
        <th>Capacity</th>
        <th></th>
        </thead>
        <tfoot>
            <tr>
                <td colspan="7" style="vertical-align: middle;">


                    <a href="#" class="add_table_row"><img src ="<?php echo EPL_FULL_URL ?>images/add.png" /></a>
                </td>
            </tr>


        </tfoot>
        <tbody class="events_planner_tbody">

            <?php foreach ( $date_fields as $row ) : ?>
                <tr>
                    <td>
                        <div class="handle"></div>
                    </td>
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



</div>
