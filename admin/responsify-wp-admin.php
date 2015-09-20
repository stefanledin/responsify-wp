<?php

class Responsify_WP_Admin
{
	protected static $instance = null;
	protected $plugin;

	public function __construct()
	{
		$this->plugin = Responsify_WP::get_instance();
		add_action( 'admin_menu', array( $this, 'add_settings_to_menu') );
		add_action( 'admin_menu', array( $this, 'enqueue_scripts') );
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
		add_options_page( 'Responsify WP', 'RWP Settings', 'manage_options', 'responsify-wp', array( $this, 'view_settings_page' ) );
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style( 'rwp_stylesheet', plugins_url( '/css/responsify-wp.css', __FILE__ ), null, null );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'backbone' );
		wp_enqueue_script( 'rwp_scripts', plugins_url( '/js/responsify-wp.js', __FILE__ ), array( 'jquery', 'backbone' ), '1.9', true );
	}

	public function view_settings_page()
	{
		include 'views/settings.php';
	}

	public function register_plugin_settings()
	{
		register_setting( 'responsify-wp-settings', 'selected_sizes' );
		register_setting( 'responsify-wp-settings', 'globally_active' );
		register_setting( 'responsify-wp-settings', 'rwp_added_filters' );
		register_setting( 'responsify-wp-settings', 'rwp_picturefill' );
		register_setting( 'responsify-wp-settings', 'rwp_retina' );
		register_setting( 'responsify-wp-settings', 'selected_element' );
		register_setting( 'responsify-wp-settings', 'ignored_image_formats' );
		register_setting( 'responsify-wp-settings', 'rwp_custom_media_queries' );
		register_setting( 'responsify-wp-settings', 'rwp_debug_mode' );
	}

}