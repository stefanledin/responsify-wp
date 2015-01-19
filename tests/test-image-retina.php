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
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 112w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-720x960.jpg 112w 1.5x, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-960x1280.jpg 112w 2x, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 225w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-900x1200.jpg 225w 1.5x, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1200x1600.jpg 225w 2x, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina.jpg 279w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 474w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1536x2047.jpg 474w 1.5x, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-2048x2730.jpg 474w 2x" ';
			
			$expected .= 'sizes="(min-width: 279px) 474px, (min-width: 225px) 279px, (min-width: 112px) 225px, 112px"';
		$expected .= '>';

		$this->assertEquals($expected, $img);
	}

	/*function test_retina_false()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => false
		) );
		$expected = '<img ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 112w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 225w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina.jpg 279w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 474w" ';
			$expected .= 'sizes="(min-width: 279px) 474px, (min-width: 225px) 279px, (min-width: 112px) 225px, 112px"';
		$expected .= '>';

		$this->assertEquals($expected, $img);	
	}*/

	function test_selected_2x_sizes()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => '2x',
			'sizes' => array( 'thumbnail', 'medium', 'large' )
		) );
		$expected = '<img ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 112w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-960x1280.jpg 112w 2x, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 225w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1200x1600.jpg 225w 2x, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 474w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-2048x2730.jpg 474w 2x" ';
			$expected .= 'sizes="(min-width: 225px) 474px, (min-width: 112px) 225px, 112px"';
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
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 112w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-720x960.jpg 112w 1.5x, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 225w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-900x1200.jpg 225w 1.5x, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 474w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-1536x2047.jpg 474w 1.5x" ';
			
			$expected .= 'sizes="(min-width: 225px) 474px, (min-width: 112px) 225px, 112px"';
		$expected .= '>';

		$this->assertEquals($expected, $img);
	}

	/*function test_array_of_densities()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'retina' => array( '1.5x' )
		) );
		$expected = '<img ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 112w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-720x960.jpg 112w 1.5x, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 225w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-900x1200.jpg 225w 1.5x, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 474w, ';
			$expected .= 'http://example.org/wp-content/uploads/retina-1536x2047.jpg 474w 1.5x" ';
			
			$expected .= 'sizes="(min-width: 225px) 474px, (min-width: 112px) 225px, 112px"';
		$expected .= '>';

		$this->assertEquals($expected, $img);
	}*/

}