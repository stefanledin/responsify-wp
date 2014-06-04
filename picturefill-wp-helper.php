<?php
/*
Plugin Name: Picturefill WP helper
Version: 1.1
Description: A helper class for creating the markup required for PictureFill (and more!)
Author: Stefan Ledin
Author URI: http://stefanledin.com
Plugin URI: https://github.com/stefanledin/picturefill-wp-helper
*/

require 'includes/picturefill.php';
require 'includes/element.php';
require 'includes/style.php';
require 'includes/picture.php';

class Picturefill_WP_Helper
{
	const VERSION = '1.1';

	protected static $instance = null;

	public function __construct()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'the_content', array( $this, 'filter_images' ) );
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
		wp_enqueue_script( 'picturefill', plugins_url('/picturefill.js', __FILE__) );
	}

	/**
	 * This breaks SRP and should be abstracted to another class.
	 */
	public function filter_images ( $content ) {
		$content = preg_replace_callback('/<img (.*) \/>\s*/', function ($match) {
			preg_match('/src="([^"]+)"/', $match[0], $src);
			$id = $this->url_to_attachment_id($src[1]);
			$picture = Picture::create('element', $id, array(
				'notBiggerThan' => $src[1]
			));
			return $picture;
		}, $content);
	    return $content;
	}
	protected function url_to_attachment_id ( $image_url ) {
		// Thx to https://github.com/kylereicks/picturefill.js.wp/blob/master/inc/class-model-picturefill-wp.php
		global $wpdb;
		$original_image_url = $image_url;
		$image_url = preg_replace('/^(.+?)(-\d+x\d+)?\.(jpg|jpeg|png|gif)((?:\?|#).+)?$/i', '$1.$3', $image_url);
		$prefix = $wpdb->prefix;
		$attachment_id = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $image_url ));
		if ( !empty($attachment_id) ) {
			return $attachment_id[0];
		} else {
			$attachment_id = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $original_image_url ));
		}
		return !empty($attachment_id) ? $attachment_id[0] : false;
	}
}

add_action( 'plugins_loaded', array( 'Picturefill_WP_Helper', 'get_instance') );
