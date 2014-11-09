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

    /**
     * Stores the array with user settings.
     *
     * @param $query
     */
    public function get_user_settings( $query )
	{
		if ( isset($query->query['rwp_settings']) ) {
			$this->user_settings = $query->query['rwp_settings'];
		}
	}

    /**
     * Returns an array with all attributes from the original <img> element
     *
     * @param $imageNode
     * @return array
     */
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

    /**
     * Finds <img> tags in the content and replaces it with a responsive image.
     *
     * @param $content
     * @return mixed
     */
    public function filter_images ( $content ) {
		// Cache $this. Javascript style for PHP 5.3
		$self = $this;

		$ignored_image_formats = get_option( 'ignored_image_formats' );
		if ( $ignored_image_formats ) {
			$ignored_image_formats = array_keys($ignored_image_formats);
			// Quick and dirty jpg/jpeg hack. I'm sorry.
			if ( in_array('jpg', $ignored_image_formats) ) {
				$ignored_image_formats[] = 'jpeg';
			}
		}
		// Find and replace all <img>
		$content = preg_replace_callback('/<img[^>]*>/', function ($match) use ($self, $ignored_image_formats) {
			$image_attributes = $self->get_attributes($match[0]);
			$src = $image_attributes['src'];

			// Return if the image format is ignored.
			if ( $ignored_image_formats ) {
				$image_info = pathinfo($src);
				if ( in_array($image_info['extension'], $ignored_image_formats) ) return $match[0];
			}
			
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
            $type = get_option( 'selected_element', 'img' );
			$picture = Picture::create( $type, $id, $settings );

			return $picture;
		}, $content);

	    return $content;
	}

    /**
     * @param $image_url
     * @return array
     */
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