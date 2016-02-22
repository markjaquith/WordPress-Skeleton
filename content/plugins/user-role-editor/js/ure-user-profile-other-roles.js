/* User Role Editor - assign to the user other roles 
 * Author: Vladimir Garagulya
 * email: support@role-editor.com
 */

jQuery(document).ready(function(){
    if (jQuery('#ure_select_other_roles').length==0) {
        return;
    }
    jQuery('#ure_select_other_roles').multipleSelect({
            filter: true,
            multiple: true,
            selectAll: false,
            multipleWidth: 600,            
            maxHeight: 300,
            placeholder: ure_data_user_profile_other_roles.select_roles,
            onClick: function(view) {
                ure_update_linked_controls_other_roles();
            }
    });
      
    var other_roles = jQuery('#ure_other_roles').val();
    var selected_roles = other_roles.split(',');
    jQuery('#ure_select_other_roles').multipleSelect('setSelects', selected_roles);      
            
});    


function ure_update_linked_controls_other_roles() {
    var data_value = jQuery('#ure_select_other_roles').multipleSelect('getSelects');
    var to_save = '';
    for (i=0; i<data_value.length; i++) {
        if (to_save!=='') {
            to_save = to_save + ', ';
        }
        to_save = to_save + data_value[i];
    }
    jQuery('#ure_other_roles').val(to_save);
    
    var data_text = jQuery('#ure_select_other_roles').multipleSelect('getSelects', 'text');
    var to_show = '';
    for (i=0; i<data_text.length; i++) {        
        if (to_show!=='') {
            to_show = to_show + ', ';
        }
        to_show = to_show + data_text[i];
    }    
    jQuery('#ure_other_roles_list').html(to_show);
}
