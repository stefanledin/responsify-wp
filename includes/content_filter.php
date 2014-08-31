<?php

class Content_Filter
{
	public $user_settings;

	public function __construct()
	{
		add_action( 'parse_query', array( $this, 'get_user_settings' ) );
		if ( get_option( 'globally_active', 'on' ) == 'on' ) {
			add_filter( 'the_content', array( $this, 'filter_images' ), 11 );
		}
	}

	public function get_user_settings( $query )
	{
		if ( isset($query->query['rwp_settings']) ) {
			$this->user_settings = $query->query['rwp_settings'];
		}
	}

	public function get_attributes( $imageNode )
	{
		$dom = new DOMDocument();
		$dom->loadHTML($imageNode);
		$image = $dom->getElementsByTagName('img')->item(0);
		$attributes = array();
		foreach ( $image->attributes as $attr ) {
			$attributes[$attr->nodeName] = $attr->nodeValue;
		}
		return $attributes;
	}

	public function filter_images ( $content ) {
		// Cache $this. Javascript style for PHP 5.3
		$self = $this;

		// Find and replace all <img>
		$content = preg_replace_callback('/<img[^>]*>/', function ($match) use ($self) {
			$image_attributes = $self->get_attributes($match[0]);
			$src = $image_attributes['src'];
			// We don't wanna have an src attribute on the <img>
			unset($image_attributes['src']);

			$id = $self->url_to_attachment_id($src);

			// If no ID is found, the image might be an external, hotlinked one.
			if ( !$id ) return $match[0];

			// Basic settings for Picture::create()
			$settings = array(
				'notBiggerThan' => $src,
				'attributes' => array(
					'img' => $image_attributes
				)
			);

			// Add user settings to the $settings array. 
			// (Can't this be done in a better way?)
			if ( $self->user_settings ) {
				foreach ( $self->user_settings as $user_setting_key => $user_setting_value ) {
					$settings[$user_setting_key] = $user_setting_value;
				}
			}

			// Create responsive image markup.
			$picture = Picture::create( 'element', $id, $settings );

			return $picture;
		}, $content);

	    return $content;
	}

	public function url_to_attachment_id ( $image_url ) {
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