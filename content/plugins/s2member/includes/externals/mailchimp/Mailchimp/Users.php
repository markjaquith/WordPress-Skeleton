<?php

class Mailchimp_Users {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Invite a user to your account
     * @param string $email
     * @param string $role
     * @param string $msg
     * @return associative_array the method completion status
     *     - status string The status (success) of the call if it completed. Otherwise an error is thrown.
     */
    public function invite($email, $role='viewer', $msg='') {
        $_params = array("email" => $email, "role" => $role, "msg" => $msg);
        return $this->master->call('users/invite', $_params);
    }

    /**
     * Resend an invite a user to your account. Note, if the same address has been invited multiple times, this will simpy re-send the most recent invite
     * @param string $email
     * @return associative_array the method completion status
     *     - status string The status (success) of the call if it completed. Otherwise an error is thrown.
     */
    public function inviteResend($email) {
        $_params = array("email" => $email);
        return $this->master->call('users/invite-resend', $_params);
    }

    /**
     * Revoke an invitation sent to a user to your account. Note, if the same address has been invited multiple times, this will simpy revoke the most recent invite
     * @param string $email
     * @return associative_array the method completion status
     *     - status string The status (success) of the call if it completed. Otherwise an error is thrown.
     */
    public function inviteRevoke($email) {
        $_params = array("email" => $email);
        return $this->master->call('users/invite-revoke', $_params);
    }

    /**
     * Retrieve the list of pending users invitations have been sent for.
     * @return array structs for each invitation, including:
     *     - email string the email address the invitation was sent to
     *     - role string the role that will be assigned if they accept
     *     - sent_at string the time the invitation was sent. this will change if it's resent.
     *     - expiration string the expiration time for the invitation. this will change if it's resent.
     *     - msg string the welcome message included with the invitation
     */
    public function invites() {
        $_params = array();
        return $this->master->call('users/invites', $_params);
    }

    /**
     * Revoke access for a specified login
     * @param string $username
     * @return associative_array the method completion status
     *     - status string The status (success) of the call if it completed. Otherwise an error is thrown.
     */
    public function loginRevoke($username) {
        $_params = array("username" => $username);
        return $this->master->call('users/login-revoke', $_params);
    }

    /**
     * Retrieve the list of active logins.
     * @return array structs for each user, including:
     *     - id int the login id for this login
     *     - username string the username used to log in
     *     - name string a display name for the account - empty first/last names will return the username
     *     - email string the email tied to the account used for passwords resets and the ilk
     *     - role string the role assigned to the account
     *     - avatar string if available, the url for the login's avatar
     *     - global_user_id int the globally unique user id for the user account connected to
     *     - dc_unique_id string the datacenter unique id for the user account connected to, like helper/account-details
     */
    public function logins() {
        $_params = array();
        return $this->master->call('users/logins', $_params);
    }

    /**
     * Retrieve the profile for the login owning the provided API Key
     * @return associative_array the current user's details, including:
     *     - id int the login id for this login
     *     - username string the username used to log in
     *     - name string a display name for the account - empty first/last names will return the username
     *     - email string the email tied to the account used for passwords resets and the ilk
     *     - role string the role assigned to the account
     *     - avatar string if available, the url for the login's avatar
     *     - global_user_id int the globally unique user id for the user account connected to
     *     - dc_unique_id string the datacenter unique id for the user account connected to, like helper/account-details
     *     - account_name string The name of the account to which the API key belongs
     */
    public function profile() {
        $_params = array();
        return $this->master->call('users/profile', $_params);
    }

}


