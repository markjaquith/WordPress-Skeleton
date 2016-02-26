<?php

class Mailchimp_Lists {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Get all email addresses that complained about a campaign sent to a list
     * @param string $id
     * @param int $start
     * @param int $limit
     * @param string $since
     * @return associative_array the total of all reports and the specific reports reports this page
     *     - total int the total number of matching abuse reports
     *     - data array structs for the actual data for each reports, including:
     *         - date string date+time the abuse report was received and processed
     *         - email string the email address that reported abuse
     *         - campaign_id string the unique id for the campaign that report was made against
     *         - type string an internal type generally specifying the originating mail provider - may not be useful outside of filling report views
     */
    public function abuseReports($id, $start=0, $limit=500, $since=null) {
        $_params = array("id" => $id, "start" => $start, "limit" => $limit, "since" => $since);
        return $this->master->call('lists/abuse-reports', $_params);
    }

    /**
     * Access up to the previous 180 days of daily detailed aggregated activity stats for a given list. Does not include AutoResponder activity.
     * @param string $id
     * @return array of structs containing daily values, each containing:
     */
    public function activity($id) {
        $_params = array("id" => $id);
        return $this->master->call('lists/activity', $_params);
    }

    /**
     * Subscribe a batch of email addresses to a list at once. If you are using a serialized version of the API, we strongly suggest that you
only run this method as a POST request, and <em>not</em> a GET request. Maximum batch sizes vary based on the amount of data in each record,
though you should cap them at 5k - 10k records, depending on your experience. These calls are also long, so be sure you increase your timeout values.
     * @param string $id
     * @param array $batch
     *     - email associative_array a struct with one of the following keys - failing to provide anything will produce an error relating to the email address. Provide multiples and we'll use the first we see in this same order.
     *         - email string an email address
     *         - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *         - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     *     - email_type string for the email type option (html or text)
     *     - merge_vars associative_array data for the various list specific and special merge vars documented in lists/subscribe
     * @param boolean $double_optin
     * @param boolean $update_existing
     * @param boolean $replace_interests
     * @return associative_array struct of result counts and associated data
     *     - add_count int Number of email addresses that were successfully added
     *     - adds array array of structs for each add
     *         - email string the email address added
     *         - euid string the email unique id
     *         - leid string the list member's truly unique id
     *     - update_count int Number of email addresses that were successfully updated
     *     - updates array array of structs for each update
     *         - email string the email address added
     *         - euid string the email unique id
     *         - leid string the list member's truly unique id
     *     - error_count int Number of email addresses that failed during addition/updating
     *     - errors array array of error structs including:
     *         - email string whatever was passed in the batch record's email parameter
     *             - email string the email address added
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     *         - code int the error code
     *         - error string the full error message
     *         - row associative_array the row from the batch that caused the error
     */
    public function batchSubscribe($id, $batch, $double_optin=true, $update_existing=false, $replace_interests=true) {
        $_params = array("id" => $id, "batch" => $batch, "double_optin" => $double_optin, "update_existing" => $update_existing, "replace_interests" => $replace_interests);
        return $this->master->call('lists/batch-subscribe', $_params);
    }

    /**
     * Unsubscribe a batch of email addresses from a list
     * @param string $id
     * @param array $batch
     *     - email string an email address
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @param boolean $delete_member
     * @param boolean $send_goodbye
     * @param boolean $send_notify
     * @return array Array of structs containing results and any errors that occurred
     *     - success_count int Number of email addresses that were successfully removed
     *     - error_count int Number of email addresses that failed during addition/updating
     *     - errors array array of error structs including:
     *         - email string whatever was passed in the batch record's email parameter
     *             - email string the email address added
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     *         - code int the error code
     *         - error string the full error message
     */
    public function batchUnsubscribe($id, $batch, $delete_member=false, $send_goodbye=true, $send_notify=false) {
        $_params = array("id" => $id, "batch" => $batch, "delete_member" => $delete_member, "send_goodbye" => $send_goodbye, "send_notify" => $send_notify);
        return $this->master->call('lists/batch-unsubscribe', $_params);
    }

    /**
     * Retrieve the clients that the list's subscribers have been tagged as being used based on user agents seen. Made possible by <a href="http://user-agent-string.info" target="_blank">user-agent-string.info</a>
     * @param string $id
     * @return associative_array the desktop and mobile user agents in use on the list
     *     - desktop associative_array desktop user agents and percentages
     *         - penetration double the percent of desktop clients in use
     *         - clients array array of structs for each client including:
     *             - client string the common name for the client
     *             - icon string a url to an image representing this client
     *             - percent string percent of list using the client
     *             - members string total members using the client
     *     - mobile associative_array mobile user agents and percentages
     *         - penetration double the percent of mobile clients in use
     *         - clients array array of structs for each client including:
     *             - client string the common name for the client
     *             - icon string a url to an image representing this client
     *             - percent string percent of list using the client
     *             - members string total members using the client
     */
    public function clients($id) {
        $_params = array("id" => $id);
        return $this->master->call('lists/clients', $_params);
    }

