<?php

class Test_Create_Attributes extends WP_UnitTestCase {

	protected $attachment;

	function setUp() {
		$this->attachment = create_attachment();
	}

	function test_returns_img_attributes() {
		$attributes = Picture::create( 'attributes', $this->attachment, array(
			'element' => 'img'
		) );
		$expected = array(
			'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w',
			'sizes' => '(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px'
		);
		$this->assertEquals($expected, $attributes);
	}

	function test_returns_picture_attributes() {
		$attributes = Picture::create( 'attributes', $this->attachment, array(
			'element' => 'picture'
		) );
		$expected = array(
			'source' => array(
				array(
					'srcset' => 'http://example.org/wp-content/uploads/IMG_2089.jpg',
					'media' => '(min-width: 1024px)'
				),
				array(
					'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg',
					'media' => '(min-width: 600px)'
				),
				array(
					'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-600x800.jpg',
					'media' => '(min-width: 480px)'
				),
			),
			'img' => array(
				'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-480x640.jpg'
			)
		);
		$this->assertEquals($expected, $attributes);
	}

	function test_returns_custom_picture_settings()
	{
		$attributes = Picture::create( 'attributes', $this->attachment, array(
			'element' => 'picture',
			'sizes' => array('thumbnail','medium')
		) );
		$expected = array(
			'source' => array(
				array(
					'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-600x800.jpg',
					'media' => '(min-width: 480px)'
				)
			),
			'img' => array(
				'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-480x640.jpg'
			)
		);
		$this->assertEquals($expected, $attributes);
	}

	function test_returns_custom_picture_media_query()
	{
		$attributes = Picture::create( 'attributes', $this->attachment, array(
			'element' => 'picture',
			'sizes' => array('thumbnail','medium'),
			'media_queries' => array(
				'medium' => 'min-width: 1024px'
			)
		) );
		$expected = array(
			'source' => array(
				array(
					'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-600x800.jpg',
					'media' => '(min-width: 1024px)'
				)
			),
			'img' => array(
				'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-480x640.jpg'
			)
		);
		$this->assertEquals($expected, $attributes);
	}

}