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
		die(var_dump($style));
		$expected = '<style>';
			$expected .= '#hero {';
				$expected .= 'background-image url("http://example.org/wp-content/uploads/IMG_2089-480x640.jpg")';
			$expected .= '}';
			$expected .= '@media screen and (112px) {';
				$expected .= '#hero {';
					$expected .= 'background-image url("http://example.org/wp-content/uploads/IMG_2089-480x640.jpg")';
				$expected .= '}';
			$expected .= '}';
		$expected .= '</style>';

		$this->assertEquals($expected, $style);
	}

}