    /**
     * Access the Growth History by Month in aggregate or for a given list.
     * @param string $id
     * @return array array of structs containing months and growth data
     *     - month string The Year and Month in question using YYYY-MM format
     *     - existing int number of existing subscribers to start the month
     *     - imports int number of subscribers imported during the month
     *     - optins int number of subscribers who opted-in during the month
     */
    public function growthHistory($id=null) {
        $_params = array("id" => $id);
        return $this->master->call('lists/growth-history', $_params);
    }

    /**
     * Get the list of interest groupings for a given list, including the label, form information, and included groups for each
     * @param string $id
     * @param bool $counts
     * @return array array of structs of the interest groupings for the list
     *     - id int The id for the Grouping
     *     - name string Name for the Interest groups
     *     - form_field string Gives the type of interest group: checkbox,radio,select
     *     - groups array Array structs of the grouping options (interest groups) including:
     *         - bit string the bit value - not really anything to be done with this
     *         - name string the name of the group
     *         - display_order string the display order of the group, if set
     *         - subscribers int total number of subscribers who have this group if "counts" is true. otherwise empty
     */
    public function interestGroupings($id, $counts=false) {
        $_params = array("id" => $id, "counts" => $counts);
        return $this->master->call('lists/interest-groupings', $_params);
    }

    /**
     * Add a single Interest Group - if interest groups for the List are not yet enabled, adding the first
group will automatically turn them on.
     * @param string $id
     * @param string $group_name
     * @param int $grouping_id
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function interestGroupAdd($id, $group_name, $grouping_id=null) {
        $_params = array("id" => $id, "group_name" => $group_name, "grouping_id" => $grouping_id);
        return $this->master->call('lists/interest-group-add', $_params);
    }

    /**
     * Delete a single Interest Group - if the last group for a list is deleted, this will also turn groups for the list off.
     * @param string $id
     * @param string $group_name
     * @param int $grouping_id
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function interestGroupDel($id, $group_name, $grouping_id=null) {
        $_params = array("id" => $id, "group_name" => $group_name, "grouping_id" => $grouping_id);
        return $this->master->call('lists/interest-group-del', $_params);
    }

    /**
     * Change the name of an Interest Group
     * @param string $id
     * @param string $old_name
     * @param string $new_name
     * @param int $grouping_id
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function interestGroupUpdate($id, $old_name, $new_name, $grouping_id=null) {
        $_params = array("id" => $id, "old_name" => $old_name, "new_name" => $new_name, "grouping_id" => $grouping_id);
        return $this->master->call('lists/interest-group-update', $_params);
    }

    /**
     * Add a new Interest Grouping - if interest groups for the List are not yet enabled, adding the first
grouping will automatically turn them on.
     * @param string $id
     * @param string $name
     * @param string $type
     * @param array $groups
     * @return associative_array with a single entry:
     *     - id int the new grouping id if the request succeeds, otherwise an error will be thrown
     */
    public function interestGroupingAdd($id, $name, $type, $groups) {
        $_params = array("id" => $id, "name" => $name, "type" => $type, "groups" => $groups);
        return $this->master->call('lists/interest-grouping-add', $_params);
    }

    /**
     * Delete an existing Interest Grouping - this will permanently delete all contained interest groups and will remove those selections from all list members
     * @param int $grouping_id
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function interestGroupingDel($grouping_id) {
        $_params = array("grouping_id" => $grouping_id);
        return $this->master->call('lists/interest-grouping-del', $_params);
    }

    /**
     * Update an existing Interest Grouping
     * @param int $grouping_id
     * @param string $name
     * @param string $value
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function interestGroupingUpdate($grouping_id, $name, $value) {
        $_params = array("grouping_id" => $grouping_id, "name" => $name, "value" => $value);
        return $this->master->call('lists/interest-grouping-update', $_params);
    }

    /**
     * Retrieve the locations (countries) that the list's subscribers have been tagged to based on geocoding their IP address
     * @param string $id
     * @return array array of locations
     *     - country string the country name
     *     - cc string the ISO 3166 2 digit country code
     *     - percent double the percent of subscribers in the country
     *     - total double the total number of subscribers in the country
     */
    public function locations($id) {
        $_params = array("id" => $id);
        return $this->master->call('lists/locations', $_params);
    }

