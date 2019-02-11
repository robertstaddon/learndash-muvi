<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * LearnDash_Muvi_REST_Server class
 * 
 * Requirement: A LearnDash_Muvi_User object for querying user information
 *
 * This class is responsible for providing REST enpoints.
 */
class LearnDash_Muvi_REST_Server extends WP_REST_Controller {

    // LearnDash_Muvi_User class
    private $muvi_user;
    
    //The namespace and version for the REST SERVER
    public $namespace = 'ld-muvi/v1';

    /**
     * Class __construct function
     * 
     * @param obj   $muvi_user
     */
    public function __construct( $muvi_user ) {

        // set $api
        $this->muvi_user = $muvi_user;

        // add actions
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );

    }

    /**
     * Register REST API Routes
     */
    public function register_routes() {
        $result = register_rest_route( $this->namespace, '/course-access', array(
            'methods' => 'GET',
            'callback' => array( $this, 'course_access_callback' )
        ) );

        $result = register_rest_route( $this->namespace, '/test', array(
            'methods' => 'GET',
            'callback' => array( $this, 'test_callback' )
        ) );
    }
    
    /**
     * Callback function for "test" endpoint
     */
    public function test_callback( $data ) {

        $response['result'] = 'Connection successful';
        $response['random-number'] = rand(1000, 9999);

        if ( class_exists( 'LiteSpeed_Cache_API' ) ) {
            LiteSpeed_Cache_API::set_nocache();
            $response['litespeed'] = "Successfully disabled LiteSpeed Cache on page";
        }

        $result = new WP_REST_Response( $response, 200 );

        // Set headers.
        $result->set_headers( array(
            'Cache-Control' => 'no-cache, must-revalidate, max-age=0'
        ) );

        return $result;
    }

    /**
     * Callback function for "course-access" endpoint
     * Needs course_id paramater and either Muvi user's "user_id" or "email"
     * 
     * @return WP_REST_Response     $result
     */
    public function course_access_callback( WP_REST_Request $request ) {

        $email = $request->get_param( 'email' );
        $muvi_user_id = $request->get_param( 'user_id' );
        $course_id = $request->get_param( 'course_id' );

        // Gather data for response
        if ( !empty($course_id) ) {
            if ( !empty( $email ) ) {
                $access = $this->muvi_user->user_email_has_access( $email, $course_id );
            } elseif ( !empty( $muvi_user_id ) ) {
                $access = $this->muvi_user->muvi_user_id_has_access( $muvi_user_id, $course_id );
            } else {
                $error = 'Requires either user_id or email paramater';
            }

        } else {
            $error = 'Requires course_id paramater';
        }

        // Create response
        if ( empty( $error ) ) {
            $response['code'] = 200;
            $response['status'] = 'OK';
            $response['access'] = $access;
        } else {
            $response['code'] = 407;
            $response['status'] = 'failure';
            $response['msg'] = $error;
        }

        $result = new WP_REST_Response( $response, 200 );

        // Disable caching
        $result->set_headers( array(
            'Cache-Control' => 'no-cache, must-revalidate, max-age=0'
        ) );
        if ( class_exists( 'LiteSpeed_Cache_API' ) ) {
            LiteSpeed_Cache_API::set_nocache();
        }

        return $result;
    }
}
