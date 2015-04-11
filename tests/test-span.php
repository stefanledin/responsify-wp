<?php

class Test_Span extends WP_UnitTestCase {

	public $attachment;

	function setUp()
	{
		$this->attachment = create_attachment();
	}

	function test_default()
	{
		$element = Picture::create( 'span', $this->attachment );
		
		$expected = '<span data-picture data-alt="">';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" ></span>';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" data-media="(min-width: 480px)" ></span>';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" data-media="(min-width: 600px)" ></span>';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089.jpg" data-media="(min-width: 1024px)" ></span>';
			$expected .= '<noscript>';
				$expected .= '<img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" alt="">';
			$expected .= '</noscript>';
		$expected .= '</span>';
		
		$this->assertEquals($expected, $element);
	}

	function test_selected_sizes()
	{
		$element = Picture::create( 'span', $this->attachment, array(
			'sizes' => array('thumbnail', 'medium', 'large')
		) );
		
		$expected = '<span data-picture data-alt="">';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" ></span>';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" data-media="(min-width: 480px)" ></span>';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" data-media="(min-width: 600px)" ></span>';
			$expected .= '<noscript>';
				$expected .= '<img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" alt="">';
			$expected .= '</noscript>';
		$expected .= '</span>';
		
		$this->assertEquals($expected, $element);
	}

	function test_custom_attributes()
	{
		$element = Picture::create( 'span', $this->attachment, array(
			'sizes' => array('thumbnail', 'medium', 'large'),
			'attributes' => array(
				'picture_span' => array(
					'id' => 'custom-id',
					'class' => 'picture-element'
				),
				'src_span' => array(
					'data-foo' => 'bar'
		        )
			)
		) );
		
		$expected = '<span data-picture data-alt="" id="custom-id" class="picture-element">';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" data-foo="bar"></span>';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" data-media="(min-width: 480px)" data-foo="bar"></span>';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" data-media="(min-width: 600px)" data-foo="bar"></span>';
			$expected .= '<noscript>';
				$expected .= '<img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" alt="">';
			$expected .= '</noscript>';
		$expected .= '</span>';
		
		$this->assertEquals($expected, $element);		
	}

	function test_custom_media_queries()
	{
		$element = Picture::create( 'span', $this->attachment, array(
			'sizes' => array('thumbnail', 'medium', 'large'),
			'media_queries' => array(
				'medium' => 'min-width: 500px',
				'large' => 'min-width: 1024px'
			)
		) );
		
		$expected = '<span data-picture data-alt="">';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" ></span>';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" data-media="(min-width: 500px)" ></span>';
			$expected .= '<span data-src="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" data-media="(min-width: 1024px)" ></span>';
			$expected .= '<noscript>';
				$expected .= '<img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" alt="">';
			$expected .= '</noscript>';
		$expected .= '</span>';
		
		$this->assertEquals($expected, $element);
	}

}

