<?php
/*
Plugin Name: Responsify WP
Version: 1.4.3
Description: A WordPress plugin that creates the markup for responsive images.
Author: Stefan Ledin
Author URI: http://stefanledin.com
Plugin URI: https://github.com/stefanledin/responsify-wp
*/

require 'includes/media_queries.php';
require 'includes/picturefill.php';
require 'includes/element.php';
require 'includes/style.php';
require 'includes/picture.php';
require 'includes/content_filter.php';

class Responsify_WP
{
	const VERSION = '1.4.3';

	protected static $instance = null;

	public function __construct()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		$content_filter = new Content_Filter;
	}

	public static function get_instance()
	{
		if ( self::$instance == null ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script( 'picturefill', plugins_url('/src/picturefill.js', __FILE__),  null, null, true);
	}

}

add_action( 'plugins_loaded', array( 'Responsify_WP', 'get_instance') );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/responsify-wp-admin.php' );
	add_action( 'plugins_loaded', array( 'Responsify_WP_Admin', 'get_instance' ) );
}
