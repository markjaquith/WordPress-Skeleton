<?php

class EPL_recurrence_model extends EPL_Model {


    function __construct() {
        parent::__construct();
        epl_log( 'init', get_class() . ' initialized.', 1 );
        $this->dates = array( );
    }


    function recurrence_dates_from_post( $fields, $values, $r_mode ) {

        $this->fields = $fields;

        $this->values = $values;
        $this->r_mode = $r_mode;

        $this->type = $_POST['_epl_recurrence_frequency'];
        $this->interval = $_POST['_epl_recurrence_interval'];

        //if a class, that has many days for the same event
        if ( current( ( array ) $_POST['_epl_event_type'] ) == 10 ) {

            $this->rec_start_date = date( "Y-m-d", strtotime( epl_dmy_convert( current( ( array ) $_POST['_epl_start_date'] ) ) ) );
            $rec_first_end_date = date( "Y-m-d", strtotime( epl_dmy_convert( current( ( array ) $_POST['_epl_start_date'] ) ) ) );

            $this->ind_event_length = $this->get_date_difference( $this->rec_start_date, $rec_first_end_date, 'day' );

            $this->recurrence_end_date = date( "Y-m-d", strtotime( epl_dmy_convert( current( ( array ) $_POST['_epl_end_date'] ) ) ) );
        }
        else {

            $this->rec_start_date = date( "Y-m-d", strtotime( epl_dmy_convert( $_POST['_epl_rec_first_start_date'] ) ) );
            $rec_first_end_date = date( "Y-m-d", strtotime( epl_dmy_convert( $_POST['_epl_rec_first_end_date'] ) ) );

            $this->ind_event_length = $this->get_date_difference( $this->rec_start_date, $rec_first_end_date, 'day' );

            // $epl_rec_first_end_date = date( "Y-m-d", strtotime( epl_dmy_convert($_POST['_epl_rec_first_end_date'] ) ));

            $this->recurrence_end_date = date( "Y-m-d", strtotime( epl_dmy_convert( $_POST['_epl_recurrence_end'] ) ) );
            $first_day = date( "d", strtotime( $this->rec_start_date ) );
        }


        $this->week_of_first_day = $this->get_week_of_the_month( $this->rec_start_date );



        //Find the difference between the start and end dates, based on the selected type (i.e. day, week, month
        $this->difference = $this->get_date_difference( $this->rec_start_date, $this->recurrence_end_date, $this->type );

        //weekdays are used for daily and weekly types
        $this->weekdays = (isset( $_POST['_epl_recurrence_weekdays'] ) && is_array( $_POST['_epl_recurrence_weekdays'] )) ? $_POST['_epl_recurrence_weekdays'] : array( );

        //make a timestamp of the start date
        $_start_date = explode( '-', $this->rec_start_date );

        $this->date_to_time = mktime( 0, 0, 0, $_start_date[1], $_start_date[2], $_start_date[0] );

        //will hold the dates that the formula constructs

        /*
         *  from first day to the last
         */


        $this->get_the_dates();

        //epl_log('debug', "<pre>" . print_r($this->dates, true ) . "</pre>");

        if ( $this->r_mode == 'recurrence_preview' )
            $r = $this->epl_util->construct_calendar( $this->dates ); // . $this->construct_table_array( $dates );
        else
            $r = $this->construct_table_array( $this->dates );

        return $r;
    }

    /*
     * for a specific event
     */


