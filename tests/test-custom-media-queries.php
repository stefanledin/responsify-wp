<?php

class Test_Custom_Media_Queries extends WP_UnitTestCase {
	protected $attachment;
	protected $image_url;
	protected $image_data;
	protected $page;

	protected $upload_url;

	function setUp() {
		update_option( 'rwp_added_filters', array( 'the_content', 'post_thumbnail_html' ) );
		$this->attachment = create_attachment();
		$this->image_data = wp_get_attachment_metadata( $this->attachment );
		$upload_url = wp_upload_dir()['baseurl'];
		$image = '<img src="'.$upload_url.'/'.$this->image_data['file'].'">';
		$this->page = wp_insert_post( array(
			'post_name' => 'test',
			'post_type' => 'page',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$this->upload_url = wp_upload_dir()['baseurl'];
		$this->image_url = wp_upload_dir()['baseurl'] . '/' . $this->image_data['file'];

		$this->mock_image_dimentions();
	}

	function mock_image_dimentions()
	{
		update_option('thumbnail_size_w', 480);
		update_option('medium_size_w', 600);
		update_option('large_size_w', 1024);
		global $_wp_additional_image_sizes;
		$_wp_additional_image_sizes['full']['width'] = 2448;
		$_wp_additional_image_sizes['full']['height'] = 2448;
	}

	/*function test_default()
	{
		$page = get_post($this->page);
		$page = trim(apply_filters( 'the_content', $page->post_content ));
		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w" sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"></p>';
		$this->assertEquals($expected, $page);
	}*/

	function test_custom_settings_when_slug_is_test()
	{
		$custom_media_queries = array(
			'cid' => array(
				'rule' => array(
	                'default' => false,
	                'when' => array(
                    	'key' => 'page-slug',
                    	'compare' => '==',
                    	'value' => 'test'
	                )
				),
				'smallestImage' => 'thumbnail',
				'breakpoints' => array(
					array( 'image_size' => 'medium', 'property' => 'min-width', 'value' => '500px' ),
					array( 'image_size' => 'large', 'property' => 'min-width', 'value' => '900px' ),
					array( 'image_size' => 'full', 'property' => 'min-width', 'value' => '1440px' )
				)
            )
      	);
		update_option( 'rwp_custom_media_queries', $custom_media_queries );
		$page = get_post($this->page);
		$page = trim(apply_filters( 'the_content', $page->post_content ));
		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w" sizes="(min-width: 1440px) 2448px, (min-width: 900px) 1024px, (min-width: 500px) 600px, 480px"></p>';
		$this->assertEquals($expected, $page);	
	}

}