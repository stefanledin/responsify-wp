<?php

class Custom_Media_Query_Rules {

	/**
	 * Page ID
	 */
	public function page_id_equals( $post_object, $value )
	{
		return ( $post_object->ID == (int) $value );
	}
	public function page_id_not_equals( $post_object, $value )
	{
		return ( $post_object->ID != (int) $value );
	}

	/**
	 * Page slug
	 */
	public function page_slug_equals( $post_object, $value )
	{
		return ( $post_object->post_name == $value );
	}
	public function page_slug_not_equals( $post_object, $value )
	{
		return ( $post_object->post_name != $value );
	}
	
	/**
	 * Page templates
	 */
	public function page_template_equals( $post_object, $value )
	{
		return is_integer( strpos(get_page_template(), $value) );
	}
	public function page_template_not_equals( $post_object, $value )
	{
		return ! is_integer( strpos(get_page_template(), $value) );
	}

	/**
	 * Image class
	 */
	public function image_class_equals( $attributes, $value )
	{
		return is_integer( strpos($attributes['img']['class'], $value) );
	}
	public function image_class_not_equals( $attributes, $value )
	{
		return ! is_integer( strpos($attributes['img']['class'], $value) );
	}

	/**
	 * Image size
	 */
	public function image_size_equals( $attributes, $value )
	{
		return is_integer( strpos($attributes['img']['class'], 'size-' . $value) );
	}
	public function image_size_not_equals( $attributes, $value )
	{
		return ! is_integer( strpos($attributes['img']['class'], 'size-' . $value) );
	}


}