<?php

class Test_Style extends WP_UnitTestCase {
	protected $attachment;

	function setUp()
	{
		$this->attachment = create_attachment();
	}

	function test_default()
	{
		$style = Picture::create( 'style', $this->attachment, array(
			'selector' => '#hero'
		) );
		$expected = '<style>';
			$expected .= '#hero {';
				$expected .= 'background-image: url("http://example.org/wp-content/uploads/IMG_2089-480x640.jpg");';
			$expected .= '}';
			$expected .= '@media screen and (min-width: 112px) {';
				$expected .= '#hero{background-image: url("http://example.org/wp-content/uploads/IMG_2089-600x800.jpg");}';
			$expected .= '}';
			$expected .= '@media screen and (min-width: 225px) {';
				$expected .= '#hero{background-image: url("http://example.org/wp-content/uploads/IMG_2089.jpg");}';
			$expected .= '}';
			$expected .= '@media screen and (min-width: 279px) {';
				$expected .= '#hero{background-image: url("http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg");}';
			$expected .= '}';
		$expected .= '</style>';

		$this->assertEquals($expected, $style);
	}

	/*function test_custom_media_query()
	{
		
	}*/

}