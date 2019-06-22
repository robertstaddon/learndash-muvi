<?php
/**
 * LearnDash Settings Page License.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( ! class_exists( 'LearnDash_Settings_Page_Muvi' ) ) ) :

class LearnDash_Settings_Page_Muvi extends LearnDash_Settings_Page {

    /**
     * Public constructor for class
     */
    public function __construct() {
        $this->parent_menu_page_url  = 'admin.php?page=learndash_lms_settings';
        $this->menu_page_capability  = LEARNDASH_ADMIN_CAPABILITY_CHECK;
        $this->settings_page_id      = 'learndash_muvi';
        $this->settings_page_title   = esc_html__( 'LearnDash Muvi Integration', LEARNDASH_MUVI_DOMAIN );
        $this->settings_tab_title    = esc_html__( 'Muvi', LEARNDASH_MUVI_DOMAIN );
        $this->settings_tab_priority = 45;
        $this->show_quick_links_meta = false;

        parent::__construct();
    }

}

endif;