    /**
     * Get the most recent 100 activities for particular list members (open, click, bounce, unsub, abuse, sent to, etc.)
     * @param string $id
     * @param array $emails
     *     - email string an email address - for new subscribers obviously this should be used
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @return associative_array of data and success/error counts
     *     - success_count int the number of subscribers successfully found on the list
     *     - error_count int the number of subscribers who were not found on the list
     *     - errors array array of error structs including:
     *         - email string whatever was passed in the email parameter
     *             - email string the email address added
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     *         - error string the error message
     *         - code string the error code
     *     - data array an array of structs where each activity record has:
     *         - email string whatever was passed in the email parameter
     *             - email string the email address added
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     *         - activity array an array of structs containing the activity, including:
     *             - action string The action name, one of: open, click, bounce, unsub, abuse, sent, queued, ecomm, mandrill_send, mandrill_hard_bounce, mandrill_soft_bounce, mandrill_open, mandrill_click, mandrill_spam, mandrill_unsub, mandrill_reject
     *             - timestamp string The date+time of the action (GMT)
     *             - url string For click actions, the url clicked, otherwise this is empty
     *             - type string If there's extra bounce, unsub, etc data it will show up here.
     *             - campaign_id string The campaign id the action was related to, if it exists - otherwise empty (ie, direct unsub from list)
     *             - campaign_data associative_array If not deleted, the campaigns/list data for the campaign
     */
    public function memberActivity($id, $emails) {
        $_params = array("id" => $id, "emails" => $emails);
        return $this->master->call('lists/member-activity', $_params);
    }

    /**
     * Get all the information for particular members of a list
     * @param string $id
     * @param array $emails
     *     - email string an email address - for new subscribers obviously this should be used
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @return associative_array of data and success/error counts
     *     - success_count int the number of subscribers successfully found on the list
     *     - error_count int the number of subscribers who were not found on the list
     *     - errors array array of error structs including:
     *         - email associative_array whatever was passed in the email parameter
     *             - email string the email address added
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     *         - error string the error message
     *     - data array array of structs for each valid list member
     *         - id string The unique id (euid) for this email address on an account
     *         - email string The email address associated with this record
     *         - email_type string The type of emails this customer asked to get: html or text
     *         - merges associative_array a struct containing a key for each merge tags and the data for those tags for this email address, plus:
     *             - GROUPINGS array if Interest groupings are enabled, this will exist with structs for each grouping:
     *                 - id int the grouping id
     *                 - name string the interest group name
     *                 - groups array structs for each group in the grouping
     *                     - name string the group name
     *                     - interested bool whether the member has this group selected
     *         - status string The subscription status for this email address, either pending, subscribed, unsubscribed, or cleaned
     *         - ip_signup string IP Address this address signed up from. This may be blank if single optin is used.
     *         - timestamp_signup string The date+time the double optin was initiated. This may be blank if single optin is used.
     *         - ip_opt string IP Address this address opted in from.
     *         - timestamp_opt string The date+time the optin completed
     *         - member_rating int the rating of the subscriber. This will be 1 - 5 as described <a href="http://eepurl.com/f-2P" target="_blank">here</a>
     *         - campaign_id string If the user is unsubscribed and they unsubscribed from a specific campaign, that campaign_id will be listed, otherwise this is not returned.
     *         - lists array An array of structs for the other lists this member belongs to
     *             - id string the list id
     *             - status string the members status on that list
     *         - timestamp string The date+time this email address entered it's current status
     *         - info_changed string The last time this record was changed. If the record is old enough, this may be blank.
     *         - web_id int The Member id used in our web app, allows you to create a link directly to it
     *         - leid int The Member id used in our web app, allows you to create a link directly to it
     *         - list_id string The list id the for the member record being returned
     *         - list_name string The list name the for the member record being returned
     *         - language string if set/detected, a language code from <a href="http://kb.mailchimp.com/article/can-i-see-what-languages-my-subscribers-use#code" target="_blank">here</a>
     *         - is_gmonkey bool Whether the member is a <a href="http://mailchimp.com/features/golden-monkeys/" target="_blank">Golden Monkey</a> or not.
     *         - geo associative_array the geographic information if we have it. including:
     *             - latitude string the latitude
     *             - longitude string the longitude
     *             - gmtoff string GMT offset
     *             - dstoff string GMT offset during daylight savings (if DST not observered, will be same as gmtoff)
     *             - timezone string the timezone we've place them in
     *             - cc string 2 digit ISO-3166 country code
     *             - region string generally state, province, or similar
     *         - clients associative_array the client we've tracked the address as using with two keys:
     *             - name string the common name of the client
     *             - icon_url string a url representing a path to an icon representing this client
     *         - static_segments array structs for each static segments the member is a part of including:
     *             - id int the segment id
     *             - name string the name given to the segment
     *             - added string the date the member was added
     *         - notes array structs for each note entered for this member. For each note:
     *             - id int the note id
     *             - note string the text entered
     *             - created string the date the note was created
     *             - updated string the date the note was last updated
     *             - created_by_name string the name of the user who created the note. This can change as users update their profile.
     */
    public function memberInfo($id, $emails) {
        $_params = array("id" => $id, "emails" => $emails);
        return $this->master->call('lists/member-info', $_params);
    }

