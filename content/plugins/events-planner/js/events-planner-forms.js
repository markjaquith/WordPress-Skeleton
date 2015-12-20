jQuery(document).ready(function($){


    $(".tabs").tabs({
        selected:0
    });
    $('div#form_field_list table tbody').sortable();
    function field_choices_toggle(el_form){
        //var el_form = el.parents('form');

        var field_type = $(':input[class="epl_field_type"]', el_form);
        var field_choices = $('.epl_field_choices', el_form);
        switch (field_type.val())
        {
            
            case 'checkbox':
                $('input[type="radio"]', field_choices).prop('type', 'checkbox');
                field_choices.slideDown();
                break;
            case 'select':
            case 'radio':
                $('input[type="checkbox"]', field_choices).prop('type', 'radio');
                field_choices.slideDown();
                break;
            default:
                field_choices.slideUp();
        }

        $('.epl_field_choices table tbody').sortable();
        $('#epl_form_form_table table tbody').sortable();

    }

    $('.epl_field_choices').hide();


    $(document).on('blur', '#epl_field_slug, #epl_form_slug, .input_name, .make_slug', function(){
        var me = $(this);

        var new_val = me.val().replace(/\s/g,"_").replace(/\W/g,"").toLowerCase();
        me.val(new_val);

    });

    //this can be fired from many places

    $(document).on('change', 'select.epl_field_type', function(){
        field_choices_toggle($(this).parents('form'));
       

    });

    //Save each form's empty state for resetting
    $('body form').each(function(){

        var me = $(this);
        var my_id = me.prop('id');

        $('body').data(my_id + '_defaults', $('fieldset', me).html());

    });

    

    /*$("table.epl_form_data_table tbody").sortable({
        handle : '.handle',
        update : function () {
        //var order = $("#question_list tbody").sortable('serialize');
        //alert(order);
        //$("#question_list").load("process-sortable.php?"+order);
        }
    });*/


    $(document).on('click', '.reset_button', function(){
        _EPL.hide_overlay();
        var par = $(this).parents('form').prop('id');
        var default_form = $('body').data(par + '_defaults');
        $('form#' + par + ' fieldset').html(default_form);
        $('div#form_field_list ').sortable();

    });

    /*
     * edit or delete icons are clicked
     **/

    $(document).on('click', '.epl_action', function(){
        var me = $(this);
        var my_form = me.parents('form');
        var my_form_id = my_form.prop('id');


        var par = me.closest('li');

        if (par.length == 0)
            par = me.closest('tr');

        if (par.length == 0)
            par = me.parents('div').eq(0);

        if (me.hasClass('epl_delete')){


            var _ess = get_essential_fields(my_form);
            //invoke this function if confirmed on overlay.
            if (me.hasClass('epl_ajax_delete')){
                var a = function(){
                    //sending the form also for the referrer and nonces

                    var data = "epl_form_action=delete&_id=" + me.closest('tr').prop('id') + _ess;

                    events_planner_do_ajax( data, function(r){
                        //console.log(r.code);
                        par.fadeOut().remove();

                    });

                };
            }

            //show the confirmation overlay
            _EPL.delete_element({
                me: me,
                par: par,
                action: a
            });
            return false;
        }

        if (me.hasClass('epl_edit')){
            $('.reset_button', my_form).trigger('click'); //clear the form if another one was selected for editing
           
            _EPL.do_overlay({
                elem: par,
                content: 'Editing... '// + ' <a href="#" class="cancel_edit button-secondary" rel="no">Cancel</a>'
            });

            $('.save_button', my_form).val('Update');
            $(':input[name="epl_form_action"]', my_form).val('edit');

            
            //get the relevant information for ajax

            var _ess = get_essential_fields(my_form);
            //invoke this function if confirmed on overlay.

            var data = "epl_form_action=get_form_data&_id=" + me.closest('tr').prop('id') + _ess;

            events_planner_do_ajax( data, function(r){
                var d = r.html; //$('.data', par).val();
                //console.log(r.html);
                d = $.parseJSON(d);

                if (d != null){

                    for(var p in d) { //slow but not too bad as the set is not that big
                        if (d.hasOwnProperty(p)) { //only target own properties,

                            _EPL.assign_input_value({
                                'input_name':p,
                                'value':d[p],
                                'parent_form':my_form
                            });

                        //console.log(p + ' >> ' + d[p]);

                        }
                    }

                    //show the list of fields in the form
                    if (d['epl_field_choices'] != ''){
                        //show the list of fields in the form
                        $('.epl_field_choices', my_form).html(d['epl_field_choices']);
                    }
                    if (d['form_field_list'] != ''){


                        /*var _available_fields = $(':input[name="_order[]"]', $('form#epl_fields_form')).val();

                        $(_available_fields).each(function(){
                            //console.log('>>' + $(this).val());
                            });


                        $(d['epl_form_fields']).each(function(){ });*/

                        $('div#form_field_list table tbody', my_form).replaceWith(d['form_field_list']);
                        $('div#form_field_list table tbody', my_form).sortable();
                    }
                }

                field_choices_toggle(my_form);//show or hide the field choices section

            });
                  
        }
        //alert (d.epl_field);
        return false;

    });



    $(document).on('click', 'a.cancel_edit', function(){
        var my_form = $(this).parents('form');
        alert(my_form.prop('id'));
        $('.reset_button', my_form).trigger('click');
        return false;

    });

    $('a.check_all, a.uncheck_all').click(function(){

        var cont = $(":input[name^='_epl_recurrence_weekdays']");
        epl_checkbox_state(cont, $(this).prop('class'));

        return false;
    });


    $('form#epl_fields_form, form#epl_forms_form, form#epl_payment_profile_fields_form').submit(function(){


        var me = $(this);

        if (!epl_validate(me))
            return false;

        var my_id = $(this).prop('id');

        var my_table = $('table#' + my_id + '_table');

        var data = $(this).serialize();
        var mode = $('input[name="epl_form_action"]', me).val();
        events_planner_do_ajax( data, function(r){

            if ( mode == 'edit' ){
                //find the id field in the table so we can replace the values
                var _row_id = $('input[name$="_id"]', me).val();
                //console.log(_row_id+r.html);
                _EPL.replace_element({
                    elem:$("#" + _row_id),
                    content: r.html
                });
            } else
                $('table.epl_form_data_table tbody', me).append(r.html);


            $('.reset_button', me).trigger('click');
        }) ;


        return false;

    });




    $(document).on('click', '.add_table_row', function(){
        _EPL.add_table_row({
            table: $(this).closest('table')
        });
        return false;


    });


    $(document).on('click', 'a.delete_table_row', function(){
        _EPL.delete_table_row({
            me: $(this)
        });
        return false;

    });

        
    field_list('epl_fields');
    field_list('epl_admin_fields');
            
    function field_list(id){

        var _cl = $('#' + id + '_form_table tbody').clone(true);

        _cl.removeAttr('id');
        $('.epl_delete, textarea, span', _cl).remove();
        $('input[name="_order[]"]', _cl).attr('name', 'epl_form_fields[]');

        $('.epl_edit', _cl).removeClass('epl_edit').addClass('epl_add');
        $('.epl_lock', _cl).removeClass('epl_lock');

        $('.list_of_fields_for_forms').html(_cl);

    }


    $(document).on('click', 'table.list_of_fields_for_forms tbody td div.epl_action', function(){
        var me = $(this);
        var par = me.closest('tr');
        var _cl = par.clone();
        //@TODO check if already in the list or do overlay
        $('div.epl_action', _cl).removeClass('epl_add').addClass('epl_delete');
        $('div#form_field_list table tbody').append(_cl);

    });



    $(document).on('click', 'a.epl_field_list', function(){
        var my_id = $(this).prop('id');

        field_list(my_id); //repopulate the list of available forms
        var _sd = $('#slide_down_box');
        $('.display', _sd).html('');
        var _cl = $('.list_of_fields_for_forms').clone(true).removeClass('epl_d_n');
                 
        //$('.display', _sd).html(_cl);
        show_slide_down(_cl);

        return false;

    });

    $('form :input').change(function(){

        epl_validate_field(jQuery(this));

    });



});
