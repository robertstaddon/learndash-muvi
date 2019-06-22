<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * LearnDash_Muvi_Settings class
 *
 * This class is responsible for creating the LearnDash Muvi settings page
 */
class LearnDash_Muvi_Settings {
    
    // settings page id
    private $settings_page_id = 'learndash_muvi';

    // include path
    private $settings_include_path;
    
    /**
     * Class __construct function
     */
    public function __construct( ) {

        $this->settings_include_path = LEARNDASH_MUVI_PLUGIN_PATH . 'includes/admin/settings/';

        // add settings page
        add_action( 'learndash_settings_sections_init', array( $this, 'settings_section_init' ) );     
        add_action( 'learndash_settings_pages_init', array( $this, 'settings_page_init' ) );

        // add settings link in plugins menu
        add_filter( 'plugin_action_links_' . plugin_basename( LEARNDASH_MUVI_FILE ) , array( $this, 'admin_settings_link' ) );

        return $this;

    }

    /**
     * Settings Section init
     */
    public function settings_section_init() {
        include $this->settings_include_path . 'class-ld-settings-section-muvi.php';
        LearnDash_Settings_Section_Muvi::add_section_instance( 'muvi' );
    }

    /**
     * Settings Page init
     */
    public function settings_page_init() {
        include $this->settings_include_path . 'class-ld-settings-page-muvi.php';
        LearnDash_Settings_Page_Muvi::add_page_instance( 'muvi' );
    }
    
    
    /*
     * Adds plugin links.
     *
     * @param array $links
     * @param array $links with additional links
     */
    public function admin_settings_link( $links ) {
        $links[] = sprintf(
            '<a href="%s">%s</a>',
            esc_url( admin_url( 'admin.php?page=' . $this->settings_page_id ) ),
            esc_html( __( 'Settings', LEARNDASH_MUVI_DOMAIN ) )
        );
        return $links;
    }
    
}