    /**
     * Get all of the list members for a list that are of a particular status and potentially matching a segment. This will cause locking, so don't run multiples at once. Are you trying to get a dump including lots of merge
data or specific members of a list? If so, checkout the <a href="/export/1.0/list.func.php">List Export API</a>
     * @param string $id
     * @param string $status
     * @param associative_array $opts
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     *     - sort_field string optional the data field to sort by - mergeX (1-30), your custom merge tags, "email", "rating","last_update_time", or "optin_time" - invalid fields will be ignored
     *     - sort_dir string optional the direct - ASC or DESC. defaults to ASC (case insensitive)
     *     - segment associative_array a properly formatted segment that works with campaigns/segment-test
     * @return associative_array of the total records matched and limited list member data for this page
     *     - total int the total matching records
     *     - data array structs for each member as returned by member-info
     */
    public function members($id, $status='subscribed', $opts=array()) {
        $_params = array("id" => $id, "status" => $status, "opts" => $opts);
        return $this->master->call('lists/members', $_params);
    }

    /**
     * Add a new merge tag to a given list
     * @param string $id
     * @param string $tag
     * @param string $name
     * @param associative_array $options
     *     - field_type string optional one of: text, number, radio, dropdown, date, address, phone, url, imageurl, zip, birthday - defaults to text
     *     - req boolean optional indicates whether the field is required - defaults to false
     *     - public boolean optional indicates whether the field is displayed in public - defaults to true
     *     - show boolean optional indicates whether the field is displayed in the app's list member view - defaults to true
     *     - order int The order this merge tag should be displayed in - this will cause existing values to be reset so this fits
     *     - default_value string optional the default value for the field. See lists/subscribe() for formatting info. Defaults to blank - max 255 bytes
     *     - helptext string optional the help text to be used with some newer forms. Defaults to blank - max 255 bytes
     *     - choices array optional kind of - an array of strings to use as the choices for radio and dropdown type fields
     *     - dateformat string optional only valid for birthday and date fields. For birthday type, must be "MM/DD" (default) or "DD/MM". For date type, must be "MM/DD/YYYY" (default) or "DD/MM/YYYY". Any other values will be converted to the default.
     *     - phoneformat string optional "US" is the default - any other value will cause them to be unformatted (international)
     *     - defaultcountry string optional the <a href="http://s2member.com/r/iso-3166/" target="_blank">ISO 3166 2 digit character code</a> for the default country. Defaults to "US". Anything unrecognized will be converted to the default.
     * @return associative_array the full data for the new merge var, just like merge-vars returns
     *     - name string Name/description of the merge field
     *     - req bool Denotes whether the field is required (true) or not (false)
     *     - field_type string The "data type" of this merge var. One of: email, text, number, radio, dropdown, date, address, phone, url, imageurl
     *     - public bool Whether or not this field is visible to list subscribers
     *     - show bool Whether the field is displayed in thelist dashboard
     *     - order string The order this field displays in on forms
     *     - default string The default value for this field
     *     - helptext string The helptext for this field
     *     - size string The width of the field to be used
     *     - tag string The merge tag that's used for forms and lists/subscribe() and lists/update-member()
     *     - choices array the options available for radio and dropdown field types
     *     - id int an unchanging id for the merge var
     */
    public function mergeVarAdd($id, $tag, $name, $options=array()) {
        $_params = array("id" => $id, "tag" => $tag, "name" => $name, "options" => $options);
        return $this->master->call('lists/merge-var-add', $_params);
    }

    /**
     * Delete a merge tag from a given list and all its members. Seriously - the data is removed from all members as well!
Note that on large lists this method may seem a bit slower than calls you typically make.
     * @param string $id
     * @param string $tag
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function mergeVarDel($id, $tag) {
        $_params = array("id" => $id, "tag" => $tag);
        return $this->master->call('lists/merge-var-del', $_params);
    }

    /**
     * Completely resets all data stored in a merge var on a list. All data is removed and this action can not be undone.
     * @param string $id
     * @param string $tag
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function mergeVarReset($id, $tag) {
        $_params = array("id" => $id, "tag" => $tag);
        return $this->master->call('lists/merge-var-reset', $_params);
    }

    /**
     * Sets a particular merge var to the specified value for every list member. Only merge var ids 1 - 30 may be modified this way. This is generally a dirty method
unless you're fixing data since you should probably be using default_values and/or conditional content. as with lists/merge-var-reset(), this can not be undone.
     * @param string $id
     * @param string $tag
     * @param string $value
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function mergeVarSet($id, $tag, $value) {
        $_params = array("id" => $id, "tag" => $tag, "value" => $value);
        return $this->master->call('lists/merge-var-set', $_params);
    }

    /**
     * Update most parameters for a merge tag on a given list. You cannot currently change the merge type
     * @param string $id
     * @param string $tag
     * @param associative_array $options
     * @return associative_array the full data for the new merge var, just like merge-vars returns
     *     - name string Name/description of the merge field
     *     - req bool Denotes whether the field is required (true) or not (false)
     *     - field_type string The "data type" of this merge var. One of: email, text, number, radio, dropdown, date, address, phone, url, imageurl
     *     - public bool Whether or not this field is visible to list subscribers
     *     - show bool Whether the field is displayed in thelist dashboard
     *     - order string The order this field to displays in on forms
     *     - default string The default value for this field
     *     - helptext string The helptext for this field
     *     - size string The width of the field to be used
     *     - tag string The merge tag that's used for forms and lists/subscribe() and lists/update-member()
     *     - choices array the options available for radio and dropdown field types
     *     - id int an unchanging id for the merge var
     */
    public function mergeVarUpdate($id, $tag, $options) {
        $_params = array("id" => $id, "tag" => $tag, "options" => $options);
        return $this->master->call('lists/merge-var-update', $_params);
    }

