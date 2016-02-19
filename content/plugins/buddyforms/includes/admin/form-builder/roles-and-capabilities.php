<?php
function bf_manage_form_roles_and_capabilities_screen(){
    global $post;

    $form_slug = $post->post_name;

    $form_setup = array();

    if($post->post_name == ''){ ?>

      <b>You need to save the form before you can setup the Form Capabilities</b><br>
      Roles and Capabilities are not stored as post meta. We use the form slug to identified capabilitys

    <?php
    } else {
        ?>
        <div class="bf-roles-main-desc" >
            <div class="bf-col-content">

        <?php
        echo '
            <p>'.__('In WordPress we have user roles and capabilities to manage the user rights. You can decide which user is allowed to create, edit and delete posts by checking the needed capabilities for the different user roles.', 'buddyforms').'</p>
            <p>'.__('If you want to create new user roles and manage all available capabilities I recommend you to install the Members plugin.', 'buddyforms').'</p>
            <p>'.__('Here you can manage all BuddyForms capabilities for all available user roles of your wp install.', 'buddyforms').'</p><br>
            <p><b>'.__('Check/Uncheck capabilities to allow/disallow users to create, edit and/or delete posts of this form', 'buddyforms').'</b></p><p><a href="#" class="bf_check_all">'.__('Check all','buddyforms').'</a></p>
        ';
        ?>



           </div>
        </div>

        <?php
        foreach (get_editable_roles() as $role_name => $role_info):

            $default_roles['buddyforms_' . $form_slug . '_create'] =  'buddyforms_' . $form_slug . '_create';
            $default_roles['buddyforms_' . $form_slug . '_edit']   =  'buddyforms_' . $form_slug . '_edit';
            $default_roles['buddyforms_' . $form_slug . '_delete'] =  'buddyforms_' . $form_slug . '_delete';

            $form_user_role = array();

            foreach ($role_info['capabilities'] as $capability => $_):

                $capability_array = explode('_', $capability);

                if($capability_array[0] == 'buddyforms'){
                    if($capability_array[1] == $form_slug){

                        $form_user_role[$capability] = $capability;
                        $default_roles[$capability] = $capability;

                    }
                }

            endforeach;

            $form_setup[] = new Element_Checkbox('<b>' . $role_name . '</b>', 'buddyforms_roles['.$form_slug.'][' . $role_name . ']', $default_roles, array('value' => $form_user_role, 'inline' => 1));

        endforeach;

        ?>
        <div class="fields_heade postbox">
            <table class="wp-list-table widefat posts">
                <thead>
                <tr>
                    <th class="field_label">Role</th>
                    <th class="field_name">Capabilities</th>
                </tr>
                </thead>
                <tbody id="the-list">
                <?php
                if (isset($form_setup)) {
                    foreach ($form_setup as $key => $field) { ?>
                        <tr id="row_form_title">
                            <th scope="row">
                                <label for="role_role"><?php echo $field->getLabel() ?></label>
                            </th>
                            <td>
                                <?php echo $field->render() ?>
                                <p class="description"><?php echo $field->getShortDesc() ?></p>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>

        <?php

        }

}
