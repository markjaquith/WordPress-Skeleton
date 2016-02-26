<?php

class Mailchimp_Reports {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Get all email addresses that complained about a given campaign
     * @param string $cid
     * @param associative_array $opts
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     *     - since string optional pull only messages since this time - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00"
     * @return associative_array abuse report data for this campaign
     *     - total int the total reports matched
     *     - data array a struct for the each report, including:
     *         - date string date/time the abuse report was received and processed
     *         - member string the email address that reported abuse - will only contain email if the list or member has been removed
     *         - type string an internal type generally specifying the originating mail provider - may not be useful outside of filling report views
     */
    public function abuse($cid, $opts=array()) {
        $_params = array("cid" => $cid, "opts" => $opts);
        return $this->master->call('reports/abuse', $_params);
    }

    /**
     * Retrieve the text presented in our app for how a campaign performed and any advice we may have for you - best
suited for display in customized reports pages. Note: some messages will contain HTML - clean tags as necessary
     * @param string $cid
     * @return array of structs for advice on the campaign's performance, each containing:
     *     - msg string the advice message
     *     - type string the "type" of the message. one of: negative, positive, or neutral
     */
    public function advice($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('reports/advice', $_params);
    }

    /**
     * Retrieve the most recent full bounce message for a specific email address on the given campaign.
Messages over 30 days old are subject to being removed
     * @param string $cid
     * @param associative_array $email
     *     - email string an email address - this is recommended for this method
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @return associative_array the full bounce message for this email+campaign along with some extra data.
     *     - date string date the bounce was received and processed
     *     - member associative_array the member record as returned by lists/member-info()
     *     - message string the entire bounce message received
     */
    public function bounceMessage($cid, $email) {
        $_params = array("cid" => $cid, "email" => $email);
        return $this->master->call('reports/bounce-message', $_params);
    }

    /**
     * Retrieve the full bounce messages for the given campaign. Note that this can return very large amounts
of data depending on how large the campaign was and how much cruft the bounce provider returned. Also,
messages over 30 days old are subject to being removed
     * @param string $cid
     * @param associative_array $opts
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     *     - since string optional pull only messages since this time - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00"
     * @return associative_array data for the full bounce messages for this campaign
     *     - total int that total number of bounce messages for the campaign
     *     - data array structs containing the data for this page
     *         - date string date the bounce was received and processed
     *         - member associative_array the member record as returned by lists/member-info()
     *         - message string the entire bounce message received
     */
    public function bounceMessages($cid, $opts=array()) {
        $_params = array("cid" => $cid, "opts" => $opts);
        return $this->master->call('reports/bounce-messages', $_params);
    }

    /**
     * Return the list of email addresses that clicked on a given url, and how many times they clicked
     * @param string $cid
     * @param int $tid
     * @param associative_array $opts
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     *     - sort_field string optional the data to sort by - "clicked" (order clicks occurred, default) or "clicks" (total number of opens). Invalid fields will fall back on the default.
     *     - sort_dir string optional the direct - ASC or DESC. defaults to ASC (case insensitive)
     * @return associative_array containing the total records matched and the specific records for this page
     *     - total int the total number of records matched
     *     - data array structs for each email addresses that click the requested url
     *         - member associative_array the member record as returned by lists/member-info()
     *         - clicks int Total number of times the URL was clicked by this email address
     */
    public function clickDetail($cid, $tid, $opts=array()) {
        $_params = array("cid" => $cid, "tid" => $tid, "opts" => $opts);
        return $this->master->call('reports/click-detail', $_params);
    }

