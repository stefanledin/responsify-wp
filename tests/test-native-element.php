<?php

class Test_Native_Picture_Element extends WP_UnitTestCase {

	public $attachment;

	function setUp()
	{
		update_option( 'rwp_picturefill', 'off' );
		$this->attachment = create_attachment();
	}

	function tearDown()
	{
		delete_option( 'rwp_picturefill' );
	}

	function test_default()
	{
		$element = Picture::create( 'element', $this->attachment );
		
		$expected = '<picture >';
			$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089.jpg" media="(min-width: 1024px)">';
			$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 600px)">';
			$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 480px)">';
			$expected .= '<img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" >';
		$expected .= '</picture>';
		
		$this->assertEquals($expected, $element);
	}

	function test_custom_attributes()
	{
		$element = Picture::create( 'element', $this->attachment, array(
			'sizes' => array('thumbnail', 'medium', 'large'),
			'attributes' => array(
				'picture' => array(
					'id' => 'custom-id',
					'class' => 'picture-element'
				),
				'source' => array(
					'data-foo' => 'bar'
		        ),
				'img' => array(
					'id' => 'responsive-image'
				)
			)
		) );
		
		$expected = '<picture id="custom-id" class="picture-element">';
			$expected .= '<source data-foo="bar" srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 600px)">';
			$expected .= '<source data-foo="bar" srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 480px)">';
			$expected .= '<img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" id="responsive-image">';
		$expected .= '</picture>';
		
		$this->assertEquals($expected, $element);		
	}

}

