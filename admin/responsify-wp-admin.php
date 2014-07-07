<?php

class Responsify_WP_Admin
{
	protected static $instance = null;
	protected $plugin;

	public function __construct()
	{
		$this->plugin = Responsify_WP::get_instance();
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
		$plask = add_options_page( 'Responsify WP', 'RWP Settings', 'manage_options', 'responsify-wp', array( $this, 'view_settings_page' ) );
	}

	public function view_settings_page()
	{
		include 'views/settings.php';
	}

	public function register_plugin_settings()
	{
		register_setting( 'responsify-wp-settings', 'selected_sizes' );
	}

}