<?php

class Mailchimp_Campaigns {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Get the content (both html and text) for a campaign either as it would appear in the campaign archive or as the raw, original content
     * @param string $cid
     * @param associative_array $options
     *     - view string optional one of "archive" (default), "preview" (like our popup-preview) or "raw"
     *     - email associative_array optional if provided, view is "archive" or "preview", the campaign's list still exists, and the requested record is subscribed to the list. the returned content will be populated with member data populated. a struct with one of the following keys - failing to provide anything will produce an error relating to the email address. If multiple keys are provided, the first one from the following list that we find will be used, the rest will be ignored.
     *         - email string an email address
     *         - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *         - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @return associative_array containing all content for the campaign
     *     - html string The HTML content used for the campaign with merge tags intact
     *     - text string The Text content used for the campaign with merge tags intact
     */
    public function content($cid, $options=array()) {
        $_params = array("cid" => $cid, "options" => $options);
        return $this->master->call('campaigns/content', $_params);
    }

    /**
     * Create a new draft campaign to send. You <strong>can not</strong> have more than 32,000 campaigns in your account.
     * @param string $type
     * @param associative_array $options
     *     - list_id string the list to send this campaign to- get lists using lists/list()
     *     - subject string the subject line for your campaign message
     *     - from_email string the From: email address for your campaign message
     *     - from_name string the From: name for your campaign message (not an email address)
     *     - to_name string the To: name recipients will see (not email address)
     *     - template_id int optional - use this user-created template to generate the HTML content of the campaign (takes precendence over other template options)
     *     - gallery_template_id int optional - use a template from the public gallery to generate the HTML content of the campaign (takes precendence over base template options)
     *     - base_template_id int optional - use this a base/start-from-scratch template to generate the HTML content of the campaign
     *     - folder_id int optional - automatically file the new campaign in the folder_id passed. Get using folders/list() - note that Campaigns and Autoresponders have separate folder setups
     *     - tracking associative_array optional - set which recipient actions will be tracked. Click tracking can not be disabled for Free accounts.
     *         - opens bool whether to track opens, defaults to true
     *         - html_clicks bool whether to track clicks in HTML content, defaults to true
     *         - text_clicks bool whether to track clicks in Text content, defaults to false
     *     - title string optional - an internal name to use for this campaign.  By default, the campaign subject will be used.
     *     - authenticate boolean optional - set to true to enable SenderID, DomainKeys, and DKIM authentication, defaults to false.
     *     - analytics associative_array optional - one or more of these keys set to the tag to use - that can be any custom text (up to 50 bytes)
     *         - google string for Google Analytics  tracking
     *         - clicktale string for ClickTale  tracking
     *         - gooal string for Goal tracking (the extra 'o' in the param name is not a typo)
     *     - auto_footer boolean optional Whether or not we should auto-generate the footer for your content. Mostly useful for content from URLs or Imports
     *     - inline_css boolean optional Whether or not css should be automatically inlined when this campaign is sent, defaults to false.
     *     - generate_text boolean optional Whether of not to auto-generate your Text content from the HTML content. Note that this will be ignored if the Text part of the content passed is not empty, defaults to false.
     *     - auto_tweet boolean optional If set, this campaign will be auto-tweeted when it is sent - defaults to false. Note that if a Twitter account isn't linked, this will be silently ignored.
     *     - auto_fb_post array optional If set, this campaign will be auto-posted to the page_ids contained in the array. If a Facebook account isn't linked or the account does not have permission to post to the page_ids requested, those failures will be silently ignored.
     *     - fb_comments boolean optional If true, the Facebook comments (and thus the <a href="http://kb.mailchimp.com/article/i-dont-want-an-archiave-of-my-campaign-can-i-turn-it-off/" target="_blank">archive bar</a> will be displayed. If false, Facebook comments will not be enabled (does not imply no archive bar, see previous link). Defaults to "true".
     *     - timewarp boolean optional If set, this campaign must be scheduled 24 hours in advance of sending - default to false. Only valid for "regular" campaigns and "absplit" campaigns that split on schedule_time.
     *     - ecomm360 boolean optional If set, our <a href="http://www.mailchimp.com/blog/ecommerce-tracking-plugin/" target="_blank">Ecommerce360 tracking</a> will be enabled for links in the campaign
     *     - crm_tracking array optional If set, an array of structs to enable CRM tracking for:
     *         - salesforce associative_array optional Enable SalesForce push back
     *             - campaign bool optional - if true, create a Campaign object and update it with aggregate stats
     *             - notes bool optional - if true, attempt to update Contact notes based on email address
     *         - highrise associative_array optional Enable Highrise push back
     *             - campaign bool optional - if true, create a Kase object and update it with aggregate stats
     *             - notes bool optional - if true, attempt to update Contact notes based on email address
     *         - capsule associative_array optional Enable Capsule push back (only notes are supported)
     *             - notes bool optional - if true, attempt to update Contact notes based on email address
     * @param associative_array $content
     *     - html string for raw/pasted HTML content
     *     - sections associative_array when using a template instead of raw HTML, each key should be the unique mc:edit area name from the template.
     *     - text string for the plain-text version
     *     - url string to have us pull in content from a URL. Note, this will override any other content options - for lists with Email Format options, you'll need to turn on generate_text as well
     *     - archive string to send a Base64 encoded archive file for us to import all media from. Note, this will override any other content options - for lists with Email Format options, you'll need to turn on generate_text as well
     *     - archive_type string optional - only necessary for the "archive" option. Supported formats are: zip, tar.gz, tar.bz2, tar, tgz, tbz . If not included, we will default to zip
     * @param associative_array $segment_opts
     * @param associative_array $type_opts
     *     - rss associative_array For RSS Campaigns this, struct should contain:
     *         - url string the URL to pull RSS content from - it will be verified and must exist
     *         - schedule string optional one of "daily", "weekly", "monthly" - defaults to "daily"
     *         - schedule_hour string optional an hour between 0 and 24 - default to 4 (4am <em>local time</em>) - applies to all schedule types
     *         - schedule_weekday string optional for "weekly" only, a number specifying the day of the week to send: 0 (Sunday) - 6 (Saturday) - defaults to 1 (Monday)
     *         - schedule_monthday string optional for "monthly" only, a number specifying the day of the month to send (1 - 28) or "last" for the last day of a given month. Defaults to the 1st day of the month
     *         - days associative_array optional used for "daily" schedules only, an array of the <a href="https://en.wikipedia.org/wiki/ISO-8601#Week_dates" target="_blank">ISO-8601 weekday numbers</a> to send on
     *             - 1 bool optional Monday, defaults to true
     *             - 2 bool optional Tuesday, defaults to true
     *             - 3 bool optional Wednesday, defaults to true
     *             - 4 bool optional Thursday, defaults to true
     *             - 5 bool optional Friday, defaults to true
     *             - 6 bool optional Saturday, defaults to true
     *             - 7 bool optional Sunday, defaults to true
     *     - absplit associative_array For A/B Split campaigns, this struct should contain:
     *         - split_test string The values to segment based on. Currently, one of: "subject", "from_name", "schedule". NOTE, for "schedule", you will need to call campaigns/schedule() separately!
     *         - pick_winner string How the winner will be picked, one of: "opens" (by the open_rate), "clicks" (by the click rate), "manual" (you pick manually)
     *         - wait_units int optional the default time unit to wait before auto-selecting a winner - use "3600" for hours, "86400" for days. Defaults to 86400.
     *         - wait_time int optional the number of units to wait before auto-selecting a winner - defaults to 1, so if not set, a winner will be selected after 1 Day.
     *         - split_size int optional this is a percentage of what size the Campaign's List plus any segmentation options results in. "schedule" type forces 50%, all others default to 10%
     *         - from_name_a string optional sort of, required when split_test is "from_name"
     *         - from_name_b string optional sort of, required when split_test is "from_name"
     *         - from_email_a string optional sort of, required when split_test is "from_name"
     *         - from_email_b string optional sort of, required when split_test is "from_name"
     *         - subject_a string optional sort of, required when split_test is "subject"
     *         - subject_b string optional sort of, required when split_test is "subject"
     *     - auto associative_array For AutoResponder campaigns, this struct should contain:
     *         - offset-units string one of "hourly", "day", "week", "month", "year" - required
     *         - offset-time string optional, sort of - the number of units must be a number greater than 0 for signup based autoresponders, ignored for "hourly"
     *         - offset-dir string either "before" or "after", ignored for "hourly"
     *         - event string optional "signup" (default) to base this members added to a list, "date", "annual", or "birthday" to base this on merge field in the list, "campaignOpen" or "campaignClicka" to base this on any activity for a campaign, "campaignClicko" to base this on clicks on a specific URL in a campaign, "mergeChanged" to base this on a specific merge field being changed to a specific value
     *         - event-datemerge string optional sort of, this is required if the event is "date", "annual", "birthday", or "mergeChanged"
     *         - campaign_id string optional sort of, required for "campaignOpen", "campaignClicka", or "campaignClicko"
     *         - campaign_url string optional sort of, required for "campaignClicko"
     *         - schedule_hour int The hour of the day - 24 hour format in GMT - the autoresponder should be triggered, ignored for "hourly"
     *         - use_import_time boolean whether or not imported subscribers (ie, <em>any</em> non-double optin subscribers) will receive
     *         - days associative_array optional used for "daily" schedules only, an array of the <a href="https://en.wikipedia.org/wiki/ISO-8601#Week_dates" target="_blank">ISO-8601 weekday numbers</a> to send on<
     *             - 1 bool optional Monday, defaults to true
     *             - 2 bool optional Tuesday, defaults to true
     *             - 3 bool optional Wednesday, defaults to true
     *             - 4 bool optional Thursday, defaults to true
     *             - 5 bool optional Friday, defaults to true
     *             - 6 bool optional Saturday, defaults to true
     *             - 7 bool optional Sunday, defaults to true
     * @return associative_array the new campaign's details - will return same data as single campaign from campaigns/list()
     */
    public function create($type, $options, $content, $segment_opts=null, $type_opts=null) {
        $_params = array("type" => $type, "options" => $options, "content" => $content, "segment_opts" => $segment_opts, "type_opts" => $type_opts);
        return $this->master->call('campaigns/create', $_params);
    }