    function recurrence_dates_from_db( $event_data ) {

        $this->dates = array( );
        if ( current( ( array ) $event_data['_epl_event_type'] ) == 0 )
            return;

        /*
         * need the start date
         * need the end date
         *
         * recurrence scheme
         *
         *
         */

        $this->type = $event_data['_epl_recurrence_frequency'];
        $this->interval = $event_data['_epl_recurrence_interval'];

        //if a class, that has many days for the same event
        if ( current( ( array ) $event_data['_epl_event_type'] ) == 10 ) {

            $this->rec_start_date = date( "Y-m-d", strtotime( epl_dmy_convert(current( ( array ) $event_data['_epl_start_date'] ) ) ));
            $rec_first_end_date = date( "Y-m-d", strtotime( epl_dmy_convert(current( ( array ) $event_data['_epl_start_date'] ) ) ));

            $this->ind_event_length = $this->get_date_difference( $this->rec_start_date, $rec_first_end_date, 'day' );

            $this->recurrence_end_date = date( "Y-m-d", strtotime( epl_dmy_convert(current( ( array ) $event_data['_epl_end_date'] ) ) ));
        }
        else {

            $this->rec_start_date = date( "Y-m-d", strtotime( epl_dmy_convert($event_data['_epl_rec_first_start_date'] ) ));
            $rec_first_end_date = date( "Y-m-d", strtotime( epl_dmy_convert($event_data['_epl_rec_first_end_date'] ) ));

            $this->ind_event_length = $this->get_date_difference( $this->rec_start_date, $rec_first_end_date, 'day' );

            $epl_rec_first_end_date = date( "Y-m-d", strtotime( epl_dmy_convert($event_data['_epl_rec_first_start_date'] ) ));

            $this->recurrence_end_date = date( "Y-m-d", strtotime( epl_dmy_convert($event_data['_epl_recurrence_end'] ) ));
            $first_day = date( "d", strtotime( $this->rec_start_date ) );
        }


        $this->week_of_first_day = $this->get_week_of_the_month( $this->rec_start_date );

        //Find the difference between the start and end dates, based on the selected type (i.e. day, week, month
        $this->difference = $this->get_date_difference( $this->rec_start_date, $this->recurrence_end_date, $this->type );

        //weekdays are used for daily and weekly types
        $this->weekdays = (isset( $event_data['_epl_recurrence_weekdays'] ) && is_array( $event_data['_epl_recurrence_weekdays'] )) ? $event_data['_epl_recurrence_weekdays'] : array( );

        //make a timestamp of the start date
        $_start_date = explode( '-', $this->rec_start_date );

        $this->date_to_time = mktime( 0, 0, 0, $_start_date[1], $_start_date[2], $_start_date[0] );

        //will hold the dates that the formula constructs

        /*
         *  from first day to the last
         */

        $this->get_the_dates();


        return $this->dates;
    }


    function get_the_dates() {


        for ( $i = 0; $i <= $this->difference; $i = $i + $this->interval ) {

            //formula for determining the next date in the seried
            $new_date = '+ ' . $i . " $this->type";
            $new_unix_time = strtotime( $new_date, $this->date_to_time );


            //Get the date information for later use.
            $date_parts = getdate( $new_unix_time );
            //formatted new date
            $new_date = date( 'Y-m-d', $new_unix_time );

            //grab the year of the new date
            $newdate_year = $date_parts['year'];
            //grab month of the new date
            $newdate_month = $date_parts['mon'];
            //grab the day of the month
            $j = $date_parts['mday'];

            //put the new date in the array
            $this->dates[$newdate_year][$newdate_month][$j] = $new_date;

            //For daily and weekly, give the user tha ability to select on which days of the week the event occurs
            if ( is_array( $this->weekdays ) && !empty( $this->weekdays ) && $this->type != 'month' ) {

                if ( $this->type == 'day' ) {

                    //get the week number of the new date
                    $new_weekday = $date_parts['wday'];

                    //if the weeknumber of the new date is not in the weekdays that the user wants, unset the new date
                    if ( !in_array( $new_weekday, $this->weekdays ) )
                        unset( $this->dates[$newdate_year][$newdate_month][$j] );
                } elseif ( $this->type == 'week' ) {
                    /*
                     * For weekly, we need to find all the days of the week of the new date
                     * and then determine if they are in the array.  This get_days_of_a_week takes
                     * care of it.
                     * NOTICE the $dates passed by reference
                     */

                    $this->get_days_of_a_week( $new_date, $this->rec_start_date, $this->recurrence_end_date, $this->weekdays );
                }
            }
            elseif ( $this->type == 'month' && $_POST['_epl_recurrence_repeat_by'] == 10 ) {
                /*
                 * If it is monthly and repeats on a specific day of week (e.g. third Wednesday of each month),
                 * we need to find which weekday the start date belongs to and go from there.
                 */
            }
        }

        //return $this->dates;
    }


