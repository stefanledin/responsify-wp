<?php

class Test_Image extends WP_UnitTestCase {
	protected $attachment;
	
	function setUp()
	{
		$this->attachment = create_attachment();	
	}

	/*function test_default()
	{
		$img = Picture::create( 'img', $this->attachment );
		$expected = '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 112w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089.jpg 279w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 279px) 474px, (min-width: 225px) 279px, (min-width: 112px) 225px, 112px">';
		
		$this->assertEquals($expected, $img);
	}*/

	function test_default_with_retina_image()
	{
		$retina_attachment = create_retina_attachment();
		#die(var_dump(wp_get_attachment_image_src( $retina_attachment )));
		$img = Picture::create( 'img', $retina_attachment, array(
			'retina' => true
		) );
		$expected = '<img srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 112w, http://example.org/wp-content/uploads/retina-480x640@2x.jpg 112w 2x, http://example.org/wp-content/uploads/retina-600x800.jpg 225w, http://example.org/wp-content/uploads/retina-600x800@2x.jpg 225w 2x, http://example.org/wp-content/uploads/retina.jpg 279w, http://example.org/wp-content/uploads/retina-1024x1365.jpg 474w, http://example.org/wp-content/uploads/retina-1024x1365@2x.jpg 474w 2x" sizes="(min-width: 279px) 474px, (min-width: 225px) 279px, (min-width: 112px) 225px, 112px">';
		
		$this->assertEquals($expected, $img);
	}

	function test_selected_sizes()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'sizes' => array('medium', 'large')
		) );
		$expected = '<img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 225px) 474px, 225px">';
		
		$this->assertEquals($expected, $img);
	}

	function test_custom_attributes()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'attributes' => array(
				'id' => 'custom-id'
			)
		) );
		$expected = '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 112w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089.jpg 279w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 279px) 474px, (min-width: 225px) 279px, (min-width: 112px) 225px, 112px" id="custom-id">';
		
		$this->assertEquals($expected, $img);
	}

	function test_custom_size_attribute()
	{
		$img = Picture::create( 'img', $this->attachment, array(
			'sizes' => array('medium', 'large'),
			'attributes' => array(
				'sizes' => '(min-width: 800px) 474px, 225px'
			)
		) );
		$expected = '<img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 800px) 474px, 225px">';
		
		$this->assertEquals($expected, $img);
	}
}