    /**
     * The urls tracked and their click counts for a given campaign.
     * @param string $cid
     * @return associative_array including:
     *     - total array structs for each url tracked for the full campaign
     *         - url string the url being tracked - urls are tracked individually, so duplicates can exist with vastly different stats
     *         - clicks int Number of times the specific link was clicked
     *         - clicks_percent double the percentage of total clicks "clicks" represents
     *         - unique int Number of unique people who clicked on the specific link
     *         - unique_percent double the percentage of unique clicks "unique" represents
     *         - tid int the tracking id used in campaign links - used primarily for reports/click-activity. also can be used to order urls by the order they appeared in the campaign to recreate our heat map.
     *     - a array if this was an absplit campaign, stat structs for the a group
     *         - url string the url being tracked - urls are tracked individually, so duplicates can exist with vastly different stats
     *         - clicks int Number of times the specific link was clicked
     *         - clicks_percent double the percentage of total clicks "clicks" represents
     *         - unique int Number of unique people who clicked on the specific link
     *         - unique_percent double the percentage of unique clicks "unique" represents
     *         - tid int the tracking id used in campaign links - used primarily for reports/click-activity. also can be used to order urls by the order they appeared in the campaign to recreate our heat map.
     *     - b array if this was an absplit campaign, stat structs for the b group
     *         - url string the url being tracked - urls are tracked individually, so duplicates can exist with vastly different stats
     *         - clicks int Number of times the specific link was clicked
     *         - clicks_percent double the percentage of total clicks "clicks" represents
     *         - unique int Number of unique people who clicked on the specific link
     *         - unique_percent double the percentage of unique clicks "unique" represents
     *         - tid int the tracking id used in campaign links - used primarily for reports/click-activity. also can be used to order urls by the order they appeared in the campaign to recreate our heat map.
     */
    public function clicks($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('reports/clicks', $_params);
    }

    /**
     * Retrieve the Ecommerce Orders tracked by ecomm/order-add()
     * @param string $cid
     * @param associative_array $opts
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     *     - since string optional pull only messages since this time - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00"
     * @return associative_array the total matching orders and the specific orders for the requested page
     *     - total int the total matching orders
     *     - data array structs for the actual data for each order being returned
     *         - store_id string the store id generated by the plugin used to uniquely identify a store
     *         - store_name string the store name collected by the plugin - often the domain name
     *         - order_id string the internal order id the store tracked this order by
     *         - member associative_array the member record as returned by lists/member-info() that received this campaign and is associated with this order
     *         - order_total double the order total
     *         - tax_total double the total tax for the order (if collected)
     *         - ship_total double the shipping total for the order (if collected)
     *         - order_date string the date the order was tracked - from the store if possible, otherwise the GMT time we received it
     *         - lines array structs containing details of the order:
     *             - line_num int the line number assigned to this line
     *             - product_id int the product id assigned to this item
     *             - product_name string the product name
     *             - product_sku string the sku for the product
     *             - product_category_id int the id for the product category
     *             - product_category_name string the product category name
     *             - qty double optional the quantity of the item ordered - defaults to 1
     *             - cost double optional the cost of a single item (ie, not the extended cost of the line) - defaults to 0
     */
    public function ecommOrders($cid, $opts=array()) {
        $_params = array("cid" => $cid, "opts" => $opts);
        return $this->master->call('reports/ecomm-orders', $_params);
    }

    /**
     * Retrieve the eepurl stats from the web/Twitter mentions for this campaign
     * @param string $cid
     * @return associative_array containing tweets, retweets, clicks, and referrer related to using the campaign's eepurl
     *     - twitter associative_array various Twitter related stats
     *         - tweets int Total number of tweets seen
     *         - first_tweet string date and time of the first tweet seen
     *         - last_tweet string date and time of the last tweet seen
     *         - retweets int Total number of retweets seen
     *         - first_retweet string date and time of the first retweet seen
     *         - last_retweet string date and time of the last retweet seen
     *         - statuses array an structs for statuses recorded including:
     *             - status string the text of the tweet/update
     *             - screen_name string the screen name as recorded when first seen
     *             - status_id string the status id of the tweet (they are really unsigned 64 bit ints)
     *             - datetime string the date/time of the tweet
     *             - is_retweet bool whether or not this was a retweet
     *     - clicks associative_array stats related to click-throughs on the eepurl
     *         - clicks int Total number of clicks seen
     *         - first_click string date and time of the first click seen
     *         - last_click string date and time of the first click seen
     *         - locations array structs for geographic locations including:
     *             - country string the country name the click was tracked to
     *             - region string the region in the country the click was tracked to (if available)
     *     - referrers array structs for referrers, including
     *         - referrer string the referrer, truncated to 100 bytes
     *         - clicks int Total number of clicks seen from this referrer
     *         - first_click string date and time of the first click seen from this referrer
     *         - last_click string date and time of the first click seen from this referrer
     */
    public function eepurl($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('reports/eepurl', $_params);
    }

