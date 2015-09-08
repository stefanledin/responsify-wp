<?php

class Test_Picture_Retina extends WP_UnitTestCase {
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
		$element = Picture::create( 'element', $this->attachment, array(
			'retina' => true
		) );
		$expected = '<picture >';
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina.jpg" media="(min-width: 1024px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina-1024x1365.jpg, http://example.org/wp-content/uploads/retina-1536x2047.jpg 1.5x, http://example.org/wp-content/uploads/retina-2048x2730.jpg 2x" media="(min-width: 600px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina-600x800.jpg, http://example.org/wp-content/uploads/retina-900x1200.jpg 1.5x, http://example.org/wp-content/uploads/retina-1200x1600.jpg 2x" media="(min-width: 480px)">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/retina-480x640.jpg, http://example.org/wp-content/uploads/retina-720x960.jpg 1.5x, http://example.org/wp-content/uploads/retina-960x1280.jpg 2x" >';
		$expected .= '</picture>';
		
		$this->assertEquals($expected, $element);
	}

	function test_retina_false()
	{
		$element = Picture::create( 'element', $this->attachment, array(
			'retina' => false
		) );
		
		$expected = '<picture >';
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina.jpg" media="(min-width: 1024px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina-1024x1365.jpg" media="(min-width: 600px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina-600x800.jpg" media="(min-width: 480px)">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/retina-480x640.jpg" >';
		$expected .= '</picture>';
		
		$this->assertEquals($expected, $element);
	}

	function test_retina_true_and_selected_sizes()
	{
		$element = Picture::create( 'element', $this->attachment, array(
			'retina' => true,
			'sizes' => array('thumbnail','medium','large', 'full')
		) );
		$expected = '<picture >';
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina.jpg" media="(min-width: 1024px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina-1024x1365.jpg, http://example.org/wp-content/uploads/retina-1536x2047.jpg 1.5x, http://example.org/wp-content/uploads/retina-2048x2730.jpg 2x" media="(min-width: 600px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina-600x800.jpg, http://example.org/wp-content/uploads/retina-900x1200.jpg 1.5x, http://example.org/wp-content/uploads/retina-1200x1600.jpg 2x" media="(min-width: 480px)">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/retina-480x640.jpg, http://example.org/wp-content/uploads/retina-720x960.jpg 1.5x, http://example.org/wp-content/uploads/retina-960x1280.jpg 2x" >';
		$expected .= '</picture>';
		
		$this->assertEquals($expected, $element);
	}

	function test_selected_2x_sizes()
	{
		$element = Picture::create( 'element', $this->attachment, array(
			'retina' => '2x',
			'sizes' => array( 'thumbnail', 'medium', 'large' )
		) );
		$expected = '<picture >';
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina-1024x1365.jpg, http://example.org/wp-content/uploads/retina-2048x2730.jpg 2x" media="(min-width: 600px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina-600x800.jpg, http://example.org/wp-content/uploads/retina-1200x1600.jpg 2x" media="(min-width: 480px)">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/retina-480x640.jpg, http://example.org/wp-content/uploads/retina-960x1280.jpg 2x" >';
		$expected .= '</picture>';
		
		$this->assertEquals($expected, $element);
	}

	function test_array_with_density()
	{
		$element = Picture::create( 'element', $this->attachment, array(
			'retina' => array( '1.5x' )
		) );
		$expected = '<picture >';
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina.jpg" media="(min-width: 1024px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina-1024x1365.jpg, http://example.org/wp-content/uploads/retina-1536x2047.jpg 1.5x" media="(min-width: 600px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/retina-600x800.jpg, http://example.org/wp-content/uploads/retina-900x1200.jpg 1.5x" media="(min-width: 480px)">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/retina-480x640.jpg, http://example.org/wp-content/uploads/retina-720x960.jpg 1.5x" >';
		$expected .= '</picture>';
		
		$this->assertEquals($expected, $element);
	}
}