jQuery(document).ready(function($) {

    /*
 * MAJOR CLEANUP AND REFACTORING IN ONE OF THE UPCOMING VERSIONS
 **/

    toggle_recurrence_fields();


    $('a#add_event_to_cart').click(function(){

        var event_id=$('#event_list_id').val();
        var admin_cart_section=$('#admin_cart_section');


        var me = $(this);
        var par = me.parent();
        var id = me.prop('id');

        var data = "epl_action=process_cart_action&cart_action=add_event&epl_controller=epl_registration&event_list_id=" + event_id + "&" + $('form').serialize() ;
        

        events_planner_do_ajax( data, function(r){
            var d = r.html; //$('.data', par).val();
            //


            admin_cart_section.html(d);
        //console.log(r.html);
        //alert(d);


        });


        return false;

    });
    $(document).on('click', 'a#admin_calc_total', function(){

        var event_id=$('#event_list_id').val();
        var admin_totals_section=$('#admin_totals_section');


        var me = $(this);
        var par = me.parent();
        var id = me.prop('id');

        var data = "epl_action=process_cart_action&cart_action=calc_total&epl_controller=epl_registration&event_list_id=" + event_id + "&" + $('form').serialize() ;
        

        events_planner_do_ajax( data, function(r){
            var d = r.html; //$('.data', par).val();
            //


            admin_totals_section.html(d);
        //console.log(r.html);
        //alert(d);


        });


        return false;

    });



    $(document).on('click', 'a#admin_get_regis_form', function(){

        var event_id=$('#event_list_id').val();
        var admin_regis_section=$('#admin_regis_section');


        var me = $(this);
        var par = me.parent();
        var id = me.prop('id');

        var data = "epl_action=process_cart_action&cart_action=regis_form&epl_controller=epl_registration&event_list_id=" + event_id + "&" + $('form').serialize() ;
        

        events_planner_do_ajax( data, function(r){
            var d = r.html; //$('.data', par).val();
            //


            admin_regis_section.html(d);
        //console.log(r.html);
        //alert(d);


        });


        return false;

    });


   $(document).on('click', 'a.epl_event_snapshot, a.epl_regis_snapshot, a.epl_payment_snapshot', function(){

        var me = $(this);
        var par = me.parent();
        var id = me.prop('id');
        var epl_action = me.prop('class');


        var data =  "epl_action=" + epl_action +  "&epl_controller=epl_registration&post_ID=" + this.getAttribute('data-post_ID') + "&event_id=" + this.getAttribute('data-event_id') ;

        events_planner_do_ajax( data, function(r){


            show_slide_down(r.html);
            create_datepicker('.datepicker');


        });


        return false;

    });




        $(document).on('submit', 'form.epl_regis_payment_meta_box_form', function(){
        var me = $(this);
        post_ID = $('input[name="post_ID"]', me).val();

        var data = "epl_action=update_payment_details&epl_controller=epl_registration&" + me.serialize();

        events_planner_do_ajax( data, function(r){

            hide_slide_down();
            $('.epl_regis_list_payment_info_wrapper_' + post_ID).hide().html(r.html).fadeIn();

        });

        return false;
    });

    $('a.epl_get_help, a.epl_send_email').click(function(){

        var me = $(this);
        var data = '';

        if (me.hasClass('epl_send_email')){

            data = "epl_load_feedback_form=1&epl_controller=epl_event_manager&section=" + me.prop('id') ;
            events_planner_do_ajax( data, function(r){

                show_slide_down(r.html);


            });
            //show_slide_down('THE FEEDBACK FORM');
            return false;
        }

        data = "epl_get_help=1&epl_controller=epl_event_manager&section=" + me.prop('id') ;

        events_planner_do_ajax( data, function(r){


            show_slide_down(r.html);


        }) ;
        return false;
    });


        $(document).on('submit', '#epl_feedback_form', function(){
        var me = $(this);

        if (!epl_validate(me))
            return false;

        var data = "epl_send_feedback=1&epl_controller=epl_event_manager&" +me.serialize() ;
        epl_loader('show');
        events_planner_do_ajax( data, function(r){

            show_slide_down(r.html);


        }) ;
        return false;


    });

    $('#epl_pay_type').change(function(){


        var me = $(this);
        var par = me.parent();
        var id = me.prop('id');

        var data = "epl_action=get_pay_profile_fields&epl_controller=epl_pay_profile&" + $('form').serialize() ;
        //alert (me.val());
        //alert (data);

        events_planner_do_ajax( data, function(r){
            var d = r.html; //$('.data', par).val();
            //

            $('#epl_pay_profile_fields_wrapper').html(r.html)

        });


        return false;

    });

    $('select[name="_epl_pricing_type"]').change(function(){
        var data = "epl_action=epl_pricing_type&epl_controller=epl_event_manager&" + $('form').serialize() ;
        
        //$("#container").html('<img src="' + EPL.plugin_url + 'images/ajax-loader.gif">').delay(5000);
        events_planner_do_ajax( data, function(r){

            $('#epl_time_price_section').html(r.html);


        }) ;

        create_timepicker('.timepicker');
        //hide_loader();
        return false;

        

    });


    $('input[name="epl_event_type"]').change(function(){

        
        toggle_recurrence_fields();


    });

    function toggle_recurrence_fields(){
        var v = $(':input[name="epl_event_type"]:checked').val();

        var tb =$(".not_for_class");
        switch (v){
            case "10":
                tb.hide();
                break;

            default:
                tb.show();
                $.each( tb,function(){
                    $(this).show();

                })


        }


    }


    $("a#recurrence_process, a#recurrence_preview").click(function(){

        
        var act =$(this).prop('id');
        
        var data = "epl_action=" + act + "&epl_controller=epl_event_manager&" + $('form').serialize() ;
        //$("#container").html('<img src="' + EPL.plugin_url + 'images/ajax-loader.gif">').delay(5000);
        events_planner_do_ajax( data, function(r){

            if (act == 'recurrence_preview'){
                $("#slide_down_box div.display").html(r.html);
                show_slide_down();
            } else {
                var dates_section =  $('#epl_dates_section');
                dates_section.slideUp(function(){
                    dates_section.html(r.html).slideDown();
                    create_datepicker('.datepicker');
                });
            }
        }) ;
        //hide_loader();
        return false;

    });




        $(document).on('click', 'a.add_time_block', function(){

        //the last time box
        var box = $('div.time-box:last');

        //clone and inster after the last one
        $(box.clone()).insertAfter('div.time-box:last');

        //find the last inserted time box
        var ins = $.find('div.time-box:last'); //inserted element

        //$(ins).css('background-color','red');
        //the time section inside the newest inserted box
        var time_section = $('table:first', ins);

        //the price section inside the newest inserted box
        var price_section = $('.price-box', ins);


        //inside the new time section, remove the index
        //var new_time_index = $(time_section).find('input[name^="epl_start_time"]').attr('name').replace(/(\D)+/g,"");

        //new_time_index = (new_time_index === '')?0:new_time_index; //if [], it will be an empty string
        //new_time_index++;

        var new_time_index = get_random_string();
        //console.log(new_time_index);
        jQuery(':input',time_section).each(function(){

            var me = jQuery(this);
            //me.css('background-color','red');
            //me.val('');

            var tmp_name = me.attr('name');
            var new_name = tmp_name.replace(/\[\w+\]|\[\]/g, '[' + new_time_index + ']');
            //console.log(new_name);
            me.attr('name', new_name);


        });

        //var new_time_index = $(ins).find('input[name^="epl_start_time"]').attr('name').replace(/(\D)+/g,"");
        //var new_price_key = Date.now();

        jQuery('tr',price_section).each(function(){
            var new_price_key = get_random_string();
            jQuery(':input',jQuery(this)).each(function(){

                var me = jQuery(this);

                //me.val('');

                var tmp_name = me.prop('name');

                me.prop('name', tmp_name.replace(/\[\w+\]|\[\]/,'[' + new_price_key + ']'));


                if (tmp_name.search('epl_price_parent_time_id')!== -1)
                {
                    me.val(new_time_index);
                //tmp_name =  tmp_name.replace(/\[\d+\]\[/g, '[' + new_time_index + '][');
                //me.attr('name',  tmp_name.replace(/\]\[\d+/,"]["));

                }




            });
        });

        create_timepicker('.timepicker');
        return false;


    });


    create_sortable(jQuery("#epl_dates_table tbody"));
    create_sortable(jQuery("#epl_prices_table tbody"));
    create_sortable(jQuery("#epl_time_price_section"));
    create_datepicker('.datepicker');
    create_timepicker('.timepicker');

});
