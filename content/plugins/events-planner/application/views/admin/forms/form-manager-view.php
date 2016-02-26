<div id="wpbody-content" style="overflow: hidden;">


    <div class="wrap">

        <div class="icon32" id="icon-options-general"></div>
        <h2><?php epl_e( 'Events Planner Form Manager' ); ?></h2>

        <div id="epl_tabs" style="overflow:auto;">
            <ul>
                <li><a href="#tabs-1"><?php epl_e( 'Registration Fields' ); ?></a></li>
                <li><a href="#tabs-2" id="epl_form_form_load"><?php epl_e( 'Registration Forms' ); ?></a></li>

            </ul>




            <div id="tabs-1">
                <div class="clearfix"> <?php echo get_help_icon( array( 'section' => 'regis_fields' ) ); ?></div>
                <?php echo $fields_page; ?>
            </div>
            <div id="tabs-2">
                <div class="clearfix"><?php echo get_help_icon( array( 'section' => 'regis_forms' ) ); ?></div>
                <?php echo $forms_page; ?>

            </div>


        </div>
    </div>


    <div id="list_of_fields_for_forms_wrapper">


        <table class="list_of_fields_for_forms epl_form_data_table epl_d_n epl_w400"  cellspacing="0">
            <tbody>
                <?php

                //echo $field_list_for_forms;
                ?>
            </tbody>
        </table>
    </div>
</div>
    <script type="text/javascript">
        jQuery(document).ready(function($){
             $("#epl_tabs").tabs({ fx: {opacity: 'toggle', duration:'fast' } });

        });
    </script>

<div class="clear"></div>

<!--<div class="wrap">



