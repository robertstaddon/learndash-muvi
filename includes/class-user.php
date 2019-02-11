<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * LearnDash_Muvi_User class
 * 
 * Requirement: A LearnDash_Muvi_API object for querying the Muvi API
 *
 * This class is responsible for providing functions and properties for interacitng with the Muvi user
 */
class LearnDash_Muvi_User {

    // LearnDash_Muvi_API class
    private $api;
    
    // User meta key for holding the Muvi user id
    private $user_meta_key_muvi_user = 'learndash_muvi_user_id';

    /**
     * Class __construct function
     * 
     * @param obj   $api 
     */
    public function __construct( $api ) {

        // set $api
        $this->api = $api;

        // add actions
        add_action( 'learndash_update_course_access', array( $this, 'course_access_updated'), 10, 4 );
        add_action( 'profile_update', array( $this, 'profile_updated' ) );
        add_action( 'password_reset', array( $this, 'reset_password' ), 10, 2 );
    }

    /**
     * When a WordPress user's LearnDash course access is updated, create a Muvi user if one doesn't already exist
     * 
     * @param  int  	    $user_id
     * @param  int  	    $course_id
     * @param  array  	    $access_list
     * @param  bool  	    $remove
     * @return int/bool     $primary_key_id or false
     */
    public function course_access_updated( $user_id, $course_id, $access_list, $remove ) {
        $muvi_user_id = $this->get_muvi_user_id( $user_id );

        if ( empty( $muvi_user_id ) ) {
            $wp_user = get_userdata( $user_id );

            $response = $this->api->create_account( $wp_user->user_email, $wp_user->first_name . $wp_user->last_name, $wp_user->user_pass );

            if ( $response['status'] == 'OK' ) {
                return add_user_meta( $user_id, $this->user_meta_key_muvi_user, $response['id'], true );
            } else {
                error_log( 'Problem creating Muvi Account through the API: ' . var_export( $response, true ) );
                return false;
            }

        } else {
            return false;
        }

    }

    /**
     * Update a Muvi user's password after updating their profile
     * 
     * @param int       $user_id
     * @return array    $response or nothing
     */
    function profile_updated( $user_id ) {
        if ( ! isset( $_POST['pass1'] ) || '' == $_POST['pass1'] ) {
            return;
        }

        $muvi_user_id = $this->get_muvi_user_id( $user_id );
        if ( !empty( $muvi_user_id ) ) {
            return $this->api->update_password( $muvi_user_id, $_POST['pass1'] );
        }
    }
    
    /**
     * Update a Muvi user's password after they reset their WordPress password
     * 
     * @param obj       $user
     * @param str       $new_pass
     * @return array    $response or nothing
     */
    public function reset_password( $user, $new_pass ) {
        $muvi_user_id = $this->get_muvi_user_id( $user->ID );
        if ( !empty( $muvi_user_id ) ) {
            return $this->api->update_password( $muvi_user_id, $new_pass );
        }
    }

    /**
     * Is the uesr with this email enrolled in this LearnDash course id?
     * 
     * @param str       $email
     * @param int       $course_id
     * @return bool     $has_access
     */
    public function user_email_has_access( $email, $course_id ) {
        $wp_user = get_user_by( 'email', $email );
        $course_id = learndash_get_course_id( $course_id );

        if ( !empty( $wp_user ) && !empty( $course_id )  ) {
            return sfwd_lms_has_access( $course_id, $wp_user->id );
        } else {
            return false;
        }
    }

    /**
     * Is Muvi user id enrolled in this LearnDash course id?
     * 
     * @param int       $muvi_user_id
     * @param int       $course_id
     * @return bool     $has_access
     */
    public function muvi_user_id_has_access( $muvi_user_id, $course_id ) {
        $wp_user_id = $this->get_wp_user_id( $muvi_user_id );
        $course_id = learndash_get_course_id( $course_id );

        if ( !empty( $wp_user_id ) && !empty( $course_id ) ) {
            return sfwd_lms_has_access( $course_id, $wp_user_id );
        } else {
            return false;
        }
    }

    /**
     * Get the Muvi user ID from a WordPress user ID
     * 
     * @param int   $wp_user_id
     * @return int  $muvi_user_id
     */
    public function get_muvi_user_id( $wp_user_id ) {
        return get_user_meta( $wp_user_id, $this->user_meta_key_muvi_user, true );
    }

    /**
     * Get the WordPress user ID from a Muvi user ID
     * 
     * @param int           $muvi_user_id
     * @return int/bool     $wp_user_id or false
     */
    public function get_wp_user_id( $muvi_user_id ) {
        $users = get_users( array( 'meta_key' => $this->user_meta_key_muvi_user, 'meta_value' => $muvi_user_id ) );

        if ( !empty( $users ) ) {
            foreach ( $users as $user ) {
                return $user->ID;
            }
        } else {
            return false;
        }
    }

}
