<?php

/**
 * This controller handles all the calls from the front of the website, uses multiple models
 *
 * @package		Events Planner for Wordpress
 * @author		Abel Sekepyan
 * @link		http://wpeventsplanner.com
 */
if ( !class_exists( 'EPL_front' ) ) {

    class EPL_front extends EPL_Controller {


        function __construct() {
            global $_on_admin, $current_step;
            $_on_admin = false;

            parent::__construct();


            epl_log( 'init', get_class() . " initialized" );
            $this->rm = $this->epl->load_model( 'epl-registration-model' );
            $this->ecm = $this->epl->load_model( 'epl-common-model' );

            if ( isset( $_REQUEST['epl_action'] ) ) {
                $this->run();
            }
            //add_filter( 'the_title', array( $this, '__return_empty_string' ) );
        }


        function __return_empty_string( $string ) {

            return $string;
        }


        function run() {

            global $epl_fields, $epl_next_step, $epl_current_step;

            $defined_actions = array(
                'show_event_list',
                'process_cart_action',
                'delete_item_from_cart',
                'show_cart',
                'regis_form',
                'show_cart_overview',
                'add_regis_info',
                'overview',
                'payment_page',
                'complete',
                '_exp_checkout_payment_success',
                '_exp_checkout_payment_cancel',
                '_exp_checkout_do_payment',
                'thank_you_page',
                'attendee_list',
                'process_payment',
                'send_email',
                'event_details',
                'widget_cal_next_prev'
            );



            if ( isset( $_REQUEST['epl_action'] ) ) {

                //POST has higher priority

                $epl_action = esc_attr( isset( $_POST['epl_action'] ) ? $_POST['epl_action'] : $_GET['epl_action']  );

                if ( in_array( $epl_action, $defined_actions ) ) {
                    if ( method_exists( $this, $epl_action ) ) {

                        $epl_current_step = $epl_action;

                        echo $this->$epl_action();
                    }
                }
            }
            else {

                /*
                 * get the event list
                 * in the loop, a global var $event_list is set for the the template tags
                 */
                add_action( 'the_post', array( $this, 'set_event_list' ) );

                return $this->the_event_list();
            }
        }


        function set_event_list( $param ) {

            $this->epl_util->set_the_event_details();
        }


        function the_event_list() {


            global $post;

            global $event_list;
            $this->ecm->events_list( array( 'show_past' => 1 ) );

            $data['event_list'] = $event_list;

            $r = null;

            $r = $this->epl->load_template_file( 'event-list.php' );

            //template not found
            if ( is_null( $r ) ) {
                $r = $this->epl->load_view( 'front/event-list', $data, true );
            }
            return $r;
        }

        /*
         * add, delete, calc total ON CART
         */


        function process_cart_action() {

            $r = $this->rm->_process_session_cart();

            if ( !$GLOBALS['epl_ajax'] ) {
                return $this->show_cart();
            }
            echo $this->epl_util->epl_response( array( 'html' => $r ) );
            die();
        }


        function widget_cal_next_prev() {
            $r = $this->epl_util->get_widget_cal();
            echo $this->epl_util->epl_response( array( 'html' => $r ) );
            die();
        }


        function show_cart() {

            $this->rm->set_mode( 'edit' );
            $data['cart_data'] = $this->rm->show_cart();
            $data['mode'] = 'edit';
            $data['content'] = $this->epl->load_view( 'front/cart/cart', $data, true );
            $data['next_step'] = 'regis_form';

            $data['form_action'] = esc_url(add_query_arg( 'epl_action', 'regis_form', $_SERVER['REQUEST_URI'] ) );
            $data['next_step_label'] = 'Next: Attendee Information';

            return $this->epl->load_view( 'front/cart-container', $data, true );
        }


        function regis_form() {
            global $epl_error;
            $this->rm->set_mode( 'edit' );
            $data['mode'] = 'edit';
            $data['content'] = $this->rm->regis_form();

            $data['prev_step_url'] = esc_url(add_query_arg( 'epl_action', 'show_cart', $_SERVER['REQUEST_URI'] ) );

            $data['form_action'] = esc_url(add_query_arg( 'epl_action', 'show_cart_overview', $_SERVER['REQUEST_URI'] ) );
            $data['next_step'] = 'show_cart_overview';

            if ( empty( $epl_error ) )
                $data['next_step_label'] = 'Overview';

            $this->epl->load_view( 'front/cart-container', $data );
        }


        function show_cart_overview( $next_step = null ) {
            //global $mode;
            //in case they come back from thank you page.
            if ( $this->rm->epl_is_empty_cart() ) {

                echo $this->epl_util->epl_invoke_error( 20, null, false );
            }
            else {
                $this->rm->set_mode( 'overview' );

                $data['cart_data'] = $this->rm->show_cart();
                $data['mode'] = 'overview';
                $data['content'] = $this->epl->load_view( 'front/cart/cart', $data, false );


                $data['content'] .= $this->rm->regis_form();
                $data['next_step'] = $next_step;
                $data['prev_step_url'] = esc_url(add_query_arg( 'epl_action', 'regis_form', $_SERVER['REQUEST_URI'] ) );
                if ( is_null( $data['next_step'] ) )
                    $data['next_step'] = 'payment_page';

                if ( epl_is_free_event() ) {

                    $data['form_action'] = esc_url(add_query_arg( 'epl_action', 'thank_you_page', $_SERVER['REQUEST_URI'] ) );

                    $data['next_step'] = 'thank_you_page';
                    $data['next_step_label'] = 'Confirm and Complete';
                }
                else {
                    $data['form_action'] = esc_url(add_query_arg( 'epl_action', 'payment_page', $_SERVER['REQUEST_URI'] ) );

                    $data['next_step'] = 'payment_page';
                    $data['next_step_label'] = 'Confirm and Continue to PayPal';
                }



                $this->epl->load_view( 'front/cart-container', $data );
            }
        }


        function payment_page() {

            $this->rm->set_mode( 'overview' );
            $data['cart_data'] = $this->rm->show_cart();

            $egp = $this->epl->load_model( 'epl-gateway-model' );
            $egp->_express_checkout_redirect();
        }


        function thank_you_page() {
            //$this->rm->set_mode( 'thank_you_page' );
            /*
             * display payment info
             * display overview
             * send email
             * destry session
             */

            //echo "<pre class='prettyprint'>" . print_r($_SESSION, true). "</pre>";
            if ( $this->rm->epl_is_empty_cart() ) {

                echo $this->epl_util->epl_invoke_error( 20, null, false );
            }
            else {
                $this->ecm->setup_regis_details( $_SESSION['__epl']['post_ID'] );
                $this->rm->set_mode( 'overview' );

                $data['cart_data'] = $this->rm->show_cart();

                //$data['thank_you_message'] = $this->epl->load_view( 'front/registration/regis-thank-you-section', '', true );
                //$data['content'] = $this->epl->load_view( 'front/cart/cart', $data );


                $data['regis_form'] = $this->rm->regis_form( null, false );
                $data['payment_details'] = $this->epl->load_view( 'front/registration/regis-payment-details', '', true );


                //$data['prev_step_url'] = esc_url(add_query_arg( 'epl_action', 'regis_form', $_SERVER['REQUEST_URI'] ) );
                if ( is_null( $data['next_step'] ) )
                    $data['next_step'] = 'payment_page';

                if ( epl_is_free_event() ) {

                    //$data['form_action'] = esc_url(add_query_arg( 'epl_action', 'thank_you_page', $_SERVER['REQUEST_URI'] ) );
                    //$data['next_step'] = 'thank_you_page';
                    //$data['next_step_label'] = 'Confirm and Complete';
                    $post_ID = $_SESSION['__epl']['post_ID'];
                    $data['post_ID'] = $post_ID;
                    $data['_epl_regis_status'] = '5';
                    $data['_epl_grand_total'] = 0;
                    $data['_epl_payment_amount'] = 0;
                    $data['_epl_payment_date'] = current_time( 'mysql' );
                    $data['_epl_payment_method'] = '';
                    $data['_epl_transaction_id'] = '';


                    $this->rm->update_payment_data( $data );
                }


                $data['mode'] = 'overview';
                $data['overview'] = $this->epl->load_view( 'front/registration/regis-thank-you-page', $data );


                $email_body = $this->epl->load_view( 'front/registration/regis-confirm-email', $data, true );

                $this->send_confirmation_email( $email_body );
            }



            $_SESSION['__epl'] = array( );
            session_regenerate_id();
        }


        function _exp_checkout_payment_cancel() {

            $this->show_cart_overview();
        }


        function _exp_checkout_payment_success() {

            $egp = $this->epl->load_model( 'epl-gateway-model' );

            if ( $egp->_exp_checkout_payment_success() ) {

                $this->rm->set_mode( 'overview' );
                $data['message'] = "Please review and Finalize your payment.";
                $data['cart_data'] = $this->rm->show_cart();
                $data['mode'] = 'overview';
                $data['content'] = $this->epl->load_view( 'front/cart/cart', $data, false );


                $data['content'] .= $this->rm->regis_form();


                $data['form_action'] = esc_url(add_query_arg( 'epl_action', '_exp_checkout_do_payment', $_SERVER['REQUEST_URI'] ) );

                $data['next_step'] = '_exp_checkout_do_payment';
                $data['next_step_label'] = 'Confirm Payment and Finish';

                $this->epl->load_view( 'front/cart-container', $data );
            }
            else {
                echo "Sorry, something must have gone wrong.  Please notify the site administrtor";
            }
            //$this->show_cart_overview( '_exp_checkout_do_payment' );
        }


        function _exp_checkout_do_payment() {
            //this sets the event details, for now
            $this->rm->set_mode( 'overview' );
            $data['cart_data'] = $this->rm->show_cart();

            $egp = $this->epl->load_model( 'epl-gateway-model' );


            $r = $egp->_exp_checkout_do_payment();

            if ( $r === true ) {

                $this->thank_you_page();
            }
            else {
                echo $r;
            }
        }


        function validate_data() {
            $v = $_SESSION['events_planner']['POST_EVENT_VARS'];

            foreach ( ( array ) $v['epl_start_date'] as $event_id => $event_dates ) {
                
            }
        }


        function send_confirmation_email( $body ) {
            global $organization_details, $customer_email;

            $data = array( );

            $_email = get_bloginfo( 'admin_email' );


            $headers = 'From: ' . get_bloginfo( 'name' ) . " <{$_email}>" . "\r\n" .
                    'Reply-To: ' . "<{$_email}>" . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();


            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            if ( (isset( $customer_email )) && $customer_email != '' )
                @wp_mail( $customer_email, epl__( 'Registration Confirmation' ) . ': ' . get_the_event_title(), $body, $headers );

            @wp_mail( $_email, epl__( 'New Registration' ) . ': ' . get_the_event_title(), $body, $headers );
        }

    }

}