    /**
     * Delete a campaign. Seriously, "poof, gone!" - be careful! Seriously, no one can undelete these.
     * @param string $cid
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function delete($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('campaigns/delete', $_params);
    }

    /**
     * Get the list of campaigns and their details matching the specified filters
     * @param associative_array $filters
     *     - campaign_id string optional - return the campaign using a know campaign_id.  Accepts multiples separated by commas when not using exact matching.
     *     - parent_id string optional - return the child campaigns using a known parent campaign_id.  Accepts multiples separated by commas when not using exact matching.
     *     - list_id string optional - the list to send this campaign to - get lists using lists/list(). Accepts multiples separated by commas when not using exact matching.
     *     - folder_id int optional - only show campaigns from this folder id - get folders using folders/list(). Accepts multiples separated by commas when not using exact matching.
     *     - template_id int optional - only show campaigns using this template id - get templates using templates/list(). Accepts multiples separated by commas when not using exact matching.
     *     - status string optional - return campaigns of a specific status - one of "sent", "save", "paused", "schedule", "sending". Accepts multiples separated by commas when not using exact matching.
     *     - type string optional - return campaigns of a specific type - one of "regular", "plaintext", "absplit", "rss", "auto". Accepts multiples separated by commas when not using exact matching.
     *     - from_name string optional - only show campaigns that have this "From Name"
     *     - from_email string optional - only show campaigns that have this "Reply-to Email"
     *     - title string optional - only show campaigns that have this title
     *     - subject string optional - only show campaigns that have this subject
     *     - sendtime_start string optional - only show campaigns that have been sent since this date/time (in GMT) -  - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00" - if this is invalid the whole call fails
     *     - sendtime_end string optional - only show campaigns that have been sent before this date/time (in GMT) -  - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00" - if this is invalid the whole call fails
     *     - uses_segment boolean - whether to return just campaigns with or without segments
     *     - exact boolean optional - flag for whether to filter on exact values when filtering, or search within content for filter values - defaults to true. Using this disables the use of any filters that accept multiples.
     * @param int $start
     * @param int $limit
     * @param string $sort_field
     * @param string $sort_dir
     * @return associative_array containing a count of all matching campaigns, the specific ones for the current page, and any errors from the filters provided
     *     - total int the total number of campaigns matching the filters passed in
     *     - data array structs for each campaign being returned
     *         - id string Campaign Id (used for all other campaign functions)
     *         - web_id int The Campaign id used in our web app, allows you to create a link directly to it
     *         - list_id string The List used for this campaign
     *         - folder_id int The Folder this campaign is in
     *         - template_id int The Template this campaign uses
     *         - content_type string How the campaign's content is put together - one of 'template', 'html', 'url'
     *         - title string Title of the campaign
     *         - type string The type of campaign this is (regular,plaintext,absplit,rss,inspection,auto)
     *         - create_time string Creation time for the campaign
     *         - send_time string Send time for the campaign - also the scheduled time for scheduled campaigns.
     *         - emails_sent int Number of emails email was sent to
     *         - status string Status of the given campaign (save,paused,schedule,sending,sent)
     *         - from_name string From name of the given campaign
     *         - from_email string Reply-to email of the given campaign
     *         - subject string Subject of the given campaign
     *         - to_name string Custom "To:" email string using merge variables
     *         - archive_url string Archive link for the given campaign
     *         - inline_css boolean Whether or not the campaign content's css was auto-inlined
     *         - analytics string Either "google" if enabled or "N" if disabled
     *         - analytics_tag string The name/tag the campaign's links were tagged with if analytics were enabled.
     *         - authenticate boolean Whether or not the campaign was authenticated
     *         - ecomm360 boolean Whether or not ecomm360 tracking was appended to links
     *         - auto_tweet boolean Whether or not the campaign was auto tweeted after sending
     *         - auto_fb_post string A comma delimited list of Facebook Profile/Page Ids the campaign was posted to after sending. If not used, blank.
     *         - auto_footer boolean Whether or not the auto_footer was manually turned on
     *         - timewarp boolean Whether or not the campaign used Timewarp
     *         - timewarp_schedule string The time, in GMT, that the Timewarp campaign is being sent. For A/B Split campaigns, this is blank and is instead in their schedule_a and schedule_b in the type_opts array
     *         - parent_id string the unique id of the parent campaign (currently only valid for rss children). Will be blank for non-rss child campaigns or parent campaign has been deleted.
     *         - is_child boolean true if this is an RSS child campaign. Will return true even if the parent campaign has been deleted.
     *         - tests_sent string tests sent
     *         - tests_remain int test sends remaining
     *         - tracking associative_array the various tracking options used
     *             - html_clicks boolean whether or not tracking for html clicks was enabled.
     *             - text_clicks boolean whether or not tracking for text clicks was enabled.
     *             - opens boolean whether or not opens tracking was enabled.
     *         - segment_text string a string marked-up with HTML explaining the segment used for the campaign in plain English
     *         - segment_opts array the segment used for the campaign - can be passed to campaigns/segment-test or campaigns/create()
     *         - saved_segment associative_array if a saved segment was used (match+conditions returned above):
     *             - id int the saved segment id
     *             - type string the saved segment type
     *             - name string the saved segment name
     *         - type_opts associative_array the type-specific options for the campaign - can be passed to campaigns/create()
     *         - comments_total int total number of comments left on this campaign
     *         - comments_unread int total number of unread comments for this campaign based on the login the apikey belongs to
     *         - summary associative_array if available, the basic aggregate stats returned by reports/summary
     *     - errors array structs of any errors found while loading lists - usually just from providing invalid list ids
     *         - filter string the filter that caused the failure
     *         - value string the filter value that caused the failure
     *         - code int the error code
     *         - error string the error message
     */
    public function getList($filters=array(), $start=0, $limit=25, $sort_field='create_time', $sort_dir='DESC') {
        $_params = array("filters" => $filters, "start" => $start, "limit" => $limit, "sort_field" => $sort_field, "sort_dir" => $sort_dir);
        return $this->master->call('campaigns/list', $_params);
    }

