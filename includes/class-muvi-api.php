<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * LearnDash_Muvi_API class
 * 
 * Requirement: The WorPress setting ID where it may retrieve the Muvi API authentication token using get_option()
 *
 * This class is responsible for communicating with the Muvi API.
 */
class LearnDash_Muvi_API {

	// Setting id for auth token
	private $auth_setting_id;
    
    // Root URL for Muvi API
    private $api_url = 'https://www.muvi.com/rest/';


    /**
     * Class __construct function
     * 
     * @param str   $auth_setting_id
     */
    public function __construct( $auth_setting_id ) {

		// set $auth_setting_id
        $this->auth_setting_id = $auth_setting_id;
    }

    /**
     * Create Muvi Account
     * 
     * @param str   $email
     * @param str   $name
     * @param str   $password
     * @return str  $api_response
     */
    public function create_account( $email, $name, $password ) {

        return $this->post( 'registerUser', array(
            'email' => $email,
            'name' => $name,
            'password' => $password
        ) );

    }

    /**
     * Udate Muvi Account Password
     * 
     * @param str   $muvi_user_id
     * @param str   $password
     * @return str  $api_response
     */
    public function update_password( $muvi_user_id, $password ) {

        return $this->post( 'updateUserProfile', array(
            'user_id' => $muvi_user_id,
            'password' => $password
        ) );

    }

    /**
	 * Post to the Muvi API
     *
     * @param  str      $method
     * @param  array    $url_params
     * @return str      $api_response
	 */
    public function post( $method, $url_params = array() ) {
        // Add Authentication Key
        $url_params[ 'authToken' ] = get_option( $this->auth_setting_id );

        // Post to Muvi API
        $response = wp_remote_post( $this->api_url . $method . '?' . http_build_query( $url_params ) );
        $api_response = json_decode( wp_remote_retrieve_body( $response ), true );
        return $api_response;
    }

    /**
	 * Get from the Muvi API
     *
     * @param  str      $method
     * @param  array    $url_params
     * @return str      $api_response
	 */
    public function get( $method, $url_params = array() ) {
        // Add Authentication Key
        $url_params[ 'authToken' ] = get_option( $this->auth_setting_id );

        // Get from Muvi API
        $response = wp_remote_get( $this->api_url . $method . '?' . http_build_query( $url_params ) );
        $api_response = json_decode( wp_remote_retrieve_body( $response ), true );
        return $api_response;
    }
}
