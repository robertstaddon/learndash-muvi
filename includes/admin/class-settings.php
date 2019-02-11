<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * LearnDash_Muvi_Settings class
 * 
 * Requirement: The WorPress setting ID for storing the Muvi API authentication token
 *
 * This class is responsible for creating the LearnDash Muvi settings page
 */
class LearnDash_Muvi_Settings {
    
	// options page slug
	private $menu_slug = 'learndash_muvi';

	// options page settings group name
	private $settings_field_group = 'learndash_muvi_settings';
	
	// setting id for auth token
	private $auth_setting_id;
	
    /**
	 * Class __construct function
	 *
	 * @param str	@auth_setting_id
     */
    public function __construct( $auth_setting_id ) {

		// set $auth_setting_id
		$this->auth_setting_id = $auth_setting_id;

		// register our settings
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		
		// add our options page the the admin menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 11 );

        // add settings link in plugins menu
		add_filter( 'plugin_action_links_' . plugin_basename( LEARNDASH_MUVI_FILE ) , array( $this, 'admin_settings_link' ) );

		// add tab to LearnDash area
		add_filter( 'learndash_admin_tabs', array( $this, 'learndash_tab' ), 2, 1);

		return $this;

	}


	/**
	 * Add the Settings > LearnDash Muvi section.
	 */
	public function admin_menu() {
		add_submenu_page(
			'learndash-lms-non-existant',
			__( 'LearnDash Muvi Integration', LEARNDASH_MUVI_DOMAIN ),
			__( 'LearnDash Muvi', LEARNDASH_MUVI_DOMAIN ),
			'manage_options',
			$this->menu_slug,
			array( $this, 'settings_page' )
		);
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
			esc_url( admin_url( 'admin.php?page=' . $this->menu_slug ) ),
			esc_html( __( 'Settings', LEARNDASH_MUVI_DOMAIN ) )
		);
		return $links;
	}
	
	
	/**
	 * Implements hook admin_init to register our settings
	 */
	public function admin_init() {
        add_settings_section(
            'learndash_muvi_auth_section', 
            __( 'Authorization Settings', LEARNDASH_MUVI_DOMAIN ), 
            array( $this, 'learndash_muvi_auth_section_description' ), 
            $this->menu_slug
        );
    
		add_settings_field( 
			$this->auth_setting_id, 
			__( 'API Authorization Key', LEARNDASH_MUVI_DOMAIN ), 
			array( $this, 'learndash_muvi_auth_token_render' ), 
			$this->menu_slug, 
			'learndash_muvi_auth_section'
		);
		
		register_setting( $this->settings_field_group, $this->auth_setting_id );
	}

    /**
     * Output the description and field
     */
    public function learndash_muvi_auth_section_description() {
        echo
            '<p>An Authorization Key is required to connect to the Muvi API. <a href="https://www.muvi.com/help/using-muvi-api.html" target="_blank">Need help locating it?</a></p>';
    }
    public function learndash_muvi_auth_token_render() {        
        echo '<input name="' . $this->auth_setting_id  . '" id="' . $this->auth_setting_id  . '" type="password" value="' . get_option ( $this->auth_setting_id )  . '" size="40">';
    }
    
	/**
	 * Output the options/settings page
	 */
	public function settings_page() {
		?>
		<div class="wrap">
			<h2><?php print esc_html( get_admin_page_title() ); ?></h2>

			<form method="post" action="options.php">
				<?php
				settings_fields( $this->settings_field_group );
				do_settings_sections( $this->menu_slug );
				submit_button();
				?>
			</form>

		</div>
		<?php
	}

	/*
	 * Add tab in LearnDash
	 */
	public function learndash_tab( $admin_tabs ) {
		$admin_tabs['muvi'] = array(
			'link'  	=>      esc_url( admin_url( 'admin.php?page=' . $this->menu_slug ) ),
			'name'  	=>      __( "Muvi", LEARNDASH_MUVI_DOMAIN ),
			'id'    	=>      "admin_page_" . $this->menu_slug,
			'menu_link' =>      "edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses",
		);
		return $admin_tabs;
	}
}
