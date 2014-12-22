<?php

class Test_Native_Image extends WP_UnitTestCase {
	protected $attachment;
	
	function setUp()
	{
		$this->attachment = create_attachment();
		update_option( 'rwp_picturefill', 'off' );
	}

	function tearDown()
	{
		delete_option( 'rwp_picturefill' );
	}

	function test_default()
	{
		$img = Picture::create( 'img', $this->attachment );
		$expected = '<img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 112w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089.jpg 279w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 279px) 474px, (min-width: 225px) 279px, (min-width: 112px) 225px, 112px">';
		
		$this->assertEquals($expected, $img);
	}

	function test_custom_attributes()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'attributes' => array(
				'id' => 'custom-id'
			)
		) );
		$expected = '<img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 112w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089.jpg 279w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 279px) 474px, (min-width: 225px) 279px, (min-width: 112px) 225px, 112px" id="custom-id">';
		
		$this->assertEquals($expected, $img);
	}
}