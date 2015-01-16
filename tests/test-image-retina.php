<?php

class Test_Image_Retina extends WP_UnitTestCase {
	protected $attachment;

	function setUp()
	{
		$this->attachment = create_retina_attachment();
		add_image_size( 'thumbnail@2x' );
		add_image_size( 'medium@2x' );
		add_image_size( 'large@2x' );
	}

	function test_default()
	{
		$retina_attachment = create_retina_attachment();
		$img = Picture::create( 'img', $retina_attachment, array(
			'retina' => true
		) );
		$expected = '<img ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 112w, "';
				$expected .= 'http://example.org/wp-content/uploads/retina-960x1280.jpg 112w 2x, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 225w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1200x1600.jpg 225w 2x, ';
				$expected .= 'http://example.org/wp-content/uploads/retina.jpg 279w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 474w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-2048x2730.jpg 474w 2x" ';
			$expected .= 'sizes="(min-width: 279px) 474px, (min-width: 225px) 279px, (min-width: 112px) 225px, 112px"';
		$expected .= '>';

		$this->assertEquals($expected, $img);
	}
}