    function construct_dates_blueprint( $data ) {

    }


    function construct_table_array( $dates ) {
        $r = array( );
        $date_format = get_option( 'date_format' );


        /*
         * need to know registration start and end dates
         * need to know the constructed dates
         *
         */
        //epl_log( "debug", "<pre>" . print_r( $dates, true ) . "</pre>" );

        foreach ( $dates as $year => $month ) {

            foreach ( $month as $_month => $_days ) {

                foreach ( $_days as $k => $v ) {
                    $_dd = strtotime( $v );
                    $_d = date( $date_format, $_dd );


                    //preserving the keys for dates that have already been saved.
                    //Just in case there may be a registration that has been assigned to that date id
                    $_u_k = array_search( $_d, ( array ) $this->values['_epl_start_date'] );
                    if ( $_u_k === FALSE ) {

                        $_u_k = $this->epl_util->make_unique_id();
                    }

                    $data['_epl_start_date'][$_u_k] = $_d;
                    $data['_epl_end_date'][$_u_k] = date( $date_format, strtotime( "+{$this->ind_event_length} day", $_dd ) );

                    if ( isset( $_POST['_epl_rec_regis_start_date'] ) && $_POST['_epl_rec_regis_start_date'] != '' ) {

                        $data['_epl_regis_start_date'][$_u_k] = date( $date_format, strtotime( epl_dmy_convert($_POST['_epl_rec_regis_start_date'] )) );
                    }
                    elseif ( isset( $_POST['_epl_rec_regis_start_days_before_start_date'] ) && $_POST['_epl_rec_regis_start_days_before_start_date'] != '' ) {

                        $data['_epl_regis_start_date'][$_u_k] = date( $date_format, strtotime( "-" . ( int ) $_POST['_epl_rec_regis_start_days_before_start_date'] . " day", $_dd ) );
                    }

                    if ( isset( $_POST['_epl_rec_regis_end_date'] ) && $_POST['_epl_rec_regis_end_date'] != '' ) {

                        $data['_epl_regis_end_date'][$_u_k] = date( $date_format, strtotime( epl_dmy_convert($_POST['_epl_rec_regis_end_date'] ) ) );
                    }
                    elseif ( isset( $_POST['_epl_rec_regis_end_days_before_start_date'] ) && $_POST['_epl_rec_regis_end_days_before_start_date'] != '' ) {

                        $data['_epl_regis_end_date'][$_u_k] = date( $date_format, strtotime( "-" . ( int ) $_POST['_epl_rec_regis_end_days_before_start_date'] . " day", $_dd ) );
                    }
                }
            }
        }

        //epl_log( "debug", "<pre>" . print_r($data, true ) . "</pre>" );



        $rows_to_display = count( $data['_epl_start_date'] );
        $fields_to_display = array_keys( $this->fields['epl_date_fields'] );

        $_field_args = array(
            'section' => $this->fields['epl_date_fields'],
            'fields_to_display' => $fields_to_display,
            'meta' => array( '_view' => 1, '_type' => 'row', '_rows' => $rows_to_display, 'value' => $data )
        );

        $data['date_fields'] = $this->epl_util->render_fields( $_field_args );
        //epl_log( "<pre>" . print_r($data['date_fields'], true ) . "</pre>", 1 );
        //echo "<pre class='prettyprint'>" . print_r($data['date_fields'], true). "</pre>";

        $r = $this->epl->load_view( 'admin/events/dates-section', $data, true );

        return $r;
    }


