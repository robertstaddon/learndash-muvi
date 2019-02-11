<?php
/**
 * Plugin Name: LearnDash Muvi Integration
 * Description: Add functions for integrating LearnDash with the Muvi platform
 * Version: 1.1
 * Author: Abundant Designs LLC
 * Author URI: https://www.abundantdesigns.com/
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if class name already exists
if ( ! class_exists( 'LearnDash_Muvi' ) ) :

/**
* Main class
*/
class LearnDash_Muvi {

    /**
     * The LearnDash_Muvi instance
     *
     * @access private
     * @var object $instance
     */
    private static $instance;       

    /**
     * The LearnDash_Muvi_Settings instance
     *
     * @access private
     * @var object $settings
     */
    private static $settings;
    
    /**
     * The LearnDash_Muvi_API instance
     *
     * @access private
     * @var object $api
     */
    private static $api;

    /**
     * The LearnDash_Muvi_User instance
     *
     * @access private
     * @var object $api
     */
    private static $muvi_user;

    /**
     * The LearnDash_Muvi_REST_Server instance
     *
     * @access private
     * @var object $rest_server
     */
    private static $rest_server;

    /*
     * The setting id for the Muvi auth token (set in settings and referenced in the API)
     */
    private static $auth_setting_id = 'learndash-muvi-auth-token';

    /**
     * Instantiate the main class
     *
     * This function instantiates the class, initialize all functions and return the object.
     * 
     * @return object The LearnDash_Muvi instance.
     */
    public static function instance() {

        if ( ! isset( self::$instance ) && ( ! self::$instance instanceof LearnDash_Muvi ) ) {

            self::$instance = new LearnDash_Muvi();
            self::$instance->setup_constants();
            
            add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

            add_action( 'admin_init', array( self::$instance, 'require_dependencies' ) );
            
            self::$instance->includes();
            
        }

        return self::$instance;
    }
     
    /**
     * Function for setting up constants
     *
     * This function is used to set up constants used throughout the plugin.
     */
    public function setup_constants() {

        // Plugin version
        if ( ! defined( 'LEARNDASH_MUVI_VERSION' ) ) {
            define( 'LEARNDASH_MUVI_VERSION', '1.0' );
        }

        // Plugin text domain
        if ( ! defined( 'LEARNDASH_MUVI_DOMAIN' ) ) {
            define( 'LEARNDASH_MUVI_DOMAIN', 'learndash-muvi' );
        }
        
        // Plugin file
        if ( ! defined( 'LEARNDASH_MUVI_FILE' ) ) {
            define( 'LEARNDASH_MUVI_FILE', __FILE__ );
        }		

        // Plugin folder path
        if ( ! defined( 'LEARNDASH_MUVI_PLUGIN_PATH' ) ) {
            define( 'LEARNDASH_MUVI_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
        }

        // Plugin folder URL
        if ( ! defined( 'LEARNDASH_MUVI_PLUGIN_URL' ) ) {
            define( 'LEARNDASH_MUVI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }
    }

    /**
     * Load text domain used for translation
     *
     * This function loads mo and po files used to translate text strings used throughout the 
     * plugin.
     */
    public function load_textdomain() {

        // Set filter for plugin language directory
        $lang_dir = dirname( plugin_basename( LEARNDASH_MUVI_FILE ) ) . '/languages/';
        $lang_dir = apply_filters( 'learndash_muvi_languages_directory', $lang_dir );

        // Load plugin translation file
        load_plugin_textdomain( LEARNDASH_MUVI_DOMAIN, false, $lang_dir );
    }     
     
    /**
     * Require dependencies
     */
    public function require_dependencies() {
        if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
            if( !is_plugin_active( 'sfwd-lms/sfwd_lms.php' ) ) {
                add_action( 'admin_notices', array( $this, 'require_dependency_notice' ) );
                deactivate_plugins( plugin_basename( LEARNDASH_MUVI_FILE ) ); 
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        }
    }
    public function require_dependency_notice() {
      echo '<div class="error"><p>';
      echo __('Sorry, but <strong>LearnDash Muvi Integration</strong> requires <a href="http://learndash.com">LearnDash LMS</a> to be installed and activated.', LEARNDASH_MUVI_DOMAIN );
      echo '</p></div>';
    }

     
    /**
     * Includes all necessary PHP files
     *
     * This function is responsible for including all necessary PHP files.
     */
    public function includes() {		
        if ( is_admin() ) {
            include LEARNDASH_MUVI_PLUGIN_PATH . '/includes/admin/class-settings.php';
            self::$settings = new LearnDash_Muvi_Settings( self::$auth_setting_id );

        }
        
        include LEARNDASH_MUVI_PLUGIN_PATH . '/includes/class-muvi-api.php';
        self::$api = new LearnDash_Muvi_API( self::$auth_setting_id );

        include LEARNDASH_MUVI_PLUGIN_PATH . '/includes/class-user.php';
        self::$muvi_user = new LearnDash_Muvi_User( self::$api );

        include LEARNDASH_MUVI_PLUGIN_PATH . '/includes/class-rest-server.php';
        self::$rest_server = new LearnDash_Muvi_REST_Server( self::$muvi_user );

    }

}

endif; // End if class exists check

/**
 * The main function for returning instance
 */
function launch_learndash_muvi() {
    return LearnDash_Muvi::instance();
}

// Run plugin
launch_learndash_muvi();
