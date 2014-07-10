<?php

class Content_Filter
{
	protected $user_settings;

	public function __construct()
	{
		add_action( 'parse_query', array( $this, 'get_user_settings' ) );
		add_filter( 'the_content', array( $this, 'filter_images' ) );
	}

	public function get_user_settings( $query )
	{
		if ( isset($query->query['rwp_settings']) ) {
			$this->user_settings = $query->query['rwp_settings'];
		}
	}

	public function filter_images ( $content ) {
		$self = $this;
		$content = preg_replace_callback('/<img (.*) \/>\s*/', function ($match) use ($self) {
			preg_match('/src="([^"]+)"/', $match[0], $src);
			$id = $self->url_to_attachment_id($src[1]);
			$settings = array(
				'notBiggerThan' => $src[1]
			);
			// Can't this be done in a better way?
			if ( $self->user_settings ) {
				foreach ( $self->user_settings as $user_setting_key => $user_setting_value ) {
					$settings[$user_setting_key] = $user_setting_value;
				}
			}
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