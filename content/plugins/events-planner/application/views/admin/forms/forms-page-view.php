<?php

/*
 * this view produces the forms for creating registration forms and admin meta boxes.
 */

?>

<form id ="<?php echo $scope ;?>_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="epl_ajax_form">


    <?php wp_nonce_field( 'epl_form_nonce', '_epl_nonce' ); ?>

    <div class="epl_form_editor_wrapper">
        <fieldset class="epl_fieldset">
            <legend>Form creation and edit form </legend>
            <table cellspacing="0" style="width:530px;float:left;"  class="epl_form_data_table">

                <?php foreach ( $events_planner_forms_form as $field ) : ?>
                    <tr>
                    <?php

                    echo $field;
                    ?>
                </tr>
                <?php endforeach; ?>
                    <tr>
                        <td colspan="2">
                            <div id="form_field_list">
                                <div style="padding:10px;">
                                    <a href="#" id="<?php echo $included_fields ;?>" class="epl_button epl_field_list">Click here to select fields</a>
                                </div>
                                
                                <div class="" style ="border: 1px solid #e8e8e8;padding:5px;">
                                    <table style="width:100%" cellspacing="0" class="epl_field_list_inside_form">
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </td>

                    </tr>


                </table>
                <div class="epl_button_wrapper">
                    <input type="submit" class="save_button button-primary" value="Add" name="Add" />
                    <input type="reset" class="reset_button button-secondary" value="Cancel" name="Cancel" id="" />
                </div>

                <input type="hidden" value="add" name="epl_form_action" />
                <input type="hidden" value="<?php echo $scope ;?>" name="form_scope" />

            </fieldset>
        </div>
        <div class="epl_list_wrapper">

            <div>

                <table id="epl_form_form_table" cellspacing="0" class="epl_form_data_table">
                    
                    <tbody>


                    <?php

                    echo $list_of_forms;
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</form>