    /**
     * Pause an AutoResponder or RSS campaign from sending
     * @param string $cid
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function pause($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('campaigns/pause', $_params);
    }

    /**
     * Returns information on whether a campaign is ready to send and possible issues we may have detected with it - very similar to the confirmation step in the app.
     * @param string $cid
     * @return associative_array containing:
     *     - is_ready bool whether or not you're going to be able to send this campaign
     *     - items array an array of structs explaining basically what the app's confirmation step would
     *         - type string the item type - generally success, warning, or error
     *         - heading string the item's heading in the app
     *         - details string the item's details from the app, sans any html tags/links
     */
    public function ready($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('campaigns/ready', $_params);
    }

    /**
     * Replicate a campaign.
     * @param string $cid
     * @return associative_array the matching campaign's details - will return same data as single campaign from campaigns/list()
     */
    public function replicate($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('campaigns/replicate', $_params);
    }

    /**
     * Resume sending an AutoResponder or RSS campaign
     * @param string $cid
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function resume($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('campaigns/resume', $_params);
    }

    /**
     * Schedule a campaign to be sent in the future
     * @param string $cid
     * @param string $schedule_time
     * @param string $schedule_time_b
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function schedule($cid, $schedule_time, $schedule_time_b=null) {
        $_params = array("cid" => $cid, "schedule_time" => $schedule_time, "schedule_time_b" => $schedule_time_b);
        return $this->master->call('campaigns/schedule', $_params);
    }

    /**
     * Schedule a campaign to be sent in batches sometime in the future. Only valid for "regular" campaigns
     * @param string $cid
     * @param string $schedule_time
     * @param int $num_batches
     * @param int $stagger_mins
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function scheduleBatch($cid, $schedule_time, $num_batches=2, $stagger_mins=5) {
        $_params = array("cid" => $cid, "schedule_time" => $schedule_time, "num_batches" => $num_batches, "stagger_mins" => $stagger_mins);
        return $this->master->call('campaigns/schedule-batch', $_params);
    }

    /**
     * Allows one to test their segmentation rules before creating a campaign using them.
     * @param string $list_id
     * @param associative_array $options
     *     - saved_segment_id string a saved segment id from lists/segments() - this will take precendence, otherwise the match+conditions are required.
     *     - match string controls whether to use AND or OR when applying your options - expects "<strong>any</strong>" (for OR) or "<strong>all</strong>" (for AND)
     *     - conditions array of up to 5 structs for different criteria to apply while segmenting. Each criteria row must contain 3 keys - "<strong>field</strong>", "<strong>op</strong>", and "<strong>value</strong>" - and possibly a fourth, "<strong>extra</strong>", based on these definitions:
     * @return associative_array with a single entry:
     *     - total int The total number of subscribers matching your segmentation options
     */
    public function segmentTest($list_id, $options) {
        $_params = array("list_id" => $list_id, "options" => $options);
        return $this->master->call('campaigns/segment-test', $_params);
    }