    /**
     * Given a campaign and email address, return the entire click and open history with timestamps, ordered by time. If you need to dump the full activity for a campaign
and/or get incremental results, you should use the <a href="http://apidocs.mailchimp.com/export/1.0/campaignsubscriberactivity.func.php" targret="_new">campaignSubscriberActivity Export API method</a>,
<strong>not</strong> this, especially for large campaigns.
     * @param string $cid
     * @param array $emails
     *     - email string an email address
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
     *         - msg string the error message
     *     - data array an array of structs where each activity record has:
     *         - email string whatever was passed in the email parameter
     *             - email string the email address added
     *             - euid string the email unique id
     *             - leid string the list member's truly unique id
     *         - member associative_array the member record as returned by lists/member-info()
     *         - activity array an array of structs containing the activity, including:
     *             - action string The action name - either open or click
     *             - timestamp string The date/time of the action (GMT)
     *             - url string For click actions, the url clicked, otherwise this is empty
     *             - ip string The IP address the activity came from
     */
    public function memberActivity($cid, $emails) {
        $_params = array("cid" => $cid, "emails" => $emails);
        return $this->master->call('reports/member-activity', $_params);
    }

    /**
     * Retrieve the list of email addresses that did not open a given campaign
     * @param string $cid
     * @param associative_array $opts
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     * @return associative_array a total of all matching emails and the specific emails for this page
     *     - total int the total number of members who didn't open the campaign
     *     - data array structs for each campaign member matching as returned by lists/member-info()
     */
    public function notOpened($cid, $opts=array()) {
        $_params = array("cid" => $cid, "opts" => $opts);
        return $this->master->call('reports/not-opened', $_params);
    }

    /**
     * Retrieve the list of email addresses that opened a given campaign with how many times they opened
     * @param string $cid
     * @param associative_array $opts
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     *     - sort_field string optional the data to sort by - "opened" (order opens occurred, default) or "opens" (total number of opens). Invalid fields will fall back on the default.
     *     - sort_dir string optional the direct - ASC or DESC. defaults to ASC (case insensitive)
     * @return associative_array containing the total records matched and the specific records for this page
     *     - total int the total number of records matched
     *     - data array structs for the actual opens data, including:
     *         - member associative_array the member record as returned by lists/member-info()
     *         - opens int Total number of times the campaign was opened by this email address
     */
    public function opened($cid, $opts=array()) {
        $_params = array("cid" => $cid, "opts" => $opts);
        return $this->master->call('reports/opened', $_params);
    }

    /**
     * Get the top 5 performing email domains for this campaign. Users wanting more than 5 should use campaign reports/member-activity()
or campaignEmailStatsAIMAll() and generate any additional stats they require.
     * @param string $cid
     * @return array domains structs for each email domains and their associated stats
     *     - domain string Domain name or special "Other" to roll-up stats past 5 domains
     *     - total_sent int Total Email across all domains - this will be the same in every row
     *     - emails int Number of emails sent to this domain
     *     - bounces int Number of bounces
     *     - opens int Number of opens
     *     - clicks int Number of clicks
     *     - unsubs int Number of unsubs
     *     - delivered int Number of deliveries
     *     - emails_pct int Percentage of emails that went to this domain (whole number)
     *     - bounces_pct int Percentage of bounces from this domain (whole number)
     *     - opens_pct int Percentage of opens from this domain (whole number)
     *     - clicks_pct int Percentage of clicks from this domain (whole number)
     *     - unsubs_pct int Percentage of unsubs from this domain (whole number)
     */
    public function domainPerformance($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('reports/domain-performance', $_params);
    }

