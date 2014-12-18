<?php

class Test_Content_Filter extends WP_UnitTestCase {
	protected $attachment;
	protected $post;

	function setUp() {
		$this->attachment = create_attachment();
		$large_image = wp_get_attachment_metadata( $this->attachment );
		$upload_url = wp_upload_dir()['baseurl'];
		$image = '<img src="'.$upload_url.'/'.$large_image['file'].'">';
		$this->post = wp_insert_post( array(
			'post_name' => 'test',
			'post_content' => $image,
			'post_status' => 'publish'
		) );
	}

	function test_default()
	{
		$post = get_post($this->post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 112w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089.jpg 279w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 279px) 474px, (min-width: 225px) 279px, (min-width: 112px) 225px, 112px"></p>';
		$this->assertEquals($expected, $post);
	}

	function test_picture()
	{
		update_option( 'selected_element', 'picture' );
		$post = get_post($this->post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		
		$expected = '<p><picture ><!--[if IE 9]><video style="display: none;"><![endif]--><source  srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 279px)"><source  srcset="http://example.org/wp-content/uploads/IMG_2089.jpg" media="(min-width: 225px)"><source  srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 112px)"><source  srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg"><!--[if IE 9]></video><![endif]--><img srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" ></picture></p>';
		
		$this->assertEquals($expected, $post);
		delete_option( 'selected_element' );
	}

	function test_img_with_settings()
	{
		$post = get_posts( array(
			'p' => $this->post,
			'rwp_settings' => array(
				'sizes' => array('medium', 'large')
			)
		) );
		$post = trim(apply_filters( 'the_content', $post[0]->post_content ));

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 225px) 474px, 225px"></p>';
		
		$this->assertEquals($expected, $post);
	}

	function test_ignores_image_formats()
	{
		update_option( 'ignored_image_formats', array('png') );
		$png = create_png();
		$large_image = wp_get_attachment_metadata( $png );
		$upload_url = wp_upload_dir()['baseurl'];
		$image = '<img src="'.$upload_url.'/'.$large_image['file'].'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img src="http://example.org/wp-content/uploads/2014/12/logo.png"></p>';
		$post = get_post($post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		
		$this->assertEquals($expected, $post);
	}
}