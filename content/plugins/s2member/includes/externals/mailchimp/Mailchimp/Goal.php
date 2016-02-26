<?php

class Mailchimp_Goal {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Retrieve goal event data for a particular list member. Note: only unique events are returned. If a user triggers
a particular event multiple times, you will still only receive one entry for that event.
     * @param string $list_id
     * @param associative_array $email
     *     - email string an email address
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @param int $start
     * @param int $limit
     * @return associative_array Event data and metadata
     *     - data array An array of goal data structs for the specified list member in the following format
     *         - event string The URL or name of the event that was triggered
     *         - last_visited_at string A timestamp in the format 'YYYY-MM-DD HH:MM:SS' that represents the last time this event was seen.
     *     - total int The total number of events that match your criteria.
     */
    public function events($list_id, $email, $start=0, $limit=25) {
        $_params = array("list_id" => $list_id, "email" => $email, "start" => $start, "limit" => $limit);
        return $this->master->call('goal/events', $_params);
    }

    /**
     * This allows programmatically trigger goal event collection without the use of front-end code.
     * @param string $list_id
     * @param associative_array $email
     *     - email string an email address
     *     - euid string the unique id for an email address (not list related) - the email "id" returned from listMemberInfo, Webhooks, Campaigns, etc.
     *     - leid string the list email id (previously called web_id) for a list-member-info type call. this doesn't change when the email address changes
     * @param string $campaign_id
     * @param string $event
     * @return associative_array Event data for the submitted event
     *     - event string The URL or name of the event that was triggered
     *     - last_visited_at string A timestamp in the format 'YYYY-MM-DD HH:MM:SS' that represents the last time this event was seen.
     */
    public function recordEvent($list_id, $email, $campaign_id, $event) {
        $_params = array("list_id" => $list_id, "email" => $email, "campaign_id" => $campaign_id, "event" => $event);
        return $this->master->call('goal/record-event', $_params);
    }

}


