<?php

class Test_Image_Retina extends WP_UnitTestCase {
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
	}

	function test_default()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => true
		) );
		$expected = '<img ';
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

	function test_retina_false()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => false
		) );
		$expected = '<img ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 480w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 600w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 1024w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina.jpg 2448w" ';
			$expected .= 'sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"';
		$expected .= '>';

		$this->assertEquals($expected, $img);	
	}

	function test_selected_2x_sizes()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => '2x',
			'sizes' => array( 'thumbnail', 'medium', 'large' )
		) );
		$expected = '<img ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 480w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-960x1280.jpg 960w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 600w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1200x1600.jpg 1200w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 1024w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-2048x2730.jpg 2048w" ';
			$expected .= 'sizes="(min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"';
		$expected .= '>';

		$this->assertEquals($expected, $img);
	}

	function test_selected_1_5x_sizes()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => '1.5x',
			'sizes' => array( 'thumbnail', 'medium', 'large' )
		) );
		$expected = '<img ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 480w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-720x960.jpg 720w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 600w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-900x1200.jpg 900w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 1024w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-1536x2047.jpg 1536w" ';
			
			$expected .= 'sizes="(min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"';
		$expected .= '>';

		$this->assertEquals($expected, $img);
	}

	function test_array_with_density()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => array( '1.5x' )
		) );
		$expected = '<img ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 480w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-720x960.jpg 720w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 600w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-900x1200.jpg 900w, ';

			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 1024w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-1536x2047.jpg 1536w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina.jpg 2448w" ';
			
			$expected .= 'sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"';
		$expected .= '>';

		$this->assertEquals($expected, $img);
	}

	function test_array_of_multiple_densities()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => array( '1.5x', '2x' )
		) );
		$expected = '<img ';
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

	function test_array_of_multiple_densities_and_selected_sizes()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => array( '1.5x', '2x' ),
			'sizes' => array( 'thumbnail', 'medium', 'large' )
		) );
		$expected = '<img ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 480w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-720x960.jpg 720w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-960x1280.jpg 960w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 600w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-900x1200.jpg 900w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1200x1600.jpg 1200w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 1024w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1536x2047.jpg 1536w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-2048x2730.jpg 2048w" ';
			
			$expected .= 'sizes="(min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"';
		$expected .= '>';

		$this->assertEquals($expected, $img);
	}

}