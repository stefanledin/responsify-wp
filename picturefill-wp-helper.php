<?php
/*
Plugin Name: Picturefill WP helper
Version: 0.1-beta
Description: A helper class for creating the markup required for PictureFill (and more!)
Author: Stefan Ledin
Author URI: http://stefanledin.com
Plugin URI: https://github.com/stefanledin/picturefill-wp-helper
*/

require 'includes/picturefill.php';
require 'includes/element.php';
require 'includes/style.php';

function url_to_attachment_id ( $image_url ) {
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

function filter_images ( $content ) {
	$content = preg_replace_callback('/<img (.*) \/>\s*/', function ($match) {
		preg_match('/src="([^"]+)"/', $match[0], $src);
		$id = url_to_attachment_id($src[1]);
		$picture = Picture::create('element', $id, array(
			'notBiggerThan' => $src[1]
		));
		return $picture;
	}, $content);
    return $content;
}
add_filter( 'the_content', 'filter_images' );

function enqueue_scripts() {
	wp_enqueue_script('picturefill', plugins_url('/picturefill.js', __FILE__));
}
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

// A factory class which creates an instance of the proper class.
class Picture
{
	public static function create ( $type, $id, $settings = null )
	{
		switch (strtolower($type)) {
			case 'element':
				$picture = new Element( $id, $settings );
				return $picture->markup;
				break;
			case 'style':
				$picture = new Style( $id, $settings );
				return $picture->style;
				break;
		}
	}
}