    /**
     * Get the list of merge tags for a given list, including their name, tag, and required setting
     * @param array $id
     * @return associative_array of data and success/error counts
     *     - success_count int the number of subscribers successfully found on the list
     *     - error_count int the number of subscribers who were not found on the list
     *     - data array of structs for the merge tags on each list
     *         - id string the list id
     *         - name string the list name
     *         - merge_vars array of structs for each merge var
     *             - name string Name of the merge field
     *             - req bool Denotes whether the field is required (true) or not (false)
     *             - field_type string The "data type" of this merge var. One of the options accepted by field_type in lists/merge-var-add
     *             - public bool Whether or not this field is visible to list subscribers
     *             - show bool Whether the list owner has this field displayed on their list dashboard
     *             - order string The order the list owner has set this field to display in
     *             - default string The default value the list owner has set for this field
     *             - helptext string The helptext for this field
     *             - size string The width of the field to be used
     *             - tag string The merge tag that's used for forms and lists/subscribe() and listUpdateMember()
     *             - choices array For radio and dropdown field types, an array of the options available
     *             - id int an unchanging id for the merge var
     *     - errors array of error structs
     *         - id string the passed list id that failed
     *         - code int the resulting error code
     *         - msg string the resulting error message
     */
    public function mergeVars($id) {
        $_params = array("id" => $id);
        return $this->master->call('lists/merge-vars', $_params);
    }

    /**
     * Retrieve all of Segments for a list.
     * @param string $id
     * @param string $type
     * @return associative_array with 2 keys:
     *     - static array of structs with data for each segment
     *         - id int the id of the segment
     *         - name string the name for the segment
     *         - created_date string the date+time the segment was created
     *         - last_update string the date+time the segment was last updated (add or del)
     *         - last_reset string the date+time the segment was last reset (ie had all members cleared from it)
     *     - saved array of structs with data for each segment
     *         - id int the id of the segment
     *         - name string the name for the segment
     *         - segment_opts string same match+conditions struct typically used
     *         - segment_text string a textual description of the segment match/conditions
     *     - created_date string the date+time the segment was created
     *     - last_update string the date+time the segment was last updated (add or del)
     */
    public function segments($id, $type=null) {
        $_params = array("id" => $id, "type" => $type);
        return $this->master->call('lists/segments', $_params);
    }

    /**
     * Save a segment against a list for later use. There is no limit to the number of segments which can be saved. Static Segments <strong>are not</strong> tied
to any merge data, interest groups, etc. They essentially allow you to configure an unlimited number of custom segments which will have standard performance.
When using proper segments, Static Segments are one of the available options for segmentation just as if you used a merge var (and they can be used with other segmentation
options), though performance may degrade at that point. Saved Segments (called "auto-updating" in the app) are essentially just the match+conditions typically
used.
     * @param string $id
     * @param associative_array $opts
     *     - type string either "static" or "saved"
     *     - name string a unique name per list for the segment - 100 byte maximum length, anything longer will throw an error
     *     - segment_opts associative_array for "saved" only, the standard segment match+conditions, just like campaigns/segment-test
     *         - match string "any" or "all"
     *         - conditions array structs for each condition, just like campaigns/segment-test
     * @return associative_array with a single entry:
     *     - id int the id of the new segment, otherwise an error will be thrown.
     */
    public function segmentAdd($id, $opts) {
        $_params = array("id" => $id, "opts" => $opts);
        return $this->master->call('lists/segment-add', $_params);
    }

    /**
     * Delete a segment. Note that this will, of course, remove any member affiliations with any static segments deleted
     * @param string $id
     * @param int $seg_id
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function segmentDel($id, $seg_id) {
        $_params = array("id" => $id, "seg_id" => $seg_id);
        return $this->master->call('lists/segment-del', $_params);
    }

    /**
     * Allows one to test their segmentation rules before creating a campaign using them - this is no different from campaigns/segment-test() and will eventually replace it.
For the time being, the crazy segmenting condition documentation will continue to live over there.
     * @param string $list_id
     * @param associative_array $options
     * @return associative_array with a single entry:
     *     - total int The total number of subscribers matching your segmentation options
     */
    public function segmentTest($list_id, $options) {
        $_params = array("list_id" => $list_id, "options" => $options);
        return $this->master->call('lists/segment-test', $_params);
    }

