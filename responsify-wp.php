<?php
/*
Plugin Name: Responsify WP
Version: 1.9.11
Description: Responsify WP is the WordPress plugin that cares about responsive images.
Author: Stefan Ledin
Author URI: http://stefanledin.com
Plugin URI: https://github.com/stefanledin/responsify-wp
*/

require 'includes/Logger.php';
require 'includes/media_queries.php';
require 'includes/retina.php';
require 'includes/custom_media_query_rules.php';
require 'includes/custom_media_queries.php';
require 'includes/create_responsive_image.php';
require 'includes/img.php';
require 'includes/native-img.php';
require 'includes/element.php';
require 'includes/native-element.php';
require 'includes/span.php';
require 'includes/style.php';
require 'includes/picture.php';

require 'includes/rwp_functions.php';

require 'includes/content_filter.php';

class Responsify_WP
{
	const VERSION = '1.9.11';

	protected static $instance = null;

    /**
     * Adds actions and filters.
     */
    public function __construct()
	{
        $this->disable_native_responsive_images();
        
        if ( get_option( 'rwp_picturefill', 'on' ) == 'on' ) {
          add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        }
        add_action( 'after_setup_theme', array( $this, 'apply_content_filters' ) );
        
        
        add_filter('plugin_action_links_'.plugin_basename(__FILE__), array( $this, 'settings_link' ) );
	}

    /**
     * WordPress 4.4 and above has native support
     * for responsive images. This feature collides with
     * RWP and has to be disabled in order for RWP to work.
     */
    public function disable_native_responsive_images()
    {
        remove_filter( 'the_content', 'wp_make_content_images_responsive' );
        add_filter( 'wp_calculate_image_srcset', array( $this, 'remove_wp_calculate_image_srcset_filter' ) );
    }
    public function remove_wp_calculate_image_srcset_filter( $image_data )
    {
        return false;
    }

    /**
     * Creates the singleton
     *
     * @return null|Responsify_WP
     */
    public static function get_instance()
	{
		if ( self::$instance == null ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

    /**
     * Creates a new instance of Content_Filter for each filter that
     * RWP should apply it's magic on.
     */
    public function apply_content_filters()
    {
        $default_filters = array( 'the_content' => 'on', 'post_thumbnail_html' => 'on' );
        $filters = get_option( 'rwp_added_filters', $default_filters );
        if ( is_array($filters) ) {
            $filters = array_keys($filters);
        }
        if ( has_filter( 'rwp_add_filters' ) ) {
            $filters = apply_filters( 'rwp_add_filters', $filters );
        }
        if ( !$filters ) return;
        foreach ( $filters as $filter ) {
            new Content_Filter( $filter );
        }
    }

    /**
     * Add settings link on plugin page.
     *
     * @param $links
     * @return mixed
     */
    public function settings_link( $links ) {
        $settings_link = '<a href="options-general.php?page=responsify-wp.php">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Enqueues right version of Picturefill.
     */
    public function enqueue_scripts()
	{
		$selected_element = get_option( 'selected_element', 'img' );
		if ( $selected_element == 'span' ) {
            wp_enqueue_script( 'picturefill', plugins_url('/src/picturefill.1.2.1.js', __FILE__),  null, null, true);
        } else {
            wp_enqueue_script( 'picturefill', plugins_url('/src/picturefill.3.0.1.min.js', __FILE__),  null, null, true);
        }
	}
}

add_action( 'plugins_loaded', array( 'Responsify_WP', 'get_instance') );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/responsify-wp-admin.php' );
	add_action( 'plugins_loaded', array( 'Responsify_WP_Admin', 'get_instance' ) );
}
