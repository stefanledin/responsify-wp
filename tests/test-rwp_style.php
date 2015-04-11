<?php

class Test_Rwp_Style extends WP_UnitTestCase {
	protected $attachment;

	function setUp()
	{
		$this->attachment = create_attachment();
	}

	function test_default()
	{
		$style = rwp_style( $this->attachment, array(
			'selector' => '#hero'
		) );
		$expected = '<style>';
			$expected .= '#hero {';
				$expected .= 'background-image: url("http://example.org/wp-content/uploads/IMG_2089-480x640.jpg");';
			$expected .= '}';
			$expected .= '@media screen and (min-width: 480px) {';
				$expected .= '#hero{background-image: url("http://example.org/wp-content/uploads/IMG_2089-600x800.jpg");}';
			$expected .= '}';
			$expected .= '@media screen and (min-width: 600px) {';
				$expected .= '#hero{background-image: url("http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg");}';
			$expected .= '}';
			$expected .= '@media screen and (min-width: 1024px) {';
				$expected .= '#hero{background-image: url("http://example.org/wp-content/uploads/IMG_2089.jpg");}';
			$expected .= '}';
		$expected .= '</style>';

		$this->assertEquals($expected, $style);
	}

	function test_selected_sizes()
	{
		$style = rwp_style( $this->attachment, array(
			'selector' => '#hero',
			'sizes' => array('medium', 'large')
		) );
		$expected = '<style>';
			$expected .= '#hero {';
				$expected .= 'background-image: url("http://example.org/wp-content/uploads/IMG_2089-600x800.jpg");';
			$expected .= '}';
			$expected .= '@media screen and (min-width: 600px) {';
				$expected .= '#hero{background-image: url("http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg");}';
			$expected .= '}';
		$expected .= '</style>';

		$this->assertEquals($expected, $style);
	}

	function test_custom_media_queries()
	{
		$style = rwp_style( $this->attachment, array(
			'selector' => '#hero',
			'sizes' => array('medium', 'large'),
			'media_queries' => array(
				'large' => 'min-width: 800px'
			)
		) );
		$expected = '<style>';
			$expected .= '#hero {';
				$expected .= 'background-image: url("http://example.org/wp-content/uploads/IMG_2089-600x800.jpg");';
			$expected .= '}';
			$expected .= '@media screen and (min-width: 800px) {';
				$expected .= '#hero{background-image: url("http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg");}';
			$expected .= '}';
		$expected .= '</style>';

		$this->assertEquals($expected, $style);
	}

}