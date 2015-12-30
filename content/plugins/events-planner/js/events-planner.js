jQuery(document).ready(function($) {

    $('.help_tip').tipsy({
        opacity: 1
    });

    $.ajaxSetup({
        cache: false,
        xhr: function()
        {
            if ($.browser.msie)
            {
                return new ActiveXObject("Microsoft.XMLHTTP");
            }
            else
            {
                return new XMLHttpRequest();
            }
        },
        type: "POST",
        url:  EPL.ajaxurl
    });


    $(document).on('click', '#dismiss_loader', function(){
        hide_slide_down();
        return false;
    });

    //$('#slide_down_box').draggable();



    $(document).on('click', '.epl_delete_element, .delete_element', function(){
        /*_EPL.delete_element({
            me: $(this)
        });*/

        _EPL.delete_element({
            me: $(this)
        });
        return false;

    });

});

var _EPL = {
    

    progress: function(container){

        $(container).html('<img src="' + EPL.plugin_url + 'images/ajax-loader2.gif">');

    },

    add_table_row: function(params){


        var row =jQuery(params.table).find('tbody tr:last').html();//can use .clone() but will have to use cloned.appendTo

        var ins = params.table.append('<tr>' + row + '</tr>').find('tr:last');

        ins = jQuery(params.table).find('tbody tr:last');
        
        ins.find('img.ui-datepicker-trigger').remove(); //need to remove datepicker icon so when datepicker re-created, there aren't two calendar icons
        
        var new_key = get_random_string();

        //for each one of the elements in the inserted row, remove the value and get rid of the index in the name key
        jQuery(':input',ins).each(function(){

            var me = jQuery(this);

            var tmp_name = me.attr('name');
            me.attr('name',  tmp_name.replace(/\[\w+\]|\[\]/,'[' + new_key + ']'));

            if (me.hasClass('hasDatepicker')){
                
                me.removeClass('hasDatepicker').removeAttr('id');
                //me.datepicker('destroy');
                create_datepicker(me);
                
            };

            if (me.hasClass('hasTimepicker')){

                me.removeClass('hasTimepicker').removeAttr('id');
                //me.datepicker('destroy');
                create_timepicker(me);

            };
        /*if (me.attr('type') != 'hidden')
                me.val('');*/
            

        });

         
        create_datepicker(jQuery('.datepicker', ins));
        create_timepicker(jQuery('.timepicker', ins));

        return false;

    },
    delete_table_row: function(params){

        var par = params.me.closest('table');


        if(jQuery("tbody >tr", par).size() == 1)
        {
            alert ("At least one row is required.");
            return false;
        }

        var par_row = params.me.closest('tr');

        var rel = params.me.attr('rel');

        if (typeof rel == 'undefined'){

            jQuery('body').data('epl_del_elem', par_row);


            var conf = '<a href="#" class="delete_table_row  rel="yes">Confirm</a>';
            var cancel = ' <a href="#" class="delete_table_row " rel="no">Cancel</a>';
            _EPL.do_overlay({
                'elem':par_row,
                'content':conf + cancel
            });

        }
        else if (rel == 'no'){

            _EPL.hide_overlay();
            
        }
        else if (rel == 'yes'){

            jQuery('body').data('epl_del_elem').slideUp().remove();

            jQuery('body').removeData('epl_del_elem');
            _EPL.hide_overlay();
        }



        return false;


    },
    delete_element: function(params){

        /*
 *  How this works.
 *  - an element is clicked to delete another element
 *      - In the first click, the .data stores the element to be deleted
 *      - If a function needs to run after confirmation, the function is stored in data as well
 *      - an overlay is displayed over the element that needs to be deleted, with confirm and cancel links
 *   - if cancel, remove overlay, empty .data
 *   - if confirm, remove element, run function, if any();
 */

        //var par = params.par;

        var par = params.me.closest('li');

        if (par.length == 0){
            par = params.me.closest('tr');

            if(jQuery("tbody >tr", par.closest('table') ).size() == 1)
            {
                alert ("At least one row is required.");
                return false;
            }

        }

        if (par.length == 0)
            par = params.me.parents('div').eq(0);

        var rel = params.me.attr('rel');

        if (typeof rel == 'undefined'){

            jQuery('body').data('epl_del_elem', par);
            jQuery('body').data('epl_del_elem_action', params.action);


            var conf = '<a href="#" class="delete_element " rel="yes">Confirm</a>';
            var cancel = ' <a href="#" class="delete_element " rel="no">Cancel</a>';
            var _ol_id = _EPL.do_overlay({
                'elem':par,
                'content':conf + cancel
            });

            jQuery('body').data('epl_del_elem_ol_id', _ol_id);
        }
        else if (rel == 'no'){

            _EPL.hide_overlay({
                '_ol_id': jQuery('body').data('epl_del_elem_ol_id')
            });
            jQuery('body').removeData('epl_del_elem');
            jQuery('body').removeData('epl_del_elem_action');
            jQuery('body').removeData('epl_del_elem_ol_id');
            
        }
        else if (rel == 'yes'){
           
            var r = null;
            if (typeof jQuery('body').data('epl_del_elem_action') != 'undefined'){
                //invoke the function
                r = jQuery('body').data('epl_del_elem_action')();

            } else {
                 
                r = jQuery('body').data('epl_del_elem').slideUp().slideUp(300).remove();
            }

            _EPL.hide_overlay({
                '_ol_id': jQuery('body').data('epl_del_elem_ol_id')
            });
            jQuery('body').removeData('epl_del_elem');
            jQuery('body').removeData('epl_del_elem_action');
            jQuery('body').removeData('epl_del_elem_action');

            return r;

        }



        return false;


    },
    replace_element: function(params){

        var elem = params.elem;

        _EPL.hide_overlay();

        //elem.slideUp();//.replaceWith(params.content).slideDown();
        elem.replaceWith(params.content);

        return false;


    },
    assign_input_value: function(params){

        var val = params.value;
        var _parent_form = params.parent_form;
        var _input = jQuery(":input[name='" + params.input_name + "']", _parent_form);
       
        if (_input.length === 0) //if the input was not found, chances are it's an array
            _input = jQuery(":input[name='" + params.input_name + "[]']", _parent_form);

           
        switch (_input.prop('type'))
        {
            case 'text':
            case 'hidden':
            case 'select-one':
            case 'textarea':

                _input.val(val);
                break;
            case 'radio':
            case 'checkbox':
                // val comes in as 0,1,2
                //jQuery still accepts these values and determines which one to select, YAY :)
                //Could have kept with the above group but put them here just in case

                _input.val(val);

                // if the above doesn't work, try
                var arr = jQuery.makeArray(val);

                jQuery(_input).each(function(){

                    if (jQuery.inArray(jQuery(this).val(), arr) > -1) //-1 = not found
                        jQuery(this).prop("checked", "checked");

                });
                break;

        }


    },
    do_overlay: function(params){

        var elem = params.elem;

        var _new_id = Date.now();
        
        var  div_overlay = jQuery('#epl_overlay').clone(true).prop('id', _new_id).addClass('epl_overlay');
        var _offsets = elem.offset();

        div_overlay.css({
            top: _offsets.top,
            left: _offsets.left,
            width: elem.outerWidth(),
            height: elem.outerHeight()
        });
        jQuery('div',div_overlay).html(params.content);

        div_overlay.appendTo('body').show();
        return _new_id;
    },
    hide_overlay: function(params){
        if (params !== undefined)
            jQuery('div#' + params._ol_id).fadeOut(400).remove();
        else
            jQuery('.epl_overlay').fadeOut(400).remove();
    },
    hide_overlay_all: function(){

        jQuery('.epl_overlay').fadeOut(400).remove();
    },
    populate_dd: function(el, num_options){

        var temp_val =0;
        temp_val = el.val(); // in case there is a selected value, remember


        if (temp_val > 0)
            num_options = parseInt(temp_val) + parseInt(num_options);

        //remove all the <options> and reconstruct
        el.children().remove();
        //Reconstruch the dd based on avaiable spaces left
        for (var i=0;i<=num_options;i++){
            jQuery(el).append(
                jQuery('<option></option>').val(i).html(i)
                );


        }
        //assign the previously selected value to the newly modified dd
        el.val(temp_val);

    }


}

