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
			'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 112w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089.jpg 279w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w',
			'sizes' => '(min-width: 279px) 474px, (min-width: 225px) 279px, (min-width: 112px) 225px, 112px'
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
					'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg',
					'media' => '(min-width: 279px)'
				),
				array(
					'srcset' => 'http://example.org/wp-content/uploads/IMG_2089.jpg',
					'media' => '(min-width: 225px)'
				),
				array(
					'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-600x800.jpg',
					'media' => '(min-width: 112px)'
				),
				array(
					'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-480x640.jpg'
				),
			),
			'img' => array(
				'srcset' => 'http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg'
			)
		);
		$this->assertEquals($expected, $attributes);
	}

}