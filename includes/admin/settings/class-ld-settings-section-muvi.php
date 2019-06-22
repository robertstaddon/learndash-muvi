<?php
/**
 * LearnDash Settings Section for Muvi Metabox.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( ! class_exists( 'LearnDash_Settings_Section_Muvi' ) ) ) {
	/**
	 * Class to create the settings section.
	 */
	class LearnDash_Settings_Section_Muvi extends LearnDash_Settings_Section {

		/**
		 * Protected constructor for class
		 */
		protected function __construct() {
			$this->settings_page_id = 'learndash_muvi';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'learndash_muvi_settings';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'learndash_muvi_auth_section';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Authorization Settings', LEARNDASH_MUVI_DOMAIN );

			parent::__construct();
        }

		/**
		 * Initialize the metabox settings values.
		 */
		public function load_settings_values() {
            parent::load_settings_values();

            $this->setting_option_values = get_option( $this->setting_option_key );
        }

		/**
		 * Initialize the metabox settings fields.
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'auth_key' => array(
					'name'      => 'auth_key',
					'type'      => 'text',
					'label'     => esc_html__( 'API Authorization Key', LEARNDASH_MUVI_DOMAIN ),
					'help_text' => 'An Authorization Key is required to connect to the Muvi API. <a href="https://www.muvi.com/help/using-muvi-api.html" target="_blank">Need help locating it?</a>',
					'value'     => array_key_exists( 'auth_key', $this->setting_option_values ) ? $this->setting_option_values['auth_key'] : '',
					'class'     => '',
				),
			);

			parent::load_settings_fields();
		}
	}
}