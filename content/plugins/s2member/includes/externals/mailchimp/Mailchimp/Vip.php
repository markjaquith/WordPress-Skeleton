<?php

class Mailchimp_Vip {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Retrieve all Activity (opens/clicks) for VIPs over the past 10 days
     * @return array structs for each activity recorded.
     *     - action string The action taken - either "open" or "click"
     *     - timestamp string The datetime the action occurred in GMT
     *     - url string IF the action is a click, the url that was clicked
     *     - unique_id string The campaign_id of the List the Member appears on
     *     - title string The campaign title
     *     - list_name string The name of the List the Member appears on
     *     - list_id string The id of the List the Member appears on
     *     - email string The email address of the member
     *     - fname string IF a FNAME merge field exists on the list, that value for the member
     *     - lname string IF a LNAME merge field exists on the list, that value for the member
     *     - member_rating int the rating of the subscriber. This will be 1 - 5 as described <a href="http://eepurl.com/f-2P" target="_blank">here</a>
     *     - member_since string the datetime the member was added and/or confirmed
     *     - geo associative_array the geographic information if we have it. including:
     *         - latitude string the latitude
     *         - longitude string the longitude
     *         - gmtoff string GMT offset
     *         - dstoff string GMT offset during daylight savings (if DST not observered, will be same as gmtoff
     *         - timezone string the timezone we've place them in
     *         - cc string 2 digit ISO-3166 country code
     *         - region string generally state, province, or similar
     */
    public function activity() {
        $_params = array();
        return $this->master->call('vip/activity', $_params);
    }

    /**
     * Add VIPs (previously called Golden Monkeys)
     * @param string $id
     * @param array $emails
     *     - email string an email address - for new subscribers obviously this should be used
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @return associative_array of data and success/error counts
     *     - success_count int the number of successful adds
     *     - error_count int the number of unsuccessful adds
     *     - errors array array of error structs including:
     *         - email associative_array whatever was passed in the email parameter
     *             - email string the email address added
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     *         - code string the error code
     *         - error string the error message
     *     - data array array of structs for each member added
     *         - email associative_array whatever was passed in the email parameter
     *             - email string the email address added
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     */
    public function add($id, $emails) {
        $_params = array("id" => $id, "emails" => $emails);
        return $this->master->call('vip/add', $_params);
    }

    /**
     * Remove VIPs - this does not affect list membership
     * @param string $id
     * @param array $emails
     *     - email string an email address - for new subscribers obviously this should be used
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @return associative_array of data and success/error counts
     *     - success_count int the number of successful deletions
     *     - error_count int the number of unsuccessful deletions
     *     - errors array array of error structs including:
     *         - email associative_array whatever was passed in the email parameter
     *             - email string the email address
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     *         - code string the error code
     *         - msg string the error message
     *     - data array array of structs for each member deleted
     *         - email associative_array whatever was passed in the email parameter
     *             - email string the email address
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     */
    public function del($id, $emails) {
        $_params = array("id" => $id, "emails" => $emails);
        return $this->master->call('vip/del', $_params);
    }

    /**
     * Retrieve all Golden Monkey(s) for an account
     * @return array structs for each Golden Monkey, including:
     *     - list_id string The id of the List the Member appears on
     *     - list_name string The name of the List the Member appears on
     *     - email string The email address of the member
     *     - fname string IF a FNAME merge field exists on the list, that value for the member
     *     - lname string IF a LNAME merge field exists on the list, that value for the member
     *     - member_rating int the rating of the subscriber. This will be 1 - 5 as described <a href="http://eepurl.com/f-2P" target="_blank">here</a>
     *     - member_since string the datetime the member was added and/or confirmed
     */
    public function members() {
        $_params = array();
        return $this->master->call('vip/members', $_params);
    }

}


