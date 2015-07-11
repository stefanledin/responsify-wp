<?php
class Custom_Media_Queries {

	protected $custom_media_queries;
	protected $rwp_settings = array();

	public function __construct( $custom_media_queries )
	{
		$this->custom_media_queries = $custom_media_queries;
	}

	public function should_be_applied_when( $type, $data )
	{
		$this->rwp_settings = array();
		$method_to_call = 'check_rule_for_' . $type;
		call_user_func( array($this, $method_to_call), $data );
		return ( count($this->rwp_settings) > 0 );
	}

	public function get_settings()
	{
		$rwp_settings = $this->rwp_settings;
		$this->rwp_settings = array();
		return $rwp_settings;
	}

	protected function check_rule_for_post( $post_object )
	{
		foreach ( $this->custom_media_queries as $media_query ) {
			if ( $media_query['rule']['default'] == 'true' ) {
				$this->apply_custom_media_queries( $media_query );
			}

			$key = $media_query['rule']['when']['key'];
			$value = $media_query['rule']['when']['value'];
			if ( $media_query['rule']['when']['compare'] == '==' ) {
				if ( $key == 'page-id' ) {
					if ( $post_object->ID == (int) $value ) {
						$this->apply_custom_media_queries( $media_query );
					}
				}
				if ( $key == 'page-slug' ) {
					if ( $post_object->post_name == $value ) {
						$this->apply_custom_media_queries( $media_query );
					}
				}		
			} else {
				if ( $key == 'page-id' ) {
					if ( $post_object->ID != (int) $value ) {
						$this->apply_custom_media_queries( $media_query );
					}
				}
				if ( $key == 'page-slug' ) {
					if ( $post_object->post_name != $value ) {
						$this->apply_custom_media_queries( $media_query );
					}
				}
			}
			
			
		}
	}
	protected function check_rule_for_image( $attributes )
	{
		foreach ($this->custom_media_queries as $media_query) {
			if ( $media_query['rule']['default'] == 'true' ) {
				$this->apply_custom_media_queries( $media_query );
			}
			$key = $media_query['rule']['when']['key'];
			$value = $media_query['rule']['when']['value'];

			if ( $key == 'image' ) {
				if ( is_integer( strpos($attributes['img']['class'], 'size-' . $value) ) ) {
					$this->apply_custom_media_queries( $media_query );
				}
			}
		}
	}

	/**
	 * Builds up an $rwp_settings array
	 * @param array $custom_media_query
	 * @return array 
	 */
	protected function apply_custom_media_queries( $custom_media_query )
	{
		$rwp_settings = array(
			'sizes' => array( $custom_media_query['smallestImage'] ),
			'media_queries' => array()
		);
		for ($i=0; $i < count($custom_media_query['breakpoints']); $i++) { 
			$breakpoint = $custom_media_query['breakpoints'][$i];
			$rwp_settings['media_queries'][$breakpoint['image_size']] = array(
				'property' => $breakpoint['property'],
				'value' => $breakpoint['value']
			);
			$rwp_settings['sizes'][] = $breakpoint['image_size'];
		}
		$this->rwp_settings = $rwp_settings;
	}

}