/*
 * Global Functions
 *
 **/

function events_planner_do_ajax(data, callback){
    data += "&epl_ajax=1&action=events_planner_form";
    jQuery.ajax({
        data: data,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
            epl_loader('show');
        },
        success: function(response, textStatus){

            events_planner_process_response(response, callback);

        },
        error: function(response) {
            events_planner_process_response(response, callback);//alert("Error.");
        },
        complete:function(){
            epl_loader('hide');
        }
    });

}

function events_planner_process_response(from_server, callback)
{
    if (from_server == null){
        return false;
    }

    if (from_server.is_error ==1){
           
        alert(from_server.error_text);
            

    }
    else
    {
        callback(from_server);
    }

    return true;
}

function epl_loader(act){

    var loader = jQuery('#epl_loader');

    if (act == 'show'){
        loader.show();
 
    }
    else {
        loader.hide();
    }
}


function show_loader_image(container){

    jQuery('#'.container).html('<img src="' + EPL.plugin_url + 'images/ajax-loader.gif" alt="loading..." />');

}

function show_slide_down(cont){

    show_loader_image('slide_down_box div.display');

    if(cont !== '')
        jQuery("#slide_down_box div.display").html(cont);

    jQuery('#slide_down_box').animate({
        'top':'0px'
    },500);

    return true;
}

