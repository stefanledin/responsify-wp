<?php

class SampleTest extends WP_UnitTestCase {

	public $attachment;

	function setUp()
	{
		$this->attachment = create_attachment();
	}

	function test_default()
	{
		$element = Picture::create( 'element', $this->attachment );
		
		$expected = '<picture >';
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089.jpg" media="(min-width: 1024px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 600px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 480px)">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" >';
		$expected .= '</picture>';
		
		$this->assertEquals($expected, $element);
	}

	function test_selected_sizes()
	{
		$element = Picture::create( 'element', $this->attachment, array(
			'sizes' => array('thumbnail', 'medium', 'large')
		) );
		
		$expected = '<picture >';
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 600px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 480px)">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" >';
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
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source data-foo="bar" srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 600px)">';
				$expected .= '<source data-foo="bar" srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 480px)">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" id="responsive-image">';
		$expected .= '</picture>';
		
		$this->assertEquals($expected, $element);		
	}

	function test_custom_media_queries()
	{
		$element = Picture::create( 'element', $this->attachment, array(
			'sizes' => array('thumbnail', 'medium', 'large'),
			'media_queries' => array(
				'medium' => 'min-width: 500px',
				'large' => 'min-width: 1024px'
			)
		) );
		
		$expected = '<picture >';
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 1024px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 500px)">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" >';
		$expected .= '</picture>';
		
		$this->assertEquals($expected, $element);
	}

}

