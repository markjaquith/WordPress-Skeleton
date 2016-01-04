<?php

class Postman_Mandrill_Whitelists {
    public function __construct(Postman_Mandrill $master) {
        $this->master = $master;
    }

    /**
     * Adds an email to your email rejection whitelist. If the address is
currently on your blacklist, that blacklist entry will be removed
automatically.
     * @param string $email an email address to add to the whitelist
     * @param string $comment an optional description of why the email was whitelisted
     * @return struct a status object containing the address and the result of the operation
     *     - email string the email address you provided
     *     - added boolean whether the operation succeeded
     */
    public function add($email, $comment=null) {
        $_params = array("email" => $email, "comment" => $comment);
        return $this->master->call('whitelists/add', $_params);
    }

    /**
     * Retrieves your email rejection whitelist. You can provide an email
address or search prefix to limit the results. Returns up to 1000 results.
     * @param string $email an optional email address or prefix to search by
     * @return array up to 1000 whitelist entries
     *     - return[] struct the information for each whitelist entry
     *         - email string the email that is whitelisted
     *         - detail string a description of why the email was whitelisted
     *         - created_at string when the email was added to the whitelist
     */
    public function getList($email=null) {
        $_params = array("email" => $email);
        return $this->master->call('whitelists/list', $_params);
    }

    /**
     * Removes an email address from the whitelist.
     * @param string $email the email address to remove from the whitelist
     * @return struct a status object containing the address and whether the deletion succeeded
     *     - email string the email address that was removed from the blacklist
     *     - deleted boolean whether the address was deleted successfully
     */
    public function delete($email) {
        $_params = array("email" => $email);
        return $this->master->call('whitelists/delete', $_params);
    }

}


