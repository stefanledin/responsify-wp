<?php
/**
* fjpgd
*/
class Picturefill_WP_Helper_Admin
{
	protected static $instance = null;
	protected $plugin;

	public function __construct()
	{
		$this->plugin = Picturefill_WP_Helper::get_instance();
		add_action( 'admin_menu', array( $this, 'add_settings_to_menu') );
		add_action( 'admin_init', array( $this, 'register_plugin_settings' ) );
	}

	public static function get_instance()
	{
		if ( self::$instance == null ) {
			self::$instance = new self;
		}
	}

	public function add_settings_to_menu()
	{
		$plask = add_options_page( 'Page title', 'Menu title', 'manage_options', 'picturefill-wp-helper', array( $this, 'view_settings_page' ) );
	}

	public function view_settings_page()
	{
		include 'views/settings.php';
	}

	public function register_plugin_settings()
	{
		register_setting( 'responsive-images-helper-settings', 'selected_sizes' );
	}

}