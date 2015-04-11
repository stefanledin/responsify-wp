<?php

class Test_Image extends WP_UnitTestCase {
	protected $attachment;
	
	function setUp()
	{
		$this->attachment = create_attachment();	
	}

	function test_default()
	{
		$img = Picture::create( 'img', $this->attachment );
		$expected = '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w" sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px">';
		
		$this->assertEquals($expected, $img);
	}

	function test_selected_sizes()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'sizes' => array('medium', 'large')
		) );
		$expected = '<img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w" sizes="(min-width: 600px) 1024px, 600px">';
		
		$this->assertEquals($expected, $img);
	}

	function test_custom_attributes()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'attributes' => array(
				'id' => 'custom-id'
			)
		) );
		$expected = '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w" sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px" id="custom-id">';
		
		$this->assertEquals($expected, $img);
	}

	function test_custom_size_attribute()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'sizes' => array('medium', 'large'),
			'attributes' => array(
				'sizes' => '(min-width: 800px) 2448px, 600px'
			)
		) );
		$expected = '<img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w" sizes="(min-width: 800px) 2448px, 600px">';
		
		$this->assertEquals($expected, $img);
	}
}