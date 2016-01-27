<?php

class Mailchimp_Conversations {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Retrieve conversation metadata, includes message data for the most recent message in the conversation
     * @param string $list_id
     * @param string $leid
     * @param string $campaign_id
     * @param int $start
     * @param int $limit
     * @return associative_array Conversation data and metadata
     *     - count int Total number of conversations, irrespective of pagination.
     *     - data array An array of structs representing individual conversations
     *         - unique_id string A string identifying this particular conversation
     *         - message_count int The total number of messages in this conversation
     *         - campaign_id string The unique identifier of the campaign this conversation is associated with
     *         - list_id string The unique identifier of the list this conversation is associated with
     *         - unread_messages int The number of messages in this conversation which have not yet been read.
     *         - from_label string A label representing the sender of this message.
     *         - from_email string The email address of the sender of this message.
     *         - subject string The subject of the message.
     *         - timestamp string Date the message was either sent or received.
     *         - last_message associative_array The most recent message in the conversation
     *             - from_label string A label representing the sender of this message.
     *             - from_email string The email address of the sender of this message.
     *             - subject string The subject of the message.
     *             - message string The plain-text content of the message.
     *             - read boolean Whether or not this message has been marked as read.
     *             - timestamp string Date the message was either sent or received.
     */
    public function getList($list_id=null, $leid=null, $campaign_id=null, $start=0, $limit=25) {
        $_params = array("list_id" => $list_id, "leid" => $leid, "campaign_id" => $campaign_id, "start" => $start, "limit" => $limit);
        return $this->master->call('conversations/list', $_params);
    }

    /**
     * Retrieve conversation messages
     * @param string $conversation_id
     * @param boolean $mark_as_read
     * @param int $start
     * @param int $limit
     * @return associative_array Message data and metadata
     *     - count int The number of messages in this conversation, irrespective of paging.
     *     - data array An array of structs representing each message in a conversation
     *         - from_label string A label representing the sender of this message.
     *         - from_email string The email address of the sender of this message.
     *         - subject string The subject of the message.
     *         - message string The plain-text content of the message.
     *         - read boolean Whether or not this message has been marked as read.
     *         - timestamp string Date the message was either sent or received.
     */
    public function messages($conversation_id, $mark_as_read=false, $start=0, $limit=25) {
        $_params = array("conversation_id" => $conversation_id, "mark_as_read" => $mark_as_read, "start" => $start, "limit" => $limit);
        return $this->master->call('conversations/messages', $_params);
    }

    /**
     * Retrieve conversation messages
     * @param string $conversation_id
     * @param string $message
     * @return associative_array Message data from the created message
     *     - from_label string A label representing the sender of this message.
     *     - from_email string The email address of the sender of this message.
     *     - subject string The subject of the message.
     *     - message string The plain-text content of the message.
     *     - read boolean Whether or not this message has been marked as read.
     *     - timestamp string Date the message was either sent or received.
     */
    public function reply($conversation_id, $message) {
        $_params = array("conversation_id" => $conversation_id, "message" => $message);
        return $this->master->call('conversations/reply', $_params);
    }

}


