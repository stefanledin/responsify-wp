<?php
class Test_Media_Queries extends WP_UnitTestCase {
	protected $images;

	function setUp()
	{
		$this->images = array(
			array(
				'size' => 'thumbnail',
				'width' => 150,
				'height' => 150
			),
			array(
				'size' => 'medium',
				'width' => 300,
				'height' => 300
			),
			array(
				'size' => 'large',
				'width' => 1024,
				'height' => 1024
			)
		);
	}
	
	function test_default_media_queries()
	{
		$expected = $this->images;
		$expected[1]['media_query'] = array(
			'property' => 'min-width',
			'value' => '150px'
		);
		$expected[2]['media_query'] = array(
			'property' => 'min-width',
			'value' => '300px'
		);
		$media_queries = new Media_Queries( $this->images );
		$images = $media_queries->set();

		$this->assertEquals($expected, $images);
	}

	function test_custom_media_queries()
	{
		$settings = array(
			'medium' => 'min-width: 600px',
			'large' => 'min-width: 1280px'
		);
		$expected = $this->images;
		$expected[1]['media_query'] = 'min-width: 600px';
		$expected[2]['media_query'] = 'min-width: 1280px';

		$media_queries = new Media_Queries( $this->images, $settings );
		$images = $media_queries->set( $this->images );

		$this->assertEquals($expected, $images);
	}
}