    /**
     * Update an existing segment. The list and type can not be changed.
     * @param string $id
     * @param int $seg_id
     * @param associative_array $opts
     *     - name string a unique name per list for the segment - 100 byte maximum length, anything longer will throw an error
     *     - segment_opts associative_array for "saved" only, the standard segment match+conditions, just like campaigns/segment-test
     *         - match string "any" or "all"
     *         - conditions array structs for each condition, just like campaigns/segment-test
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function segmentUpdate($id, $seg_id, $opts) {
        $_params = array("id" => $id, "seg_id" => $seg_id, "opts" => $opts);
        return $this->master->call('lists/segment-update', $_params);
    }

    /**
     * Save a segment against a list for later use. There is no limit to the number of segments which can be saved. Static Segments <strong>are not</strong> tied
to any merge data, interest groups, etc. They essentially allow you to configure an unlimited number of custom segments which will have standard performance.
When using proper segments, Static Segments are one of the available options for segmentation just as if you used a merge var (and they can be used with other segmentation
options), though performance may degrade at that point.
     * @param string $id
     * @param string $name
     * @return associative_array with a single entry:
     *     - id int the id of the new segment, otherwise an error will be thrown.
     */
    public function staticSegmentAdd($id, $name) {
        $_params = array("id" => $id, "name" => $name);
        return $this->master->call('lists/static-segment-add', $_params);
    }

    /**
     * Delete a static segment. Note that this will, of course, remove any member affiliations with the segment
     * @param string $id
     * @param int $seg_id
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function staticSegmentDel($id, $seg_id) {
        $_params = array("id" => $id, "seg_id" => $seg_id);
        return $this->master->call('lists/static-segment-del', $_params);
    }

    /**
     * Add list members to a static segment. It is suggested that you limit batch size to no more than 10,000 addresses per call. Email addresses must exist on the list
in order to be included - this <strong>will not</strong> subscribe them to the list!
     * @param string $id
     * @param int $seg_id
     * @param array $batch
     *     - email string an email address
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @return associative_array an array with the results of the operation
     *     - success_count int the total number of successful updates (will include members already in the segment)
     *     - errors array structs for each error including:
     *         - email string whatever was passed in the email parameter
     *             - email string the email address added
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     *         - code string the error code
     *         - error string the full error message
     */
    public function staticSegmentMembersAdd($id, $seg_id, $batch) {
        $_params = array("id" => $id, "seg_id" => $seg_id, "batch" => $batch);
        return $this->master->call('lists/static-segment-members-add', $_params);
    }

    /**
     * Remove list members from a static segment. It is suggested that you limit batch size to no more than 10,000 addresses per call. Email addresses must exist on the list
in order to be removed - this <strong>will not</strong> unsubscribe them from the list!
     * @param string $id
     * @param int $seg_id
     * @param array $batch
     *     - email string an email address
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @return associative_array an array with the results of the operation
     *     - success_count int the total number of successful removals
     *     - error_count int the total number of unsuccessful removals
     *     - errors array structs for each error including:
     *         - email string whatever was passed in the email parameter
     *             - email string the email address added
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     *         - code string the error code
     *         - error string the full error message
     */
    public function staticSegmentMembersDel($id, $seg_id, $batch) {
        $_params = array("id" => $id, "seg_id" => $seg_id, "batch" => $batch);
        return $this->master->call('lists/static-segment-members-del', $_params);
    }

    /**
     * Resets a static segment - removes <strong>all</strong> members from the static segment. Note: does not actually affect list member data
     * @param string $id
     * @param int $seg_id
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function staticSegmentReset($id, $seg_id) {
        $_params = array("id" => $id, "seg_id" => $seg_id);
        return $this->master->call('lists/static-segment-reset', $_params);
    }

    /**
     * Retrieve all of the Static Segments for a list.
     * @param string $id
     * @param boolean $get_counts
     * @param int $start
     * @param int $limit
     * @return array an of structs with data for each static segment
     *     - id int the id of the segment
     *     - name string the name for the segment
     *     - member_count int the total number of subscribed members currently in a segment
     *     - created_date string the date+time the segment was created
     *     - last_update string the date+time the segment was last updated (add or del)
     *     - last_reset string the date+time the segment was last reset (ie had all members cleared from it)
     */
    public function staticSegments($id, $get_counts=true, $start=0, $limit=null) {
        $_params = array("id" => $id, "get_counts" => $get_counts, "start" => $start, "limit" => $limit);
        return $this->master->call('lists/static-segments', $_params);
    }