    function get_date_difference( $start_date, $end_date, $format = 'day' ) {

        if ( $start_date == '' || $end_date == '' )
            return false;

        /*
         * Can't remember where I got this.  Would love to credit the author.
         */

        $startdate = explode( "-", $start_date );

        $enddate = explode( "-", $end_date );

        $seconds_difference = mktime( 0, 0, 0, $enddate[1], $enddate[2], $enddate[0] ) - mktime( 0, 0, 0, $startdate[1], $startdate[2], $startdate[0] );

        switch ( $format )
        {

            case 'minute': // Difference in Minutes
                return floor( $seconds_difference / 60 );

            case 'hour': // Difference in Hours
                return floor( $seconds_difference / 60 / 60 );

            case 'day': // Difference in Days
                return floor( $seconds_difference / 60 / 60 / 24 );

            case 'week': // Difference in Weeks
                return floor( $seconds_difference / 60 / 60 / 24 / 7 );

            case 'month': // Difference in Months
                //return floor( $seconds_difference / 60 / 60 / 24 / 7 / 4 );
                $diff = abs( strtotime( $end_date ) - strtotime( $start_date ) );

                $years = floor( $diff / (365 * 60 * 60 * 24) );

                return floor( ($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24) );

            default: // Difference in Years
                return floor( $seconds_difference / 365 / 60 / 60 / 24 );
        }
    }


    function get_week_of_the_month( $date ) {

        $d = date( 'j', $date );

        $w = date( 'w', $date ) + 1; //add 1 because date returns value between 0 to 6
        $dt = (floor( $d % 7 ) != 0) ? floor( $d % 7 ) : 7;
        $k = (($w - $dt) < 0) ? 7 + ($w - $dt) : $w - $dt;

        $W = ceil( ($d + $k) / 7 );
        return $W;
    }


    /**
     * Returns an array of days in a certain week
     *
     * When a date is passed to the function, the weekday number of that day is calculated.
     * Then, looping from 0-6 (Sun - Sat), the dates of that week are calculated.
     *
     * @since 1.0.0
     * @param date $date Ex. 2011-01-01
     * @param date $startdate If passed, will only return dates after that day. Ex. 2011-01-01
     * @param array $weekdays From the recurrence form, the checkboxes corresponding to the weekdays.
     * @return string
     */
    function get_days_of_a_week( $date, $start_date = 0, $end_date = 0, $weekdays = array( ) ) {

        $date = strtotime( epl_dmy_convert( $date ) );
        $start_date = strtotime( epl_dmy_convert( $start_date ) );
        $end_date = strtotime( epl_dmy_convert( $end_date ) );

        //usingn this method instead of date("W") since the latter method uses Monday as start of week
        $year = date( "Y", $date );
        $jan1 = gmmktime( 0, 0, 0, 1, 1, $year );
        $mydate = gmmktime( 0, 0, 0, 11, 30, $year );
        $week_number = ( int ) (($date - $jan1) / (7 * 24 * 60 * 60)) + 1;

        // below, in the loop, for $n_d, the week number that is below 10 needs to be represented in 0# format
        if ( $week_number < 10 )
            $week_number = "0" . $week_number;

        //$week_number = date( "W", $date ); //Note that this number is derived with Monday as start of the week


        $_d = array( );
        for ( $day = 0; $day <= 6; $day++ ) {

            $n_d = strtotime( $year . "W" . $week_number . $day );

            $newdate_year = date( "Y", $n_d );
            $newdate_month = date( "n", $n_d );

            $j = date( "j", $n_d ); //day 1-31
            $w = date( "w", $n_d ); //day of the week 0-6

            if ( ($n_d >= $start_date && $n_d <= $end_date) && in_array( $w, $weekdays ) ) {
                $this->dates[$newdate_year][$newdate_month][$j] = date( 'Y-m-d', $n_d );
            }
            else {
                unset( $this->dates[$newdate_year][$newdate_month][$j] );
            }
        }
    }

}

?>
