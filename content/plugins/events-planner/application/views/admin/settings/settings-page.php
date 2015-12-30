<div id="wpbody-content" style="overflow: hidden;">

    <script type="text/javascript">
        jQuery(document).ready(function($){
             $("#epl_tabs").tabs({ fx: {opacity: 'toggle', duration:'fast' } });

        });
    </script>
    <div class="wrap">

        <div class="icon32" id="icon-options-general"></div>
        <h2>Events Planner General Settings <?php echo get_help_icon( array( 'section' => 'settings' ) ); ?></h2>
        <?php echo epl_show_ad ('Go Pro, get access to plenty of other features, create new fields and access them anywhere.'); ?>
        <?php if ( $settings_updated == 'true' ): ?>
            <div id="message" class="updated"><?php _e( 'Settings Updated', 'events_planner' ); ?></div>
        <?php endif; ?>

            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

            <?php

            wp_nonce_field( 'epl_form_nonce', '_epl_nonce' );
            ?>
            <div id="epl_tabs">
                <ul>
                    <li><a href="#tabs-1">General</a></li>
                    <li><a href="#tabs-2">Registrations</a></li>
                </ul>
                <div id="tabs-1">
                    <table class="epl_form_data_table epl_w500" cellspacing="0">
                        <?php foreach ( $epl_general_option_fields as $field ) : ?>
                            <tr>
                            <?php

                            echo $field;
                            ?>
                        </tr>
                        <?php endforeach; ?>

                        </table>


                    </div>



                    <div id="tabs-2">
                        <table class="epl_form_data_table epl_w500" cellspacing="0">

                        <?php foreach ( $epl_registration_options as $field ) : ?>
                                <tr>
                            <?php

                                echo $field;
                            ?>
                            </tr>
                        <?php endforeach; ?>

                    </table>
                </div>



            </div>


            <p class="submit">
                <input type="submit" value="Save Changes" name="Submit" id="epl_options_submit">
                <input type="hidden" value="1" name="epl_submitted">
            </p>

        </form>
    </div>
</div>

<div class="clear"></div>

<!--<div class="wrap">