    /**
     * Retrieve the countries/regions and number of opens tracked for each. Email address are not returned.
     * @param string $cid
     * @return array an array of country structs where opens occurred
     *     - code string The ISO3166 2 digit country code
     *     - name string A version of the country name, if we have it
     *     - opens int The total number of opens that occurred in the country
     *     - regions array structs of data for each sub-region in the country
     *         - code string An internal code for the region. When this is blank, it indicates we know the country, but not the region
     *         - name string The name of the region, if we have one. For blank "code" values, this will be "Rest of Country"
     *         - opens int The total number of opens that occurred in the country
     */
    public function geoOpens($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('reports/geo-opens', $_params);
    }

    /**
     * Retrieve the Google Analytics data we've collected for this campaign. Note, requires Google Analytics Add-on to be installed and configured.
     * @param string $cid
     * @return array of structs for analytics we've collected for the passed campaign.
     *     - visits int number of visits
     *     - pages int number of page views
     *     - new_visits int new visits recorded
     *     - bounces int vistors who "bounced" from your site
     *     - time_on_site double the total time visitors spent on your sites
     *     - goal_conversions int number of goals converted
     *     - goal_value double value of conversion in dollars
     *     - revenue double revenue generated by campaign
     *     - transactions int number of transactions tracked
     *     - ecomm_conversions int number Ecommerce transactions tracked
     *     - goals array structs containing goal names and number of conversions
     *         - name string the name of the goal
     *         - conversions int the number of conversions for the goal
     */
    public function googleAnalytics($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('reports/google-analytics', $_params);
    }

    /**
     * Get email addresses the campaign was sent to
     * @param string $cid
     * @param associative_array $opts
     *     - status string optional the status to pull - one of 'sent', 'hard' (bounce), or 'soft' (bounce). By default, all records are returned
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     * @return associative_array a total of all matching emails and the specific emails for this page
     *     - total int the total number of members for the campaign and status
     *     - data array structs for each campaign member matching
     *         - member associative_array the member record as returned by lists/member-info()
     *         - status string the status of the send - one of 'sent', 'hard', 'soft'
     *         - absplit_group string if this was an absplit campaign, one of 'a','b', or 'winner'
     *         - tz_group string if this was an timewarp campaign the timezone GMT offset the member was included in
     */
    public function sentTo($cid, $opts=array()) {
        $_params = array("cid" => $cid, "opts" => $opts);
        return $this->master->call('reports/sent-to', $_params);
    }

    /**
     * Get the URL to a customized <a href="http://eepurl.com/gKmL" target="_blank">VIP Report</a> for the specified campaign and optionally send an email to someone with links to it. Note subsequent calls will overwrite anything already set for the same campign (eg, the password)
     * @param string $cid
     * @param array $opts
     *     - to_email string optional - optional, comma delimited list of email addresses to share the report with - no value means an email will not be sent
     *     - theme_id int optional - either a global or a user-specific theme id. Currently this needs to be pulled out of either the Share Report or Cobranding web views by grabbing the "theme" attribute from the list presented.
     *     - css_url string optional - a link to an external CSS file to be included after our default CSS (http://vip-reports.net/css/vip.css) <strong>only if</strong> loaded via the "secure_url" - max 255 bytes
     * @return associative_array details for the shared report, including:
     *     - title string The Title of the Campaign being shared
     *     - url string The URL to the shared report
     *     - secure_url string The URL to the shared report, including the password (good for loading in an IFRAME). For non-secure reports, this will not be returned
     *     - password string If secured, the password for the report, otherwise this field will not be returned
     */
    public function share($cid, $opts=array()) {
        $_params = array("cid" => $cid, "opts" => $opts);
        return $this->master->call('reports/share', $_params);
    }