    /**
     * Send a given campaign immediately. For RSS campaigns, this will "start" them.
     * @param string $cid
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function send($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('campaigns/send', $_params);
    }

    /**
     * Send a test of this campaign to the provided email addresses
     * @param string $cid
     * @param array $test_emails
     * @param string $send_type
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function sendTest($cid, $test_emails=array(), $send_type='html') {
        $_params = array("cid" => $cid, "test_emails" => $test_emails, "send_type" => $send_type);
        return $this->master->call('campaigns/send-test', $_params);
    }

    /**
     * Get the HTML template content sections for a campaign. Note that this <strong>will</strong> return very jagged, non-standard results based on the template
a campaign is using. You only want to use this if you want to allow editing template sections in your application.
     * @param string $cid
     * @return associative_array content containing all content section for the campaign - section name are dependent upon the template used and thus can't be documented
     */
    public function templateContent($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('campaigns/template-content', $_params);
    }

    /**
     * Unschedule a campaign that is scheduled to be sent in the future
     * @param string $cid
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function unschedule($cid) {
        $_params = array("cid" => $cid);
        return $this->master->call('campaigns/unschedule', $_params);
    }

    /**
     * Update just about any setting besides type for a campaign that has <em>not</em> been sent. See campaigns/create() for details.
Caveats:<br/><ul class='bullets'>
<li>If you set a new list_id, all segmentation options will be deleted and must be re-added.</li>
<li>If you set template_id, you need to follow that up by setting it's 'content'</li>
<li>If you set segment_opts, you should have tested your options against campaigns/segment-test().</li>
<li>To clear/unset segment_opts, pass an empty string or array as the value. Various wrappers may require one or the other.</li>
</ul>
     * @param string $cid
     * @param string $name
     * @param array $value
     * @return associative_array updated campaign details and any errors
     *     - data associative_array the update campaign details - will return same data as single campaign from campaigns/list()
     *     - errors array for "options" only - structs containing:
     *         - code int the error code
     *         - message string the full error message
     *         - name string the parameter name that failed
     */
    public function update($cid, $name, $value) {
        $_params = array("cid" => $cid, "name" => $name, "value" => $value);
        return $this->master->call('campaigns/update', $_params);
    }

}