function hide_slide_down(){

    var height = Number(jQuery('#slide_down_box').outerHeight(true) + 10);

    jQuery('#slide_down_box').animate({
        'top':'-' + height + 'px'
    },500,function(){
                        
        });
};

function create_datepicker(elem){
    jQuery( elem ).datepicker({
        dateFormat: EPL.date_format,
        showOn: "button",
        buttonImage: EPL.plugin_url + "images/calendar.png",
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true
    });

}
function create_sortable(elem){
    jQuery( elem ).sortable();

}
function create_timepicker(elem){
    var _t_f =  (EPL.time_format == "H:i")?false:true;
    var opt = {
        showPeriod: _t_f,
        showLeadingZero: true,
        showPeriodLabels: _t_f
    };
    //showPeriodLabels: false,
 
    jQuery(elem).timepicker(opt);

}

function destroy_datepicker(elem){
    jQuery( elem ).datepicker('destroy');

}

function clear_form(form){

    jQuery(':input',form)
    .not(':button, :submit, :reset, :hidden, .no_clear')
    .val('')
    .removeAttr('checked')
    .removeAttr('selected');
}

//get the nonce fields, scope
//this is used for ajax calls when we don't need to send the whole form data
//on the admin side
function get_essential_fields(_form){

    return "&form_scope=" + jQuery(":input[name='form_scope']",_form).val() + "&_epl_nonce=" + jQuery(":input[name='_epl_nonce']",_form).val()
    + "&_wp_http_referer=" + jQuery(":input[name='_wp_http_referer']",_form).val()
    + "&epl_controller=" + jQuery(":input[name='epl_controller']",_form).val();


}

function epl_checkbox_state(control, state){
    state = (state == 'check_all'?true:false);
    jQuery(control).prop("checked",state);

}

function get_random_string() {
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    var string_length = 6;
    var random_string = '';
    for (var i=0; i<string_length; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        random_string += chars.substring(rnum,rnum+1);
    }
    return random_string;
}

/*
 * JS field validations.  Will only check for required or email, for now.
 * Planning on adding min, max, alpha, numeric, custom regexp, tooltip
 */

function epl_validate(form){

    epl_valid = true;
    jQuery.each (jQuery('input, textarea',form), function (){

        var field = jQuery(this);

        if (field.prop('type') != 'submit' && field.prop('type') != 'hidden'){
            
            if (!epl_validate_field(field))
                epl_valid = false;

        }
    });


    return epl_valid;
}

function epl_validate_field(field){

    //http://www.regular-expressions.info/email.html should get 99.99%
    var email_regexp = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;



    if (field.hasClass('required') && jQuery.trim(field.val()) == ''){

        field.css('background-color','pink');
        /*par.animate({
            backgroundColor: 'pink'
        }, 300);*/
        return false;
        
    }

    if (field.hasClass('email') && !validate_regex(field.val(), email_regexp)){

        
        field.css('background-color','pink');
        return false;
    } else {
 
        field.css('background-color','#fff');
        return true;
    }
}




function validate_regex(FValue, VRegExp){
    if(VRegExp){
        var re=new RegExp(VRegExp);
        if (re.test(FValue)) {
            return true;
        } else {
            return false;
        }
    }
    else
    {
        return "empty";
    }



}