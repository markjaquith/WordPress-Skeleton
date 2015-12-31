<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class NF_Upgrade_Submissions extends NF_Upgrade
{
    public $name = 'submissions';

    public $priority = '2.7';

    public $description = 'The new submission database allows submissions to be stored and retrieved more efficiently. It also allows for much better submission searching.';

    public $args = array();

    public $errors = array();

    public function loading()
    {
        $old_sub_count = $this->countOldSubs();

        $this->total_steps = round( ( $old_sub_count / 100 ), 0 );

        if ( ! $this->total_steps || 1 > $this->total_steps ) {
            $this->total_steps = 1;
        }
    }

    public function _beforeStep( $step )
    {
        if ( get_option( 'nf_convert_subs_num' ) ) {
            $this->args['number'] = get_option( 'nf_convert_subs_num' );
        }

        $this->args['form_id']  = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;

        update_option( 'nf_convert_subs_step', $step );
    }

    public function step( $step )
    {
        $begin = ( $step - 1 ) * 100;

        $subs_results = $this->getOldSubs( $begin, 100 );

        if ( is_array( $subs_results ) && ! empty( $subs_results ) ) {

            foreach ( $subs_results as $sub ) {
                if ( $this->args['form_id'] != $sub['form_id'] ) {
                    $this->args['form_id'] = $sub['form_id'];
                    $number = 1;
                }
                $converted = get_option( 'nf_converted_subs' );
                if ( empty( $converted ) )
                    $converted = array();

                if ( ! in_array( $sub['id'], $converted ) ) {
                    $this->convert( $sub, $number );

                    $converted[] = $sub['id'];
                    update_option( 'nf_converted_subs', $converted );
                    $number++;
                    update_option( 'nf_convert_subs_num', $number );
                }
            }
        }
    }

    public function _afterStep( $step )
    {

    }

    public function complete()
    {
        update_option( 'nf_convert_subs_step', 'complete' );
        delete_option( 'nf_convert_subs_num' );
    }

    public function isComplete()
    {
        return get_option( 'nf_convert_subs_step', false );
    }

    /*
     * PRIVATE METHODS
     */

    private function getOldSubs( $begin = '', $count = '' ) {
        global $wpdb;

        if ( $begin == '' && $count == '' ) {
            $limit = '';
        } else {
            $limit = ' LIMIT ' . $begin . ',' . $count;
        }
        $subs_results = $wpdb->get_results( 'SELECT * FROM ' . NINJA_FORMS_SUBS_TABLE_NAME . ' WHERE `action` != "mp_save" ORDER BY `form_id` ASC, `id` ASC ' . $limit, ARRAY_A );
        //Now that we have our sub results, let's loop through them and remove any that don't match our args array.
        if( is_array( $subs_results ) AND ! empty( $subs_results ) ) {
            foreach( $subs_results as $key => $val ) { //Initiate a loop that will run for all of our submissions.
                //Set our $data variable. This variable contains an array that looks like: array('field_id' => 13, 'user_value' => 'Hello World!').
                if( is_serialized( $subs_results[$key]['data'] ) ) {
                    $subs_results[ $key ]['data'] = unserialize( $subs_results[ $key ]['data'] );
                }
            }
        }
        return $subs_results;
    }

    private function countOldSubs() {
        global $wpdb;
        $count = $wpdb->get_results( 'SELECT COUNT(*) FROM '. NINJA_FORMS_SUBS_TABLE_NAME . ' WHERE `action` != "mp_save"', ARRAY_A );
        if ( is_array ( $count ) && ! empty ( $count ) ) {
            return $count[0]['COUNT(*)'];
        } else {
            return false;
        }
    }

    public function convert( $sub, $num ) {

        if ( isset ( $sub['id'] ) ) {
            $old_id = $sub['id'];
            unset( $sub['id'] );
        }

        if ( isset ( $sub['form_id'] ) ) {
            $form_id = $sub['form_id'];
            unset ( $sub['form_id'] );
        }

        if ( isset ( $sub['action'] ) ) {
            $action = $sub['action'];
            unset ( $sub['action'] );
        }

        if ( isset ( $sub['user_id'] ) ) {
            $user_id = $sub['user_id'];
            unset ( $sub['user_id'] );
        }

        if ( isset ( $sub['date_updated'] ) ) {
            $date_updated = $sub['date_updated'];
            unset ( $sub['date_updated'] );
        }

        if ( isset ( $sub['status'] ) )
            unset ( $sub['status'] );

        if ( isset ( $sub['saved'] ) )
            unset ( $sub['saved'] );

        $sub_id = Ninja_Forms()->subs()->create( $form_id );
        Ninja_Forms()->sub( $sub_id )->update_action( $action );
        Ninja_Forms()->sub( $sub_id )->update_user_id( $user_id );
        Ninja_Forms()->sub( $sub_id )->update_seq_num( $num );
        Ninja_Forms()->sub( $sub_id )->update_date_submitted( $date_updated );
        Ninja_Forms()->sub( $sub_id )->update_date_modified( $date_updated );
        Ninja_Forms()->sub( $sub_id )->add_meta( '_old_id', $old_id );

        if ( isset ( $sub['data'] ) ) {
            foreach ( $sub['data'] as $data ) {
                $field_id = $data['field_id'];
                $value = $data['user_value'];
                Ninja_Forms()->sub( $sub_id )->add_field( $field_id, $value );
            }
            unset ( $sub['data'] );
        }

        if ( ! empty ( $sub ) ) {
            foreach ( $sub as $key => $value ) {
                if ( $value !== '' ) {
                    Ninja_Forms()->sub( $sub_id )->add_meta( '_' . $key, $value );
                }
            }
        }
    }
}