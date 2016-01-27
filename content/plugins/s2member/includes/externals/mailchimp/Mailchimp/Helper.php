<?php

class Mailchimp_Helper {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Retrieve lots of account information including payments made, plan info, some account stats, installed modules,
contact info, and more. No private information like Credit Card numbers is available.
     * @param array $exclude
     * @return associative_array containing the details for the account tied to this API Key
     *     - username string The company name associated with the account
     *     - user_id string The Account user unique id (for building some links)
     *     - is_trial bool Whether the Account is in Trial mode (can only send campaigns to less than 100 emails)
     *     - is_approved bool Whether the Account has been approved for purchases
     *     - has_activated bool Whether the Account has been activated
     *     - timezone string The timezone for the Account - default is "US/Eastern"
     *     - plan_type string Plan Type - "monthly", "payasyougo", or "free"
     *     - plan_low int <em>only for Monthly plans</em> - the lower tier for list size
     *     - plan_high int <em>only for Monthly plans</em> - the upper tier for list size
     *     - plan_start_date string <em>only for Monthly plans</em> - the start date for a monthly plan
     *     - emails_left int <em>only for Free and Pay-as-you-go plans</em> emails credits left for the account
     *     - pending_monthly bool Whether the account is finishing Pay As You Go credits before switching to a Monthly plan
     *     - first_payment string date of first payment
     *     - last_payment string date of most recent payment
     *     - times_logged_in int total number of times the account has been logged into via the web
     *     - last_login string date/time of last login via the web
     *     - affiliate_link string Monkey Rewards link for our Affiliate program
     *     - industry string the user's selected industry
     *     - contact associative_array Contact details for the account
     *         - fname string First Name
     *         - lname string Last Name
     *         - email string Email Address
     *         - company string Company Name
     *         - address1 string Address Line 1
     *         - address2 string Address Line 2
     *         - city string City
     *         - state string State or Province
     *         - zip string Zip or Postal Code
     *         - country string Country name
     *         - url string Website URL
     *         - phone string Phone number
     *         - fax string Fax number
     *     - modules array a struct for each addon module installed in the account
     *         - id string An internal module id
     *         - name string The module name
     *         - added string The date the module was added
     *         - data associative_array Any extra data associated with this module as key=>value pairs
     *     - orders array a struct for each order for the account
     *         - order_id int The order id
     *         - type string The order type - either "monthly" or "credits"
     *         - amount double The order amount
     *         - date string The order date
     *         - credits_used double The total credits used
     *     - rewards associative_array Rewards details for the account including credits & inspections earned, number of referrals, referral details, and rewards used
     *         - referrals_this_month int the total number of referrals this month
     *         - notify_on string whether or not we notify the user when rewards are earned
     *         - notify_email string the email address address used for rewards notifications
     *         - credits associative_array Email credits earned:
     *             - this_month int credits earned this month
     *             - total_earned int credits earned all time
     *             - remaining int credits remaining
     *         - inspections associative_array Inbox Inspections earned:
     *             - this_month int credits earned this month
     *             - total_earned int credits earned all time
     *             - remaining int credits remaining
     *         - referrals array a struct for each referral, including:
     *             - name string the name of the account
     *             - email string the email address associated with the account
     *             - signup_date string the signup date for the account
     *             - type string the source for the referral
     *         - applied array a struct for each applied rewards, including:
     *             - value int the number of credits user
     *             - date string the date applied
     *             - order_id int the order number credits were applied to
     *             - order_desc string the order description
     *     - integrations array a struct for each connected integrations that can be used with campaigns, including:
     *         - id int an internal id for the integration
     *         - name string the integration name
     *         - list_id string either "_any_" when globally accessible or the list id it's valid for use against
     *         - user_id string if applicable, the user id for the integrated system
     *         - account string if applicable, the user/account name for the integrated system
     *         - profiles array For Facebook, users/page that can be posted to.
     *             - id string the user or page id
     *             - name string the user or page name
     *             - is_page bool whether this is a user or a page
     */
    public function accountDetails($exclude=array()) {
        $_params = array("exclude" => $exclude);
        return $this->master->call('helper/account-details', $_params);
    }

    /**
     * Retrieve minimal data for all Campaigns a member was sent
     * @param associative_array $email
     *     - email string an email address
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @param associative_array $options
     *     - list_id string optional A list_id to limit the campaigns to
     * @return array an array of structs containing campaign data for each matching campaign (ordered by send time ascending), including:
     *     - id string the campaign unique id
     *     - title string the campaign's title
     *     - subject string the campaign's subject
     *     - send_time string the time the campaign was sent
     *     - type string the campaign type
     */
    public function campaignsForEmail($email, $options=null) {
        $_params = array("email" => $email, "options" => $options);
        return $this->master->call('helper/campaigns-for-email', $_params);
    }