    /**
     * Subscribe the provided email to a list. By default this sends a confirmation email - you will not see new members until the link contained in it is clicked!
     * @param string $id
     * @param array $email
     *     - email string an email address - for new subscribers obviously this should be used
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @param array $merge_vars
     *     - new-email string set this to change the email address. This is only respected on calls using update_existing or when passed to lists/update.
     *     - groupings array of Interest Grouping structs. Each should contain:
     *         - id int Grouping "id" from lists/interest-groupings (either this or name must be present) - this id takes precedence and can't change (unlike the name)
     *         - name string Grouping "name" from lists/interest-groupings (either this or id must be present)
     *         - groups array an array of valid group names for this grouping.
     *     - optin_ip string Set the Opt-in IP field. <em>Abusing this may cause your account to be suspended.</em> We do validate this and it must not be a private IP address.
     *     - optin_time string Set the Opt-in Time field. <em>Abusing this may cause your account to be suspended.</em> We do validate this and it must be a valid date. Use  - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00" to be safe. Generally, though, anything strtotime() understands we'll understand - <a href="http://us2.php.net/strtotime" target="_blank">http://us2.php.net/strtotime</a>
     *     - mc_location associative_array Set the member's geographic location either by optin_ip or geo data.
     *         - latitude string use the specified latitude (longitude must exist for this to work)
     *         - longitude string use the specified longitude (latitude must exist for this to work)
     *         - anything string if this (or any other key exists here) we'll try to use the optin ip. NOTE - this will slow down each subscribe call a bit, especially for lat/lng pairs in sparsely populated areas. Currently our automated background processes can and will overwrite this based on opens and clicks.
     *     - mc_language string Set the member's language preference. Supported codes are fully case-sensitive and can be found <a href="http://kb.mailchimp.com/article/can-i-see-what-languages-my-subscribers-use#code" target="_new">here</a>.
     *     - mc_notes array of structs for managing notes - it may contain:
     *         - note string the note to set. this is required unless you're deleting a note
     *         - id int the note id to operate on. not including this (or using an invalid id) causes a new note to be added
     *         - action string if the "id" key exists and is valid, an "update" key may be set to "append" (default), "prepend", "replace", or "delete" to handle how we should update existing notes. "delete", obviously, will only work with a valid "id" - passing that along with "note" and an invalid "id" is wrong and will be ignored.
     * @param string $email_type
     * @param bool $double_optin
     * @param bool $update_existing
     * @param bool $replace_interests
     * @param bool $send_welcome
     * @return array the ids for this subscriber
     *     - email string the email address added
     *     - euid string the email unique id
     *     - leid string the list member's truly unique id
     */
    public function subscribe($id, $email, $merge_vars=null, $email_type='html', $double_optin=true, $update_existing=false, $replace_interests=true, $send_welcome=false) {
        $_params = array("id" => $id, "email" => $email, "merge_vars" => $merge_vars, "email_type" => $email_type, "double_optin" => $double_optin, "update_existing" => $update_existing, "replace_interests" => $replace_interests, "send_welcome" => $send_welcome);
        return $this->master->call('lists/subscribe', $_params);
    }

    /**
     * Unsubscribe the given email address from the list
     * @param string $id
     * @param array $email
     *     - email string an email address
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @param boolean $delete_member
     * @param boolean $send_goodbye
     * @param boolean $send_notify
     * @return array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function unsubscribe($id, $email, $delete_member=false, $send_goodbye=true, $send_notify=true) {
        $_params = array("id" => $id, "email" => $email, "delete_member" => $delete_member, "send_goodbye" => $send_goodbye, "send_notify" => $send_notify);
        return $this->master->call('lists/unsubscribe', $_params);
    }

    /**
     * Edit the email address, merge fields, and interest groups for a list member. If you are doing a batch update on lots of users,
consider using lists/batch-subscribe() with the update_existing and possible replace_interests parameter.
     * @param string $id
     * @param associative_array $email
     *     - email string an email address
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @param array $merge_vars
     * @param string $email_type
     * @param boolean $replace_interests
     * @return associative_array the ids for this subscriber
     *     - email string the email address added
     *     - euid string the email unique id
     *     - leid string the list member's truly unique id
     */
    public function updateMember($id, $email, $merge_vars, $email_type='', $replace_interests=true) {
        $_params = array("id" => $id, "email" => $email, "merge_vars" => $merge_vars, "email_type" => $email_type, "replace_interests" => $replace_interests);
        return $this->master->call('lists/update-member', $_params);
    }

    /**
     * Add a new Webhook URL for the given list
     * @param string $id
     * @param string $url
     * @param associative_array $actions
     *     - subscribe bool optional as subscribes occur, defaults to true
     *     - unsubscribe bool optional as subscribes occur, defaults to true
     *     - profile bool optional as profile updates occur, defaults to true
     *     - cleaned bool optional as emails are cleaned from the list, defaults to true
     *     - upemail bool optional when  subscribers change their email address, defaults to true
     *     - campaign bool option when a campaign is sent or canceled, defaults to true
     * @param associative_array $sources
     *     - user bool optional user/subscriber initiated actions, defaults to true
     *     - admin bool optional admin actions in our web app, defaults to true
     *     - api bool optional actions that happen via API calls, defaults to false
     * @return associative_array with a single entry:
     *     - id int the id of the new webhook, otherwise an error will be thrown.
     */
    public function webhookAdd($id, $url, $actions=array(), $sources=array()) {
        $_params = array("id" => $id, "url" => $url, "actions" => $actions, "sources" => $sources);
        return $this->master->call('lists/webhook-add', $_params);
    }

