<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <title>Registration Confirmation</title>
        <!--general stylesheet-->
        <style type="text/css">
            p { padding: 0; margin: 0; }
            h1, h2, h3, p, li { font-family: Helvetica Neue, Helvetica, Arial, sans-serif; }
            td { vertical-align:top;}
            ul, ol { margin: 0; padding: 0;}
            .heading {
                border-radius: 3px;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                -khtml-border-radius: 3px;
                -icab-border-radius: 3px;
            }

        </style>
    </head>
    <body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" background="" style="margin: 0px; background-color: #ffffff;" bgcolor="#ffffff">
        <table cellspacing="0" border="0" cellpadding="0" width="100%">
            <tbody>
                <tr valign="top">
                    <td><!--container-->
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="750" bgcolor="#ffffff">
                            <tbody>
                                <tr><!--content-->
                                    <td valign="middle" bgcolor="#ebebeb" height="30" style="vertical-align: middle; border-bottom-color: #d6d6d6; border-bottom-width: 1px; border-bottom-style: solid;">
                                        <p style="font-size: 11px; font-weight: bold; color: #8a8a8a; text-align: center;">
                                            <?php epl_e('Registration Confirmation'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" bgcolor="#ffffff" align="center">
                                        <table cellspacing="0" border="0" cellpadding="0" width="700">
                                            <tbody>
                                                <tr>
                                                    <td valign="top" height="37" style="height: 37px;">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" colspan="2" style="text-align: left;">
                                                        <h1 style="margin: 0; padding: 0; font-size: 22px; color: #fd2323; font-weight: bold;"><?php echo get_the_event_title(); ?> <?php epl_e('by'); ?> <?php echo get_the_organization_name(); ?></h1>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" height="34" style="height: 34px; border-bottom-color: #d6d6d6; border-bottom-width: 1px; border-bottom-style: solid;">


                                                        <div class="" style="margin:10px auto;border:1px solid #eee;padding: 10px;">
                                                            <div class="section">


                                                                <div class="address_section">

                                                                    <div><strong><?php epl_e('Registration ID'); ?>: <?php echo get_the_regis_id(); ?></strong></div>
                                                                    <?php echo get_the_location_name(); ?><br />
                                                                    <?php echo get_the_location_address(); ?> <?php echo get_the_location_address2(); ?><br />
                                                                    <?php echo get_the_location_city(); ?>, <?php echo get_the_location_state(); ?> <?php echo get_the_location_zip(); ?><br />
                                                                </div>

                                                                <div class="time_section">
                                                                    Tickets
                                                                    <?php echo get_the_regis_prices(); ?>
                                                                </div>

                                                                <div class="time_section">
                                                                    Time(s)
                                                                    <?php echo get_the_regis_times(); ?>
                                                                </div>

                                                                <div class="date_section">
                                                                    Date(s)
                                                                    <?php echo get_the_regis_dates(); ?>
                                                                </div>


                                                            </div>
                                                        </div>
                                                        <div class="" style="margin:10px auto;border:1px solid #eee;padding: 10px;">

                                                            <?php

                                                                    if ( isset( $payment_details ) && $payment_details != '' && !epl_is_free_event() )
                                                                        echo $payment_details;
                                                            ?>
                                                            <?php $regis_form =  str_replace('for=\'\'', 'style="font-style:italic;color:#555555;font-size:12px"',$regis_form); ?>

                                                            <?php echo str_replace(array('<span class="overview_value">','</span>'), array('<p style="margin-top:5px;margin-left:10px;font-size:16px;color:#00000;">','</p>'),$regis_form); ?>


                                                        </div>

                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </td>
                                    <!--/content-->
                                </tr>
                            </tbody>
                        </table><!--/container-->
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>