    /**
     * Return the current Chimp Chatter messages for an account.
     * @return array An array of structs containing data for each chatter message
     *     - message string The chatter message
     *     - type string The type of the message - one of lists:new-subscriber, lists:unsubscribes, lists:profile-updates, campaigns:facebook-likes, campaigns:facebook-comments, campaigns:forward-to-friend, lists:imports, or campaigns:inbox-inspections
     *     - url string a url into the web app that the message could link to, if applicable
     *     - list_id string the list_id a message relates to, if applicable. Deleted lists will return -DELETED-
     *     - campaign_id string the list_id a message relates to, if applicable. Deleted campaigns will return -DELETED-
     *     - update_time string The date/time the message was last updated
     */
    public function chimpChatter() {
        $_params = array();
        return $this->master->call('helper/chimp-chatter', $_params);
    }

    /**
     * Have HTML content auto-converted to a text-only format. You can send: plain HTML, an existing Campaign Id, or an existing Template Id. Note that this will <strong>not</strong> save anything to or update any of your lists, campaigns, or templates.
It's also not just Lynx and is very fine tuned for our template layouts - your mileage may vary.
     * @param string $type
     * @param associative_array $content
     *     - html string optional a single string value,
     *     - cid string a valid Campaign Id
     *     - user_template_id string the id of a user template
     *     - base_template_id string the id of a built in base/basic template
     *     - gallery_template_id string the id of a built in gallery template
     *     - url string a valid & public URL to pull html content from
     * @return associative_array the content pass in converted to text.
     *     - text string the converted html
     */
    public function generateText($type, $content) {
        $_params = array("type" => $type, "content" => $content);
        return $this->master->call('helper/generate-text', $_params);
    }

    /**
     * Send your HTML content to have the CSS inlined and optionally remove the original styles.
     * @param string $html
     * @param bool $strip_css
     * @return associative_array with a "html" key
     *     - html string Your HTML content with all CSS inlined, just like if we sent it.
     */
    public function inlineCss($html, $strip_css=false) {
        $_params = array("html" => $html, "strip_css" => $strip_css);
        return $this->master->call('helper/inline-css', $_params);
    }

    /**
     * Retrieve minimal List data for all lists a member is subscribed to.
     * @param associative_array $email
     *     - email string an email address
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @return array An array of structs with info on the  list_id the member is subscribed to.
     *     - id string the list unique id
     *     - web_id int the id referenced in web interface urls
     *     - name string the list name
     */
    public function listsForEmail($email) {
        $_params = array("email" => $email);
        return $this->master->call('helper/lists-for-email', $_params);
    }

    /**
     * "Ping" the MailChimp API - a simple method you can call that will return a constant value as long as everything is good. Note
than unlike most all of our methods, we don't throw an Exception if we are having issues. You will simply receive a different
string back that will explain our view on what is going on.
     * @return associative_array a with a "msg" key
     *     - msg string containing "Everything's Chimpy!" if everything is chimpy, otherwise returns an error message
     */
    public function ping() {
        $_params = array();
        return $this->master->call('helper/ping', $_params);
    }

    /**
     * Search all campaigns for the specified query terms
     * @param string $query
     * @param int $offset
     * @param string $snip_start
     * @param string $snip_end
     * @return associative_array containing the total matches and current results
     *     - total int total campaigns matching
     *     - results array matching campaigns and snippets
     *     - snippet string the matching snippet for the campaign
     *     - campaign associative_array the matching campaign's details - will return same data as single campaign from campaigns/list()
     */
    public function searchCampaigns($query, $offset=0, $snip_start=null, $snip_end=null) {
        $_params = array("query" => $query, "offset" => $offset, "snip_start" => $snip_start, "snip_end" => $snip_end);
        return $this->master->call('helper/search-campaigns', $_params);
    }

    /**
     * Search account wide or on a specific list using the specified query terms
     * @param string $query
     * @param string $id
     * @param int $offset
     * @return associative_array An array of both exact matches and partial matches over a full search
     *     - exact_matches associative_array containing the exact email address matches and current results
     *         - total int total members matching
     *         - members array each entry will be struct matching the data format for a single member as returned by lists/member-info()
     *     - full_search associative_array containing the total matches and current results
     *         - total int total members matching
     *         - members array each entry will be struct matching  the data format for a single member as returned by lists/member-info()
     */
    public function searchMembers($query, $id=null, $offset=0) {
        $_params = array("query" => $query, "id" => $id, "offset" => $offset);
        return $this->master->call('helper/search-members', $_params);
    }

    /**
     * Retrieve all domain verification records for an account
     * @return array structs for each domain verification has been attempted for
     *     - domain string the verified domain
     *     - status string the status of the verification - either "verified" or "pending"
     *     - email string the email address used for verification - "pre-existing" if we automatically backfilled it at some point
     */
    public function verifiedDomains() {
        $_params = array();
        return $this->master->call('helper/verified-domains', $_params);
    }

}


