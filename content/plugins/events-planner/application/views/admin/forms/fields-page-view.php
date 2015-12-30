<form autocomplete="off" id ="<?php echo $scope ;?>_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post"  class="epl_ajax_form">


    <?php

    wp_nonce_field( 'epl_form_nonce', '_epl_nonce' );
    ?>
    <div class="epl_form_editor_wrapper">
        <fieldset class="epl_fieldset">
            <legend>Field creation and edit form </legend>
            <table  cellspacing="0" style="width:530px;float:left;"  class="epl_form_data_table">

                <?php foreach ( $events_planner_field_form as $field ) : ?>
                    <tr>
                    <?php

                    echo $field;
                    ?>
                </tr>
                <?php endforeach; ?>
                </table>

                <div class="epl_button_wrapper">
                    <input type="submit" class="save_button button-primary" value="Add" name="submit" />
                    <input type="reset" class="reset_button button-secondary" value="Cancel" name="Cancel" id="" />
                </div>

                <input type="hidden" value="add" name="epl_form_action" />
                <input type="hidden" value="<?php echo $scope ;?>" name="form_scope" />

            </fieldset>
        </div>

        <div class="epl_list_wrapper epl_field_list_wrapper" style="">
            
            <table id ="<?php echo $scope ;?>_form_table" cellspacing ="0" class="epl_form_data_table">
                <thead>
  
                </thead>
                <tbody>
                <?php

                    echo $list_of_fields;
                ?>
            </tbody>

        </table>


    </div>
</form>