<?php
class Custom_Media_Queries {

	protected $rules;
	protected $custom_media_queries;
	protected $rwp_settings = array();

	public function __construct( $custom_media_queries )
	{
		$this->rules = new Custom_Media_Query_Rules;
		$this->custom_media_queries = $custom_media_queries;
	}

	/**
	 * Checks if the media query should be applied in this context.
	 * @param  string $type 
	 * @param  array|object $data 
	 * @return boolean
	 */
	public function should_be_applied_when( $type, $data )
	{
		if ( ! is_array($this->custom_media_queries) ) return false;
		$this->rwp_settings = array();
		$method_to_call = 'check_rule_for_' . $type;
		call_user_func( array($this, $method_to_call), $data );
		return ( count($this->rwp_settings) > 0 );
	}

	/**
	 * Returns the latest array of settings that has been generated
	 * @return array
	 */
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
				continue;
			}
			$key = $media_query['rule']['when']['key'];
			if ( $key == 'image' ) return;
			$value = $media_query['rule']['when']['value'];
			$compare = $media_query['rule']['when']['compare'];

			$rule_to_check = $key . '_';
			$rule_to_check .= $compare;
			if ( call_user_func( array($this->rules, $rule_to_check), $post_object, $value ) ) {
				$this->apply_custom_media_queries( $media_query );
			}
		}
	}

	protected function check_rule_for_image( $attributes )
	{
		foreach ( $this->custom_media_queries as $media_query ) {
			// Ignore default settings. It has already been set in check_rule_for_post()
			if ( $media_query['rule']['default'] == 'true' ) continue;

			$key = $media_query['rule']['when']['key'];
			if ( $key != 'image' ) return;
			$value = $media_query['rule']['when']['value'];
			$compare = $media_query['rule']['when']['compare'];

			$rule_to_check = $key . '_';
			$rule_to_check .= $media_query['rule']['when']['image'] . '_';
			$rule_to_check .= $compare;
			if ( call_user_func( array($this->rules, $rule_to_check), $attributes, $value ) ) {
				$this->apply_custom_media_queries( $media_query );
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
		foreach ($custom_media_query['breakpoints'] as $breakpoint) {
			$rwp_settings['media_queries'][$breakpoint['image_size']] = array(
				'property' => $breakpoint['property'],
				'value' => $breakpoint['value']
			);
			$rwp_settings['sizes'][] = $breakpoint['image_size'];
		}
		$this->rwp_settings = $rwp_settings;
	}

}