    /**
     * Retrieve relevant aggregate campaign statistics (opens, bounces, clicks, etc.)
     * @param string $cid
     * @return associative_array the statistics for this campaign
     *     - syntax_errors int Number of email addresses in campaign that had syntactical errors.
     *     - hard_bounces int Number of email addresses in campaign that hard bounced.
     *     - soft_bounces int Number of email addresses in campaign that soft bounced.
     *     - unsubscribes int Number of email addresses in campaign that unsubscribed.
     *     - abuse_reports int Number of email addresses in campaign that reported campaign for abuse.
     *     - forwards int Number of times email was forwarded to a friend.
     *     - forwards_opens int Number of times a forwarded email was opened.
     *     - opens int Number of times the campaign was opened.
     *     - last_open string Date of the last time the email was opened.
     *     - unique_opens int Number of people who opened the campaign.
     *     - clicks int Number of times a link in the campaign was clicked.
     *     - unique_clicks int Number of unique recipient/click pairs for the campaign.
     *     - last_click string Date of the last time a link in the email was clicked.
     *     - users_who_clicked int Number of unique recipients who clicked on a link in the campaign.
     *     - emails_sent int Number of email addresses campaign was sent to.
     *     - unique_likes int total number of unique likes (Facebook)
     *     - recipient_likes int total number of recipients who liked (Facebook) the campaign
     *     - facebook_likes int total number of likes (Facebook) that came from Facebook
     *     - industry associative_array Various rates/percentages for the account's selected industry - empty otherwise. These will vary across calls, do not use them for anything important.
     *         - type string the selected industry
     *         - open_rate float industry open rate
     *         - click_rate float industry click rate
     *         - bounce_rate float industry bounce rate
     *         - unopen_rate float industry unopen rate
     *         - unsub_rate float industry unsub rate
     *         - abuse_rate float industry abuse rate
     *     - absplit associative_array If this was an absplit campaign, stats for the A and B groups will be returned - otherwise this is empty
     *         - bounces_a int bounces for the A group
     *         - bounces_b int bounces for the B group
     *         - forwards_a int forwards for the A group
     *         - forwards_b int forwards for the B group
     *         - abuse_reports_a int abuse reports for the A group
     *         - abuse_reports_b int abuse reports for the B group
     *         - unsubs_a int unsubs for the A group
     *         - unsubs_b int unsubs for the B group
     *         - recipients_click_a int clicks for the A group
     *         - recipients_click_b int clicks for the B group
     *         - forwards_opens_a int opened forwards for the A group
     *         - forwards_opens_b int opened forwards for the B group
     *         - opens_a int total opens for the A group
     *         - opens_b int total opens for the B group
     *         - last_open_a string date/time of last open for the A group
     *         - last_open_b string date/time of last open for the BG group
     *         - unique_opens_a int unique opens for the A group
     *         - unique_opens_b int unique opens for the B group
     *     - timewarp array If this campaign was a Timewarp campaign, an array of structs from each timezone stats exist for. Each will contain:
     *         - opens int opens for this timezone
     *         - last_open string the date/time of the last open for this timezone
     *         - unique_opens int the unique opens for this timezone
     *         - clicks int the total clicks for this timezone
     *         - last_click string the date/time of the last click for this timezone
     *         - unique_opens int the unique clicks for this timezone
     *         - bounces int the total bounces for this timezone
     *         - total int the total number of members sent to in this timezone
     *         - sent int the total number of members delivered to in this timezone
     *     - timeseries array structs for the first 24 hours of the campaign, per-hour stats:
     *         - timestamp string The timestemp in Y-m-d H:00:00 format
     *         - emails_sent int the total emails sent during the hour
     *         - unique_opens int unique opens seen during the hour
     *         - recipients_click int unique clicks seen during the hour
     */
    public function summary($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('reports/summary', $_params);
    }

    /**
     * Get all unsubscribed email addresses for a given campaign
     * @param string $cid
     * @param associative_array $opts
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     * @return associative_array a total of all unsubscribed emails and the specific members for this page
     *     - total int the total number of unsubscribes for the campaign
     *     - data array structs for the email addresses that unsubscribed
     *         - member string the member that unsubscribed as returned by lists/member-info()
     *         - reason string the reason collected for the unsubscribe. If populated, one of 'NORMAL','NOSIGNUP','INAPPROPRIATE','SPAM','OTHER'
     *         - reason_text string if the reason is OTHER, the text entered.
     */
    public function unsubscribes($cid, $opts=array()) {
        $_params = array("cid" => $cid, "opts" => $opts);
        return $this->master->call('reports/unsubscribes', $_params);
    }

}


