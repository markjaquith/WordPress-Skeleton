

jQuery(document).ready(function($){
    /*
 * Please, no funny business.  The ids passed are checked for accuracy, so...
 */


    $('a#calculate_total_due').click(function(){



        var me = $(this);
        var par = me.parent();
        var id = me.prop('id');
        var form =  $('#events_planner_shopping_cart');

        var data = form.serialize() + "&epl_action=process_cart_action&cart_action=calculate_total_due&epl_controller=epl_front" ;

        events_planner_do_ajax( data, function(r){
            var d = r.html; //$('.data', par).val();
            //
            // $('.epl_totals_table').replaceWith(d);

            $('#epl_totals_wrapper table').replaceWith(d);


        //console.log(r.html);
        //alert(d);


        });


        return false;

    });

    $('.epl_att_qty_dd').change(function(){
        $('a#calculate_total_due').trigger('click');
    });
    
    $('a.add_to_cart').click(function(){
        var me = $(this);
        var par = me.parent();
        var id = me.prop('id');

        var data = "epl_action=process_cart_action&cart_action=add&epl_controller=epl_front&event_id=" + id;

        events_planner_do_ajax( data, function(r){
            var d = r.html; //$('.data', par).val();
            //
            par.html(d);
        //console.log(r.html);
        //alert(d);


        });


        return false;

    });

    $('#events_planner_shopping_cart').submit(function(){

        return true;
        var me = $(this);

        var data = "epl_action=process_cart_action&cart_action=update&epl_controller=epl_front&" + me.serialize();

        events_planner_do_ajax( data, function(r){
            var d = r.html;
            
            par.html(d);


        });


        return false;



    });
   

    $(document).on('click', 'div.widget_has_data', function(){
        $('.epl_calendar_widget_data').html(Math.random());
        



    });


    $(document).on('click', 'a.epl_next_prev_link', function(){
        var me = $(this);
        var url = me.prop("href");
        var par = me.parents('table');

        var data =url + "&epl_action=widget_cal_next_prev&epl_controller=epl_front";

        events_planner_do_ajax( data, function(r){
            var d = r.html; //$('.data', par).val();
            //
            par.replaceWith(d);
        //console.log(r.html);
        //alert(d);


        });


        return false;

        
    });


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
    });


    $("#events_planner_shopping_cart").validate({
        submitHandler: function(form) {
            form.submit();
        }
    });


    $(document).on('click', 'form#events_planner_shopping_cart1', function(){
        var me = $(this);

        me.validate();

        /*        if (!epl_validate(me))
            return false;*/

        return true;
        var main_cont = $('#epl_main_container');
        var ajax_cont = $('#epl_ajax_content');
        var h = main_cont.outerHeight();

        main_cont.css('min-height', h + 'px');
        var data = me.serialize() + "&epl_controller=epl_front";

        events_planner_do_ajax(data, function(r){


            ajax_cont.fadeOut('fast').html(r.html).delay(400).fadeIn('fast');


        });



        return false;
    });




});