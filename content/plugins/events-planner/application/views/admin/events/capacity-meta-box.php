<?php
    /*
     * two ways to get the information passed to this view:
     * epl_get_the_field($field, $fields);
     * epl_get_the_label($field, $fields);
     * epl_get_the_desc($field, $fields);
     *
     * or directly from the array
     * 
     * $_f['_epl_event_available_space_display']['field'];
     * $_f['_epl_event_available_space_display']['label'];
     * $_f['_epl_event_available_space_display']['description'];
     */

 ?>
<div id="capacity_section">
    <?php echo epl_show_ad ('More options available in PRO.'); ?>
    <table class="epl_form_data_table" cellspacing ="0" id ="">

        <tr>
            <td><?php echo $_f['_epl_event_capacity_per']['label']; ?></td>
            <td><?php echo $_f['_epl_event_capacity_per']['field']; ?>
                
            <small><?php echo epl_get_the_desc('_epl_event_capacity_per', $_f);?></small>
            </td>
        </tr>
        <tr>
            <td><?php echo $_f['_epl_event_available_space_display']['label']; ?></td>
            <td><?php echo $_f['_epl_event_available_space_display']['field']; ?>
            </td>
        </tr>

    </table>

    <div class="epl_box epl_info">
        <div class="epl_box_content">

            This section gives you the option of allowing additional attendees and
            limiting their numbers for the event, date, time, or price.

        </div>
    </div>

    <table class="epl_form_data_table" cellspacing ="0" id ="">
        <tr>
            <td>Min</td>
            <td> <?php echo $_f['_epl_min_attendee_per_regis']['field']; ?>
                <small><?php echo $_f['_epl_min_attendee_per_regis']['description']; ?></small>
            </td>
        </tr>
        <tr>
            <td>Max</td>
            <td> <?php echo $_f['_epl_max_attendee_per_regis']['field']; ?>
                <small><?php echo $_f['_epl_max_attendee_per_regis']['description']; ?></small>
            </td>
        </tr>
        <tr>
            <td>Per</td>
            <td> <?php echo epl_get_the_field('_epl_attendee_regis_limit_per', $_f); ?>
                <small><?php echo epl_get_the_desc('_epl_attendee_regis_limit_per', $_f);?></small>
            </td>
        </tr>


    </table>

</div>