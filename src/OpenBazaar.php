<?php

/**
 * LICENSE
 *
 * This source file is subject to the GNU General Public License, Version 3
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @package    PHP Client Library for OpenBazaar API
 * @copyright  Copyright (c) 2016 Eugene Lifescale (a.k.a. Shaman) https://github.com/shaman/php-openbazaar-library
 * @sources    https://gist.github.com/drwasho
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License, Version 3
 */

class OpenBazaar {

    private $_curl;
    private $_error;
    private $_url;


    /*
     * Class Initialization
     *
     * @param string $username
     * @param string $password
     * @param string $protocol
     * @param string $host
     * @param string $port
     * @param string $certificate
     *
     */
    public function __construct($username,
                                $password,
                                $protocol    = 'http',
                                $host        = 'localhost',
                                $port        = 18469,
                                $certificate = false) {

        // Create session
        $this->_curl = curl_init();

        curl_setopt($this->_curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($this->_curl, CURLOPT_COOKIEJAR, true);
        curl_setopt($this->_curl, CURLOPT_COOKIEFILE, true);

        // API URL
        $this->_url = sprintf('%s://%s:%s/api/v1/', ($certificate ? 'https' : $protocol), $host, $port);

        // Connection attempt
        $this->_query(
            'POST',
            'login',
            array(
                'username' => $username,
                'password' => $password,
            )
        );

    }


    /*
     * Returns the profile data of the user’s node, or that of a target node.
     *
     * @param string $guid guid of the target node
     *
     */
    public function getProfile($guid = null) {

        return $this->_query(
            'GET',
            'profile',
            array(
                'guid' => $guid,
            )
        );

    }


    /*
     * Returns the profile image of the user’s node, or that of a target node.
     *
     * @param string $hash image hash
     * @param string $guid guid of the target node
     *
     */
    public function getImage($hash, $guid = null) {

        return $this->_query(
            'GET',
            'get_image',
            array(
                'hash' => $hash,
                'guid' => $guid,
            )
        );

    }


    /*
     * Returns the listings of the user’s node, or that of a target node.
     *
     * @param string $guid guid of the target node
     *
     */
    public function getListings($guid = null) {

        return $this->_query(
            'GET',
            'get_listings',
            array(
                'guid' => $guid,
            )
        );

    }


    /*
     * Returns the nodes following your node or a target's node.
     *
     * @param string $guid guid of the target node
     *
     */
    public function getFollowers($guid = null) {

        return $this->_query(
            'GET',
            'get_followers',
            array(
                'guid' => $guid,
            )
        );

    }


    /*
     * Returns the nodes being followed by your node or a target's node.
     *
     * @param string $guid guid of the target node
     *
     */
    public function getFollowing($guid = null) {

        return $this->_query(
            'GET',
            'get_following',
            array(
                'guid' => $guid,
            )
        );

    }


    /*
     * Returns the settings of your node
     *
     */
    public function getSettings() {

        return $this->_query(
            'GET',
            'settings'
        );

    }


    /*
     * Retrieve a history of all notifications your node has received. Notifications can be sent due to:
     *
     * - A node following you
     * - Events related to a purchase or sale
     *
     */
    public function getNotifications($limit) {

        return $this->_query(
            'GET',
            'get_notifications',
            array(
                'limit' => $limit,
            )
        );

    }


    /*
     * Retrieves all chat message received from other nodes.
     *
     * @param string $guid target node
     * @param int $limit max number of chat messages to return
     * @param int $start the starting point in the message list
     *
     */
    public function getChatMessages($guid, $limit = null, $start = null) {

        return $this->_query(
            'GET',
            'get_chat_messages',
            array(
                'guid'  => $guid,
                'limit' => $limit,
                'start' => $start,
            )
        );

    }


    /*
     * Retrieves a list of outstanding conversations.
     *
     */
    public function getChatConversations() {

        return $this->_query(
            'GET',
            'get_chat_conversations'
        );

    }


    /*
     * Retrieves the listings created by either your node or a target node.
     *
     */
    public function getContracts($id, $guid) {

        return $this->_query(
            'GET',
            'contracts',
            array(
                'id'    => $id,
                'guid'  => $guid,
            )
        );

    }


    /*
     * API call to cleanly disconnect from connected nodes and shutsdown the OpenBazaar server component.
     *
     */
    public function getShutdown() {

        return $this->_query(
            'GET',
            'shutdown'
        );

    }


    /*
     * Retrieves any sales made by the node.
     *
     */
    public function getSales() {

        return $this->_query(
            'GET',
            'get_sales'
        );

    }


    /*
     * Retrieves any purchases made by the node.
     *
     */
    public function getPurchases() {

        return $this->_query(
            'GET',
            'get_purchases'
        );

    }


    /*
     * Return error details
     *
     */
    public function getError() {

        return $this->_error;

    }


    /*
     * Follows a target node and will cause you to receive notifications from that node after certain event (e.g. new listing, broadcast messages) and share some metadata (in future).
     *
     * @param string $guid guid of the target node
     *
     */
    public function follow($guid) {

        return $this->_query(
            'POST',
            'follow',
            array(
                'guid'  => $guid,
            )
        );

    }


    /*
     * Stop following a target node, will cease to receive notifications and sharing metadata.
     *
     * @param string $guid guid of the target node
     *
     */
    public function unfollow($guid) {

        return $this->_query(
            'POST',
            'unfollow',
            array(
                'guid'  => $guid,
            )
        );

    }


    /*
     * Adds a social account to the user profile data of the user.
     *
     * @param string $account_type must be one of these; case sensitive: facebook, twitter, instagram, snapchat
     * @param string $username
     * @param string $proof URL proving ownership of the social account
     *
     */
    public function socialAccounts($account_type, $username, $proof) {

        return $this->_query(
            'POST',
            'social_accounts',
            array(
                'account_type' => $account_type,
                'username'     => $username,
                'proof'        => $proof,
            )
        );

    }


    /*
     * Sets your node as a Moderator, which is discoverable on the network.
     *
     */
    public function makeModerator() {

        return $this->_query(
            'POST',
            'make_moderator'
        );

    }


    /*
     * Removes the node as a Moderator and is no longer discoverable on the network as a Moderator.
     *
     */
    public function unmakeModerator() {

        return $this->_query(
            'POST',
            'unmake_moderator'
        );

    }


    /*
     * Upload images to your node.
     *
     * @param string $image Base 64 image data, no prefix
     * @param string $avatar Base 64 image data, no prefix
     * @param string $header Base 64 image data, no prefix
     *
     */
    public function uploadImage($image = null, $avatar = null, $header = null) {

        return $this->_query(
            'POST',
            'upload_image',
            array(
                'image'  => $image,
                'avatar' => $avatar,
                'header' => $header,
            )
        );

    }


    /*
     * Marks a notification as read in the database.
     *
     * @param string $id ID of the notification, 40 character hex string
     *
     */
    public function markNotificationAsRead($id) {

        return $this->_query(
            'POST',
            'mark_notification_as_read',
            array(
                'id'  => $id,
            )
        );

    }


    /*
     * Marks all chat messages with a specific node as read in the database.
     *
     * @param string $guid GUID of the party you are chatting with, 40 character hex string
     *
     */
    public function markChatMessageAsRead($guid) {

        return $this->_query(
            'POST',
            'mark_chat_message_as_read',
            array(
                'guid'  => $guid,
            )
        );

    }


    /*
     * Sends a Twitter-like message to all nodes that are following you.
     *
     * @param string $message 140 characters or less
     *
     */
    public function broadcast($message) {

        return $this->_query(
            'POST',
            'broadcast',
            array(
                'message'  => $message,
            )
        );

    }


    public function checkForPayment() {

        return $this->_query(
            'POST',
            'check_for_payment'
        );

    }

    // todo
    public function profile() {}
    public function contracts() {}
    public function purchaseContract() {}
    public function confirmOrder() {}
    public function completeOrder() {}
    public function settings() {}


    private function _query($method, $uri, array $args = array()) {

        switch ($method) {

            case 'GET':

                curl_setopt($this->_curl, CURLOPT_URL, $this->_url . $uri . '?' . http_build_query($args));
                curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($this->_curl, CURLOPT_POST, false);

                return $this->_response();

                break;

            case 'POST':

                curl_setopt($this->_curl, CURLOPT_URL, $this->_url . $uri);
                curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($this->_curl, CURLOPT_POST, true);
                curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $args);

                return $this->_response();

                break;
        }

        return false;
    }


    private function _response() {

        if (false === $response = curl_exec($this->_curl)) {

            $this->_error = curl_error($this->_curl);

            return false;

        } else {

            return json_decode($response, true);
        }
    }
}