    /**
     * Delete an existing Webhook URL from a given list
     * @param string $id
     * @param string $url
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function webhookDel($id, $url) {
        $_params = array("id" => $id, "url" => $url);
        return $this->master->call('lists/webhook-del', $_params);
    }

    /**
     * Return the Webhooks configured for the given list
     * @param string $id
     * @return array of structs for each webhook
     *     - url string the URL for this Webhook
     *     - actions associative_array the possible actions and whether they are enabled
     *         - subscribe bool triggered when subscribes happen
     *         - unsubscribe bool triggered when unsubscribes happen
     *         - profile bool triggered when profile updates happen
     *         - cleaned bool triggered when a subscriber is cleaned (bounced) from a list
     *         - upemail bool triggered when a subscriber's email address is changed
     *         - campaign bool triggered when a campaign is sent or canceled
     *     - sources associative_array the possible sources and whether they are enabled
     *         - user bool whether user/subscriber triggered actions are returned
     *         - admin bool whether admin (manual, in-app) triggered actions are returned
     *         - api bool whether api triggered actions are returned
     */
    public function webhooks($id) {
        $_params = array("id" => $id);
        return $this->master->call('lists/webhooks', $_params);
    }

    /**
     * Retrieve all of the lists defined for your user account
     * @param associative_array $filters
     *     - list_id string optional - return a single list using a known list_id. Accepts multiples separated by commas when not using exact matching
     *     - list_name string optional - only lists that match this name
     *     - from_name string optional - only lists that have a default from name matching this
     *     - from_email string optional - only lists that have a default from email matching this
     *     - from_subject string optional - only lists that have a default from email matching this
     *     - created_before string optional - only show lists that were created before this date+time  - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00"
     *     - created_after string optional - only show lists that were created since this date+time  - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00"
     *     - exact boolean optional - flag for whether to filter on exact values when filtering, or search within content for filter values - defaults to true
     * @param int $start
     * @param int $limit
     * @param string $sort_field
     * @param string $sort_dir
     * @return associative_array result of the operation including valid data and any errors
     *     - total int the total number of lists which matched the provided filters
     *     - data array structs for the lists which matched the provided filters, including the following
     *         - id string The list id for this list. This will be used for all other list management functions.
     *         - web_id int The list id used in our web app, allows you to create a link directly to it
     *         - name string The name of the list.
     *         - date_created string The date that this list was created.
     *         - email_type_option boolean Whether or not the List supports multiple formats for emails or just HTML
     *         - use_awesomebar boolean Whether or not campaigns for this list use the Awesome Bar in archives by default
     *         - default_from_name string Default From Name for campaigns using this list
     *         - default_from_email string Default From Email for campaigns using this list
     *         - default_subject string Default Subject Line for campaigns using this list
     *         - default_language string Default Language for this list's forms
     *         - list_rating double An auto-generated activity score for the list (0 - 5)
     *         - subscribe_url_short string Our eepurl shortened version of this list's subscribe form (will not change)
     *         - subscribe_url_long string The full version of this list's subscribe form (host will vary)
     *         - beamer_address string The email address to use for this list's <a href="http://kb.mailchimp.com/article/how-do-i-import-a-campaign-via-email-email-beamer/">Email Beamer</a>
     *         - visibility string Whether this list is Public (pub) or Private (prv). Used internally for projects like <a href="http://blog.mailchimp.com/introducing-wavelength/" target="_blank">Wavelength</a>
     *         - stats associative_array various stats and counts for the list - many of these are cached for at least 5 minutes
     *             - member_count double The number of active members in the given list.
     *             - unsubscribe_count double The number of members who have unsubscribed from the given list.
     *             - cleaned_count double The number of members cleaned from the given list.
     *             - member_count_since_send double The number of active members in the given list since the last campaign was sent
     *             - unsubscribe_count_since_send double The number of members who have unsubscribed from the given list since the last campaign was sent
     *             - cleaned_count_since_send double The number of members cleaned from the given list since the last campaign was sent
     *             - campaign_count double The number of campaigns in any status that use this list
     *             - grouping_count double The number of Interest Groupings for this list
     *             - group_count double The number of Interest Groups (regardless of grouping) for this list
     *             - merge_var_count double The number of merge vars for this list (not including the required EMAIL one)
     *             - avg_sub_rate double the average number of subscribe per month for the list (empty value if we haven't calculated this yet)
     *             - avg_unsub_rate double the average number of unsubscribe per month for the list (empty value if we haven't calculated this yet)
     *             - target_sub_rate double the target subscription rate for the list to keep it growing (empty value if we haven't calculated this yet)
     *             - open_rate double the average open rate per campaign for the list  (empty value if we haven't calculated this yet)
     *             - click_rate double the average click rate per campaign for the list  (empty value if we haven't calculated this yet)
     *         - modules array Any list specific modules installed for this list (example is SocialPro)
     *     - errors array structs of any errors found while loading lists - usually just from providing invalid list ids
     *         - param string the data that caused the failure
     *         - code int the error code
     *         - error string the error message
     */
    public function getList($filters=array(), $start=0, $limit=25, $sort_field='created', $sort_dir='DESC') {
        $_params = array("filters" => $filters, "start" => $start, "limit" => $limit, "sort_field" => $sort_field, "sort_dir" => $sort_dir);
        return $this->master->call('lists/list', $_params);
    }

}


