<?php

class Content_Filter
{
	public $user_settings;

	public function __construct( $filter )
	{
		add_action( 'parse_query', array( $this, 'get_user_settings' ) );
		add_filter( $filter, array( $this, 'filter_images' ), 11 );
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
     * @param $image_node
     * @return array
     */
    public function get_attributes( $image_node )
	{
		$image_node = mb_convert_encoding($image_node, 'HTML-ENTITIES', 'UTF-8');
		$dom = new DOMDocument();
		$dom->loadHTML($image_node);
		$image = $dom->getElementsByTagName('img')->item(0);
		$attributes = array();
		foreach ( $image->attributes as $attr ) {
			$attributes[$attr->nodeName] = $attr->nodeValue;
		}
		return $attributes;
	}

	/**
	 * Returns array of ignored image formats
	 * @return array
	 */
	public function get_ignored_image_formats()
	{
		$ignored_image_formats = ( isset($this->user_settings['ignored_image_formats']) ) ?
			$this->user_settings['ignored_image_formats'] :
			get_option( 'ignored_image_formats' );
		if ( $ignored_image_formats ) {
			$ignored_image_formats = array_keys($ignored_image_formats);
			// Quick and dirty jpg/jpeg hack. I'm sorry.
			if ( in_array('jpg', $ignored_image_formats) ) {
				$ignored_image_formats[] = 'jpeg';
			}
		}
		return $ignored_image_formats;
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

		$ignored_image_formats = $this->get_ignored_image_formats();
		// Find and replace all <img>
		$content = preg_replace_callback('/<img[^>]*>/', function ($match) use ($self, $ignored_image_formats) {
			$settings = array(
				'attributes' => array(
					'img' => $self->get_img_attributes( $match[0] )
				),
				'retina' => ( get_option( 'rwp_retina', 'off' ) == 'off' ) ? false : true
			);
			$src = $settings['attributes']['img']['src'];
			$settings['notBiggerThan'] = $src;
			// We don't wanna have an src attribute on the <img>
			unset($settings['attributes']['img']['src']);
			
			$id = $self->url_to_attachment_id( $src );
			// If no ID is found, the image might be an external, hotlinked one.
			if ( !$id ) return $match[0];
			// Return if the image format is ignored.
			if ( $self->is_ignored_format( $src, $ignored_image_formats ) ) return $match[0];

			if ( $self->user_settings ) {
				$settings = array_merge_recursive($settings, $self->user_settings);
			}
			// Create responsive image markup.
            $type = get_option( 'selected_element', 'img' );
			return Picture::create( $type, $id, $settings );
		}, $content);

	    return $content;
	}

	/**
	 * Get array of attributes based on the original <img> node
	 * @param  $img 
	 * @return array
	 */
	public function get_img_attributes( $img )
	{
		$image_attributes = $this->get_attributes($img);
		if ( isset($this->user_settings['attributes']) ) {
			$array_values = array_values($this->user_settings['attributes']);
			if ( !is_array($array_values[0]) ) {
				$image_attributes = array_merge($image_attributes, $this->user_settings['attributes']);
			}
		}
		return $image_attributes;
	}

	/**
	 * Check if the image format is valid or not
	 * @param  string  $src
	 * @param  array  $ignored_image_formats
	 * @return boolean
	 */
	public function is_ignored_format( $src, $ignored_image_formats )
	{
		if ( $ignored_image_formats ) {
			$image_info = pathinfo($src);
			return in_array($image_info['extension'], $ignored_image_formats);
		}
		return false;
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