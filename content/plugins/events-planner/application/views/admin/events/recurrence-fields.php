
<div id="recurrence_section">
    <div class="epl_box epl_info">
        <div class="epl_box_content">
            <?php

            $t = 'This tool will automatically create days for you in the "Dates" section.  Start selecting
            the fields and click on "Preview" to see a calendar view of your dates.  If you are happy with
            the preview, click on "Get Date Fields" and check the dates section.  For courses, this tool is used to
            present the class schedule.';

            epl_e( $t ); ?>


        </div>
    </div>
    <table class="epl_form_data_table" cellspacing ="0" id ="epl_recurrence_fields_table">
        <tr class ="not_for_class">
            <td><?php epl_e( 'First Event Start Date' ); ?>*</td>
            <td> <?php echo $r_f['_epl_rec_first_start_date']['field']; ?> Until <?php echo $r_f['_epl_rec_first_end_date']['field']; ?></td>
        </tr>
        <tr class ="not_for_class">
            <td><?php epl_e( 'Last Event Date' ); ?>*</td>
            <td><?php echo $r_f['_epl_recurrence_end']['field']; ?></td>
        </tr>
        <tr class ="not_for_class">
            <td><?php epl_e( 'Registrations Start' ); ?>*</td>
            <td> <?php echo $r_f['_epl_rec_regis_start_date']['field']; ?> OR
                <?php echo $r_f['_epl_rec_regis_start_days_before_start_date']['field']; ?> <?php epl_e('days before the start date.'); ?>
            </td>
        </tr>
        <tr class ="not_for_class">
            <td><?php epl_e( 'Registrations End' ); ?>*</td>
            <td>  <?php echo $r_f['_epl_rec_regis_end_date']['field']; ?> OR
                <?php echo $r_f['_epl_rec_regis_end_days_before_start_date']['field']; ?> <?php epl_e('days before the start date.'); ?>
            </td>
        </tr>

        <tr>
            <td><?php epl_e( 'Repeats' ); ?>*</td>
            <td> <?php echo $r_f['_epl_recurrence_frequency']['field']; ?></td>
        </tr>
        <tr class ="for_class">
            <td><?php epl_e( 'Frequency' ); ?>*</td>
            <td><?php echo $r_f['_epl_recurrence_interval']['field']; ?></td>
        </tr>

        <tr class ="for_class">
            <td><?php epl_e( 'Recurrence Weekdays' ); ?>*</td>
            <td><?php echo $r_f['_epl_recurrence_weekdays']['field']; ?>
                (<a href="#" class="check_all">All</a> | <a href="#" class="uncheck_all">None</a>)
            </td>
        </tr>
        <tr class ="for_class">
            <td><?php epl_e( 'Monthly Repeat By' ); ?></td>
            <td>
                <?php echo $r_f['_epl_recurrence_repeat_by']['field']; ?>
                <?php echo $r_f['_epl_recurrence_repeat_by']['description']; ?>
            </td>
        </tr>

    </table>

    <p>
        <a href="#" id="recurrence_preview" class="button-primary"><?php epl_e( 'Preview Calendar' ); ?></a>
        <a href="#" id="recurrence_process" class="button-primary not_for_class"><?php epl_e( 'Get Date Fields' ); ?></a>
    </p>
</div>