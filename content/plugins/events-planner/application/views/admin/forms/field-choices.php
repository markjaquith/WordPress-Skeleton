<?php
/*
 * this is a list of field choices for radio, checkbox, and selects
 */
if ( empty( $field_choices_form ) )
    return null;
?>
<table class="epl_subform_table" cellspacing="0">
    <thead>
        <tr><th></th><th>Field Label</th><th>Field Value (optional)</th><th></th></tr>
    </thead>

    <tbody>
        <?php foreach ( $field_choices_form as $row ) : ?>
            <tr>
                <td> <div class="handle"></div></td>
            <?php

            echo $row;
            ?>
            <td>
                <div class="epl_delete epl_action"></div>

            </td>



        </tr>
        <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4">

                    <div id="<?php echo $index; ?>" class="add_table_row epl_add"></div>
            </td>
        </tr>
    </tfoot>
</table>