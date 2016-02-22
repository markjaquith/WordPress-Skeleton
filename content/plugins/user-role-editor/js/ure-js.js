// get/post via jQuery
(function ($) {
    $.extend({
        ure_getGo: function (url, params) {
            document.location = url + '?' + $.param(params);
        },
        ure_postGo: function (url, params) {
            var $form = $('<form>')
                    .attr('method', 'post')
                    .attr('action', url);
            $.each(params, function (name, value) {
                $("<input type='hidden'>")
                        .attr('name', name)
                        .attr('value', value)
                        .appendTo($form);
            });
            $form.appendTo('body');
            $form.submit();
        }
    });
})(jQuery);


function ure_ui_button_text(caption) {
    var wrapper = '<span class="ui-button-text">' + caption + '</span>';

    return wrapper;
}


jQuery(function ($) {

    $('#ure_select_all').button({
        label: ure_data.select_all
    }).click(function (event) {
        event.preventDefault();
        ure_select_all(1);
    });

    if (typeof ure_current_role === 'undefined' || 'administrator' !== ure_current_role) {
        $('#ure_unselect_all').button({
            label: ure_data.unselect_all
        }).click(function (event) {
            event.preventDefault();
            ure_select_all(0);
        });

        $('#ure_reverse_selection').button({
            label: ure_data.reverse
        }).click(function (event) {
            event.preventDefault();
            ure_select_all(-1);
        });
    }

    $('#ure_update_role').button({
        label: ure_data.update
    }).click(function () {
        if (ure_data.confirm_role_update == 1) {
            event.preventDefault();
            ure_confirm(ure_data.confirm_submit, ure_form_submit);
        }
    });


    function ure_form_submit() {
        $('#ure_form').submit();
    }


    function ure_show_add_role_dialog() {
        
        $('#ure_add_role_dialog').dialog({
            dialogClass: 'wp-dialog',
            modal: true,
            autoOpen: true,
            closeOnEscape: true,
            width: 400,
            height: 230,
            resizable: false,
            title: ure_data.add_new_role_title,
            'buttons': {
                'Add Role': function () {
                    var role_id = $('#user_role_id').val();
                    if (role_id == '') {
                        alert(ure_data.role_name_required);
                        return false;
                    }
                    if (!(/^[\w-]*$/.test(role_id))) {
                        alert(ure_data.role_name_valid_chars);
                        return false;
                    }
                    if ((/^[0-9]*$/.test(role_id))) {
                        alert(ure_data.numeric_role_name_prohibited);
                        return false;
                    }
                    var role_name = $('#user_role_name').val();
                    var role_copy_from = $('#user_role_copy_from').val();

                    $(this).dialog('close');
                    $.ure_postGo(ure_data.page_url,
                            {action: 'add-new-role', user_role_id: role_id, user_role_name: role_name, user_role_copy_from: role_copy_from,
                                ure_nonce: ure_data.wp_nonce});
                },
                Cancel: function () {
                    $(this).dialog('close');
                    return false;
                }
            }
        });
        $('.ui-dialog-buttonpane button:contains("Add Role")').attr('id', 'dialog-add-role-button');
        $('#dialog-add-role-button').html(ure_ui_button_text(ure_data.add_role));
        $('.ui-dialog-buttonpane button:contains("Cancel")').attr('id', 'add-role-dialog-cancel-button');
        $('#add-role-dialog-cancel-button').html(ure_ui_button_text(ure_data.cancel));

    }


    $('#ure_add_role').button({
        label: ure_data.add_role
    }).click(function (event) {
        event.preventDefault();
        ure_show_add_role_dialog();
    });


    function ure_show_rename_role_dialog() {

        $('#ure_rename_role_dialog').dialog({
            dialogClass: 'wp-dialog',
            modal: true,
            autoOpen: true,
            closeOnEscape: true,
            width: 400,
            height: 230,
            resizable: false,
            title: ure_data.rename_role_title,
            'buttons': {
                'Rename Role': function () {
                    var role_id = $('#ren_user_role_id').val();
                    var role_name = $('#ren_user_role_name').val();
                    $(this).dialog('close');
                    $.ure_postGo(ure_data.page_url,
                            {action: 'rename-role', user_role_id: role_id, user_role_name: role_name, ure_nonce: ure_data.wp_nonce}
                    );
                },
                Cancel: function () {
                    $(this).dialog('close');
                    return false;
                }
            }
        });
        $('.ui-dialog-buttonpane button:contains("Rename Role")').attr('id', 'dialog-rename-role-button');
        $('#dialog-rename-role-button').html(ure_ui_button_text(ure_data.rename_role));
        $('.ui-dialog-buttonpane button:contains("Cancel")').attr('id', 'rename-role-dialog-cancel-button');
        $('#rename-role-dialog-cancel-button').html(ure_ui_button_text(ure_data.cancel));
        $('#ren_user_role_id').val(ure_current_role);
        $('#ren_user_role_name').val(ure_current_role_name);

    }


    $('#ure_rename_role').button({
        label: ure_data.rename_role
    }).click(function (event) {
        event.preventDefault();
        ure_show_rename_role_dialog();
    });


    function ure_show_delete_role_dialog() {
        $('#ure_delete_role_dialog').dialog({
            dialogClass: 'wp-dialog',
            modal: true,
            autoOpen: true,
            closeOnEscape: true,
            width: 320,
            height: 190,
            resizable: false,
            title: ure_data.delete_role,
            buttons: {
                'Delete Role': function () {
                    var user_role_id = $('#del_user_role').val();
                    if (!confirm(ure_data.delete_role)) {
                        return false;
                    }
                    $(this).dialog('close');
                    $.ure_postGo(ure_data.page_url,
                            {action: 'delete-role', user_role_id: user_role_id, ure_nonce: ure_data.wp_nonce});
                },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });
        // translate buttons caption
        $('.ui-dialog-buttonpane button:contains("Delete Role")').attr('id', 'dialog-delete-button');
        $('#dialog-delete-button').html(ure_ui_button_text(ure_data.delete_role));
        $('.ui-dialog-buttonpane button:contains("Cancel")').attr('id', 'delete-role-dialog-cancel-button');
        $('#delete-role-dialog-cancel-button').html(ure_ui_button_text(ure_data.cancel));
    }
    

    $('#ure_delete_role').button({
        label: ure_data.delete_role
    }).click(function (event) {
        event.preventDefault();
        ure_show_delete_role_dialog();        
    });


    function ure_show_add_capability_dialog() {
        $('#ure_add_capability_dialog').dialog({
            dialogClass: 'wp-dialog',
            modal: true,
            autoOpen: true,
            closeOnEscape: true,
            width: 350,
            height: 190,
            resizable: false,
            title: ure_data.add_capability,
            'buttons': {
                'Add Capability': function () {
                    var capability_id = $('#capability_id').val();
                    if (capability_id == '') {
                        alert(ure_data.capability_name_required);
                        return false;
                    }
                    if (!(/^[\w-]*$/.test(capability_id))) {
                        alert(ure_data.capability_name_valid_chars);
                        return false;
                    }

                    $(this).dialog('close');
                    $.ure_postGo(ure_data.page_url,
                            {action: 'add-new-capability', capability_id: capability_id, ure_nonce: ure_data.wp_nonce});
                },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });
        $('.ui-dialog-buttonpane button:contains("Add Capability")').attr('id', 'dialog-add-capability-button');
        $('#dialog-add-capability-button').html(ure_ui_button_text(ure_data.add_capability));
        $('.ui-dialog-buttonpane button:contains("Cancel")').attr('id', 'add-capability-dialog-cancel-button');
        $('#add-capability-dialog-cancel-button').html(ure_ui_button_text(ure_data.cancel));
        
    }


    $('#ure_add_capability').button({
        label: ure_data.add_capability
    }).click(function (event) {
        event.preventDefault();
        ure_show_add_capability_dialog();
    });


    function ure_show_delete_capability_dialog() {
        $('#ure_delete_capability_dialog').dialog({
            dialogClass: 'wp-dialog',
            modal: true,
            autoOpen: true,
            closeOnEscape: true,
            width: 320,
            height: 190,
            resizable: false,
            title: ure_data.delete_capability,
            buttons: {
                'Delete Capability': function () {
                    if (!confirm(ure_data.delete_capability + ' - ' + ure_data.delete_capability_warning)) {
                        return;
                    }
                    $(this).dialog('close');
                    var user_capability_id = $('#remove_user_capability').val();
                    $.ure_postGo(ure_data.page_url,
                            {action: 'delete-user-capability', user_capability_id: user_capability_id, ure_nonce: ure_data.wp_nonce});
                },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });
        // translate buttons caption
        $('.ui-dialog-buttonpane button:contains("Delete Capability")').attr('id', 'dialog-delete-capability-button');
        $('#dialog-delete-capability-button').html(ure_ui_button_text(ure_data.delete_capability));
        $('.ui-dialog-buttonpane button:contains("Cancel")').attr('id', 'delete-capability-dialog-cancel-button');
        $('#delete-capability-dialog-cancel-button').html(ure_ui_button_text(ure_data.cancel));
        
    }
    

    if ($('#ure_delete_capability').length > 0) {
        $('#ure_delete_capability').button({
            label: ure_data.delete_capability
        }).click(function (event) {
            event.preventDefault();
            ure_show_delete_capability_dialog()
        });
    }

    
    function ure_show_default_role_dialog() {
        $('#ure_default_role_dialog').dialog({
            dialogClass: 'wp-dialog',
            modal: true,
            autoOpen: true,
            closeOnEscape: true,
            width: 320,
            height: 190,
            resizable: false,
            title: ure_data.default_role,
            buttons: {
                'Set New Default Role': function () {
                    $(this).dialog('close');
                    var user_role_id = $('#default_user_role').val();
                    $.ure_postGo(ure_data.page_url,
                            {action: 'change-default-role', user_role_id: user_role_id, ure_nonce: ure_data.wp_nonce});
                },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });
        // translate buttons caption
        $('.ui-dialog-buttonpane button:contains("Set New Default Role")').attr('id', 'dialog-default-role-button');
        $('#dialog-default-role-button').html(ure_ui_button_text(ure_data.set_new_default_role));
        $('.ui-dialog-buttonpane button:contains("Cancel")').attr('id', 'default-role-dialog-cancel-button');
        $('#default-role-dialog-cancel-button').html(ure_ui_button_text(ure_data.cancel));
    }
    

    if ($('#ure_default_role').length > 0) {
        $('#ure_default_role').button({
            label: ure_data.default_role
        }).click(function (event) {
            event.preventDefault();                
            ure_show_default_role_dialog();
        });
    }
    

    $('#ure_reset_roles_button').button({
        label: ure_data.reset
    }).click(function (event) {
        event.preventDefault();
        if (!confirm(ure_data.reset_warning)) {
            return false;
        }
        jQuery.ure_postGo(ure_data.page_url, {action: 'reset', ure_nonce: ure_data.wp_nonce});
    });


    function ure_confirm(message, routine) {

        $('#ure_confirmation_dialog').dialog({
            dialogClass: 'wp-dialog',
            modal: true,
            autoOpen: true,
            closeOnEscape: true,
            width: 400,
            height: 180,
            resizable: false,
            title: ure_data.confirm_title,
            'buttons': {
                'No': function () {
                    $(this).dialog('close');
                    return false;
                },
                'Yes': function () {
                    $(this).dialog('close');
                    routine();
                    return true;
                }
            }
        });
        $('#ure_cd_html').html(message);

        $('.ui-dialog-buttonpane button:contains("No")').attr('id', 'dialog-no-button');
        $('#dialog-no-button').html(ure_ui_button_text(ure_data.no_label));
        $('.ui-dialog-buttonpane button:contains("Yes")').attr('id', 'dialog-yes-button');
        $('#dialog-yes-button').html(ure_ui_button_text(ure_data.yes_label));

    }
    // end of ure_confirm()


});
// end of jQuery(function() ...


// change color of apply to all check box - for multi-site setup only
function ure_applyToAllOnClick(cb) {
    el = document.getElementById('ure_apply_to_all_div');
    if (cb.checked) {
        el.style.color = '#FF0000';
    } else {
        el.style.color = '#000000';
    }
}
// end of ure_applyToAllOnClick()


// turn on checkbox back if clicked to turn off
function ure_turn_it_back(control) {

    control.checked = true;

}
// end of ure_turn_it_back()


/**
 * Manipulate mass capability checkboxes selection
 * @param {bool} selected
 * @returns {none}
 */
function ure_select_all(selected) {

    var qfilter = jQuery('#quick_filter').val();
    var form = document.getElementById('ure_form');
    for (i = 0; i < form.elements.length; i++) {
        el = form.elements[i];
        if (el.type !== 'checkbox') {
            continue;
        }
        if (el.name === 'ure_caps_readable' || el.name === 'ure_show_deprecated_caps' ||
                el.name === 'ure_apply_to_all' || el.disabled ||
                el.name.substr(0, 8) === 'wp_role_') {
            continue;
        }
        if (qfilter !== '' && !form.elements[i].parentNode.ure_tag) {
            continue;
        }
        if (selected >= 0) {
            form.elements[i].checked = selected;
        } else {
            form.elements[i].checked = !form.elements[i].checked;
        }

    }

}
// end of ure_select_all()


function ure_turn_caps_readable(user_id) {
    var ure_object = 'user';
    if (user_id === 0) {
        ure_object = 'role';
    }

    jQuery.ure_postGo(ure_data.page_url, {action: 'caps-readable', object: ure_object, user_id: user_id, ure_nonce: ure_data.wp_nonce});

}
// end of ure_turn_caps_readable()


function ure_turn_deprecated_caps(user_id) {

    var ure_object = '';
    if (user_id === 0) {
        ure_object = 'role';
    } else {
        ure_object = 'user';
    }
    jQuery.ure_postGo(ure_data.page_url, {action: 'show-deprecated-caps', object: ure_object, user_id: user_id, ure_nonce: ure_data.wp_nonce});

}
// ure_turn_deprecated_caps()


function ure_role_change(role_name) {

    jQuery.ure_postGo(ure_data.page_url, {action: 'role-change', object: 'role', user_role: role_name, ure_nonce: ure_data.wp_nonce});

}
// end of ure_role_change()


function ure_filter_capabilities(cap_id) {
    var div_list = jQuery("div[id^='ure_div_cap_']");
    for (i = 0; i < div_list.length; i++) {
        if (cap_id !== '' && div_list[i].id.substr(11).indexOf(cap_id) !== -1) {
            div_list[i].ure_tag = true;
            div_list[i].style.color = '#27CF27';
        } else {
            div_list[i].style.color = '#000000';
            div_list[i].ure_tag = false;
        }
    }
    ;

}
// end of ure_filter_capabilities()


function ure_hide_pro_banner() {

    jQuery.ure_postGo(ure_data.page_url, {action: 'hide-pro-banner', ure_nonce: ure_data.wp_nonce});

}
// end of ure_hide_this_banner()
