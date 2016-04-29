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
    private $_url;

    private $_error;


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
                                $protocol    = 'http',      // todo
                                $host        = 'localhost', // todo
                                $port        = 18469,       // todo
                                $certificate = null         // todo
    ) {

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
    public function shutdown() {

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


    /*
     * Creates a listing contract, which is saved to the database and local file system,
     * as well as publish the keywords in the distributed hash table.
     *
     * @param string $expiration_date The date the contract should expire in string formatted UTC datetime, empty string if the contract never expires
     * @param string $metadata_category Select from: physical good, digital good and service.
     * @param string $title Title of the product for sale.
     * @param string $description Description of the item, content or service.
     * @param string $currency_code The currency the product is priced in may either be “btc” or a currency from this list
     * @param float  $price The price per unit in the same currency as currency_code.
     * @param string $process_time The time it will take to prepare the item for shipping.
     * @param bool   $nsfw Is the item not suitable for work (i.e. 18+).
     * @param string $shipping_origin Required and only applicable if the metadata_category is a physical good.Where the item ships from.Must be a formatted string from this list.
     * @param string $shipping_regions Required and only applicable if the metadata_category is a physical good.A list of countries/regions where the product will ship to.Each item in the list must be formatted from this list.
     * @param string $est_delivery_domestic Estimated delivery time for domestic shipments.
     * @param string $est_delivery_international Estimated delivery time for international shipments.
     * @param string $terms_conditions Any terms or conditions the user wishes to include.
     * @param string $returns Return policy.
     * @param string $shipping_currency_code The currency code used to price shipping. may either be “btc” or a currency from this list.
     * @param string $shipping_domestic The price of domestic shipping in the selected currency code.
     * @param string $shipping_international The price of nternational shipping in the selected currency code.
     * @param string $keywords A list of string search terms for the listing.Must be fewer than 10.
     * @param string $category A user-generated category for this product.Will show in store’s category list.
     * @param string $condition The condition of the product.
     * @param string $sku Stock keeping unit (sku) for the listing.
     * @param string $images 40 character hex string.A list of SHA256 image hashes.The images should be uploaded using the upload_image api call.
     * @param bool $free_shipping Can be "true" or "false".
     * @param string $moderators GUID: 40 character hex string.A list of moderator GUIDs that the vendor wishes to use.Note: the moderator must have been previously returned by the get_moderators websocket call.Given the UI workflow, this call should always be made before the contract is set.
     * @param string $options A list of options for the product.For example, given “color” in the options list, choose from "red", "green", "purple" etc.
     */

    public function contracts($expiration_date,
                              $metadata_category,
                              $title,
                              $description,
                              $currency_code,
                              $price,
                              $process_time,
                              $nsfw,
                              $est_delivery_domestic,
                              $est_delivery_international,
                              $terms_conditions,
                              $returns,
                              $shipping_currency_code,
                              $category,
                              $condition,
                              $shipping_origin,
                              $shipping_regions,
                              $shipping_domestic,
                              $shipping_international,
                              $keywords,
                              $sku,
                              $images,
                              $free_shipping,
                              $moderators,
                              $options
        ) {

        return $this->_query(
            'POST',
            'contracts',
            array(
                'expiration_date'            => $expiration_date,
                'metadata_category'          => $metadata_category,
                'title'                      => $title,
                'description'                => $description,
                'currency_code'              => $currency_code,
                'price'                      => $price,
                'process_time'               => $process_time,
                'nsfw'                       => $nsfw,
                'est_delivery_domestic'      => $est_delivery_domestic,
                'est_delivery_international' => $est_delivery_international,
                'terms_conditions'           => $terms_conditions,
                'returns'                    => $returns,
                'shipping_currency_code'     => $shipping_currency_code,
                'category'                   => $category,
                'condition'                  => $condition,
                'shipping_origin'            => $shipping_origin,
                'shipping_regions'           => $shipping_regions,
                'shipping_domestic'          => $shipping_domestic,
                'shipping_international'     => $shipping_international,
                'keywords'                   => $keywords,
                'sku'                        => $sku,
                'images'                     => $images,
                'free_shipping'              => $free_shipping,
                'moderators'                 => $moderators,
                'options'                    => $options,
            )
        );

    }



    /*
     * Add data related to the node's profile into the database, which will be visible to other nodes.
     *
     * @param string $name Required.Must be set on the firt call to create the profile, but can be omitted on subsequent calls to update other fields in the profile.
     * @param string $location Country code.Required.Must be set on the firt call to create the profile, but can be omitted on subsequent calls to update other fields in the profile.
     * @param string $handle String starting with @.The Blockchain ID handle starting with “@”.Eventually this will be required to resolve to the guid.
     * @param string $about 'About' text for the store.
     * @param string $short_description Text to show in the homepage store list (string).
     * @param bool   $nsfw Is this user profile/store nsfw? Will default to false if the field is omitted. (“true” or “false”).
     * @param bool   $moderator Is this user a moderator? must be set to true if so. defaults to false if omitted. (“true” or “false”).
     * @param bool   $vendor Is this user a vendor? must be set to true if so. defaults to false if omitted. (“true” or “false”).
     * @param string $website A website for this user (string).
     * @param string $email An email address for this user (string).
     * @param int    $primary_color Hex color formatted in base 10. For example, 00FF00 should be sent as “65280” (string of base 10 formatted hex color).
     * @param int    $secondary_color Same as primary color.
     * @param int    $text_color Same as primary color.
     * @param int    $background_color Same as primary color.
     * @param string $avatar The hash of the avatar image. must have been previously uploaded using the upload_image api call (40 character hex string).
     * @param string $header The hash of the header image. must have been previously uploaded using the upload_image api call. (40 character hex string).
     * @param string $pgp_key A pgp public key to include in the profile. if included the signature field must also be included. (string pgp public key block).
     *
     */

    public function profile($name,
                            $location,
                            $handle,
                            $about,
                            $short_description,
                            $nsfw,
                            $vendor,
                            $moderator,
                            $website,
                            $email,
                            $primary_color,
                            $secondary_color,
                            $text_color,
                            $background_color,
                            $avatar,
                            $header,
                            $pgp_key
        ) {

        return $this->_query(
            'POST',
            'profile',
            array(
                'name'              => $name,
                'location'          => $location,
                'handle'            => $handle,
                'about'             => $about,
                'short_description' => $short_description,
                'nsfw'              => $nsfw,
                'vendor'            => $vendor,
                'moderator'         => $moderator,
                'website'           => $website,
                'email'             => $email,
                'primary_color'     => $primary_color,
                'secondary_color'   => $secondary_color,
                'text_color'        => $text_color,
                'background_color'  => $background_color,
                'avatar'            => $avatar,
                'header'            => $header,
                'pgp_key'           => $pgp_key,

            )
        );

    }


    /*
     * Purchases a contract by sending the purchase into the Vendor. The Buyer waits for a response to indicate
     * whether the purchase is successful or not. If successful, the Buyer needs to fund the direct or multisig address.
     *
     * @param string $id 40 character hex string.The contract id to be purchased.Note: the contract must be in cache, meaning it must have been called specifically at least once.
     * @param string $quantity Number of items to be purchased.
     * @param string $ship_to Name of the person that the item will be shipped to.
     * @param string $address Street address for delivery of the item.
     * @param string $city Name of the city corresponding to the address.
     * @param string $state Name of the state corresponding to the address.
     * @param string $postal_code Postal code corresponding to the address.
     * @param string $moderator 40 character hex string.The Moderator, listed in the original contract, chosen by the buyer.This is omitted if there is a direct payment.
     * @param string $options E.g. "color".
     *
     */


    public function purchaseContract($id,
                                     $quantity,
                                     $ship_to,
                                     $address,
                                     $city,
                                     $state,
                                     $postal_code,
                                     $moderator,
                                     $options


    ) {

        return $this->_query(
            'POST',
            'purchase_contract',
            array(
                'id'          => $id,
                'quantity'    => $quantity,
                'ship_to'     => $ship_to,
                'address'     => $address,
                'city'        => $city,
                'state'       => $state,
                'postal_code' => $postal_code,
                'moderator'   => $moderator,
                'options'     => $options,

            )
        );

    }


    // todo


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

        }

        if (false === $response = json_decode($response, true)) {

            $this->_error = 'cannot decode raw data';

            return false;

        }

        if (false === (bool) $response['success']) {

            $this->_error = $response['reason'];

            return false;

        }

        return $response;
    }
}
