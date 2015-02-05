<?php

class Test_Native_Image_Retina extends WP_UnitTestCase {
	protected $attachment;
	
	function setUp()
	{
		$this->attachment = create_retina_attachment();
		add_image_size( 'thumbnail@1.5x' );
		add_image_size( 'thumbnail@2x' );
		add_image_size( 'medium@1.5x' );
		add_image_size( 'medium@2x' );
		add_image_size( 'large@1.5x' );
		add_image_size( 'large@2x' );
		update_option( 'rwp_picturefill', 'off' );
	}

	function tearDown()
	{
		delete_option( 'rwp_picturefill' );
	}

	function test_default()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => true
		) );

		$expected = '<img ';
			$expected .= 'src="http://example.org/wp-content/uploads/retina-480x640.jpg" ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 480w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-720x960.jpg 720w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-960x1280.jpg 960w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 600w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-900x1200.jpg 900w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1200x1600.jpg 1200w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 1024w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1536x2047.jpg 1536w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-2048x2730.jpg 2048w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina.jpg 2448w" ';
			
			$expected .= 'sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"';
		$expected .= '>';
		
		$this->assertEquals($expected, $img);
	}

}