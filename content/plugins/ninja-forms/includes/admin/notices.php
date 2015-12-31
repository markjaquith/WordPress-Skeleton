<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Notices page to house all of the admin notices for Core
 *
 * Can be simply used be adding another line into the nf_admin_notices() function
 *
 * The class NF_Notices in notices-class.php can be extended to create more advanced notices to include triggered events
 *
 * @since 2.9
 */

function nf_admin_notices( $notices ) {


    $one_week_support = add_query_arg( array( 'nf_admin_notice_ignore' => 'one_week_support' ) );
    $notices['one_week_support'] = array(
        'title' => __( 'How\'s It Going?', 'ninja-forms' ),
        'msg' => __( 'Thank you for using Ninja Forms! We hope that you\'ve found everything you need, but if you have any questions:', 'ninja-forms' ),
        'link' => '<li><span class="dashicons dashicons-media-text"></span><a target="_blank" href="http://docs.ninjaforms.com/?utm_medium=plugin&utm_source=admin-notice&utm_campaign=Ninja+Forms+Upsell&utm_content=Ninja+Forms+Docs">' . __( 'Check out our documentation', 'ninja-forms' ) . '</a></li>
                    <li><span class="dashicons dashicons-sos"></span><a target="_blank" href="https://ninjaforms.com/contact/?utm_medium=plugin&utm_source=admin-notice&utm_campaign=Ninja+Forms+Upsell&utm_content=Ninja+Forms+Support">' . __( 'Get Some Help' ,'ninja-forms' ) . '</a></li>
                    <li><span class="dashicons dashicons-dismiss"></span><a href="' . $one_week_support . '">' . __( 'Dismiss' ,'ninja-forms' ) . '</a></li>',
        'int' => 7
    );

    $two_week_review_ignore = add_query_arg( array( 'nf_admin_notice_ignore' => 'two_week_review' ) );
    $two_week_review_temp = add_query_arg( array( 'nf_admin_notice_temp_ignore' => 'two_week_review', 'int' => 14 ) );
    $notices['two_week_review'] = array(
        'title' => __( 'Leave A Review?', 'ninja-forms' ),
        'msg' => __( 'We hope you\'ve enjoyed using Ninja Forms! Would you consider leaving us a review on WordPress.org?', 'ninja-forms' ),
        'link' => '<li> <span class="dashicons dashicons-smiley"></span><a href="' . $two_week_review_ignore . '"> ' . __( 'I\'ve already left a review', 'ninja-forms' ) . '</a></li>
                    <li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $two_week_review_temp . '">' . __( 'Maybe Later' ,'ninja-forms' ) . '</a></li>
                    <li><span class="dashicons dashicons-external"></span><a href="http://wordpress.org/support/view/plugin-reviews/ninja-forms?filter=5" target="_blank">' . __( 'Sure! I\'d love to!', 'ninja-forms' ) . '</a></li>',
        'int' => 14
    );


    return $notices;
}
// This function is used to hold all of the basic notices
// Date format accepts most formats but can get confused so preferred methods are m/d/Y or d-m-Y

add_filter( 'nf_admin_notices', 'nf_admin_notices' );

// Require any files that contain class extensions for NF_Notices
require_once( NF_PLUGIN_DIR . 'classes/notices-multipart.php' );

// Require any files that contain class extensions for NF_Notices
require_once( NF_PLUGIN_DIR . 'classes/notices-save-progress.php' );
