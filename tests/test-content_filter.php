<?php

class Test_Content_Filter extends WP_UnitTestCase {
	protected $attachment;
	protected $image_url;
	protected $image_data;
	protected $post;

	protected $upload_url;

	function setUp() {
		update_option( 'rwp_added_filters', array( 'the_content', 'post_thumbnail_html' ) );
		$this->attachment = create_attachment();
		$this->image_data = wp_get_attachment_metadata( $this->attachment );
		$upload_url = wp_upload_dir()['baseurl'];
		$image = '<img src="'.$upload_url.'/'.$this->image_data['file'].'">';
		$this->post = wp_insert_post( array(
			'post_name' => 'test',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$this->upload_url = wp_upload_dir()['baseurl'];
		$this->image_url = wp_upload_dir()['baseurl'] . '/' . $this->image_data['file'];
	}

	function test_default()
	{
		$post = get_post($this->post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w" sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"></p>';
		$this->assertEquals($expected, $post);
	}

	function test_debug_mode()
	{
		$attachment = create_attachment( 'debug' );
		$image_data = wp_get_attachment_metadata( $attachment );
		$upload_url = wp_upload_dir()['baseurl'];
		$image = '<img src="'.$upload_url.'/'.$image_data['file'].'">';
		$post = wp_insert_post( array(
			'post_name' => 'test',
			'post_content' => $image,
			'post_status' => 'publish'
		) );
		update_option( 'rwp_debug_mode', 'on' );
		$post = get_post($post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		update_option( 'rwp_debug_mode', 'off' );
		$debug_html_comment = array(
			"<!--",
				"### RWP Debug ###",
				"Attachment ID: $attachment",
				"Image sizes: thumbnail, medium, large, post-thumbnail, full",
				"Image width: 2448",
				"Image height: 3264",
				"Image sizes found: thumbnail, medium, large, full",
				"Images found: ",
				"- thumbnail: http://example.org/wp-content/uploads/debug-480x640.jpg, ",
				"- medium: http://example.org/wp-content/uploads/debug-600x800.jpg, ",
				"- large: http://example.org/wp-content/uploads/debug-1024x1365.jpg, ",
				"- full: http://example.org/wp-content/uploads/debug.jpg",
				"Largest size that should be used: http://example.org/wp-content/uploads/2014/10/debug.jpg",
				"Media queries: ",
				"- Use medium when min-width: 480px, ",
				"- Use large when min-width: 600px, ",
				"- Use full when min-width: 1024px",
			"-->"
		);
		$debug_html_comment = implode("\n", $debug_html_comment);
		$expected = '<p>'.$debug_html_comment.'<img srcset="http://example.org/wp-content/uploads/debug-480x640.jpg 480w, http://example.org/wp-content/uploads/debug-600x800.jpg 600w, http://example.org/wp-content/uploads/debug-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/debug.jpg 2448w" sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"></p>';
		$this->assertEquals($expected, $post);
	}

	function test_include_full_size_when_smaller_than_large()
	{
		$img = wp_insert_attachment( array(
			'guid' => 'http://example.org/wp-content/uploads/image.jpg',
			'post_mime_type' => 'image/jpeg',
			'post_title' => 'Image',
			'post_status' => 'inherit'
		), 'image.jpg' );
		wp_update_attachment_metadata( $img, array(
			'width' => 500,
			'height' => 500,
			'file' => '2015/02/image.jpg',
			'sizes' => array(
				'thumbnail' => array(
					'width' => 320,
					'height' => 320,
					'file' => '2015/02/image-320x320.jpg'
				)
			)
		) );
		$test_post = wp_insert_post( array(
			'post_name' => 'Test',
			'post_content' => '<img src="'.$this->upload_url.'/image.jpg">',
			'post_status' => 'publish'
		) );
		$test_post = get_post( $test_post );
		$test_post = trim(apply_filters( 'the_content', $test_post->post_content ));
		$expected = '<p><img srcset="http://example.org/wp-content/uploads/2015/02/image-320x320.jpg 320w, http://example.org/wp-content/uploads/image.jpg 500w" sizes="(min-width: 320px) 500px, 320px"></p>';
		$this->assertEquals($expected, $test_post);
	}

	function test_thumbnail_filter()
	{
		$image = '<img src="'.$this->image_url.'">';
		$thumbnail = trim(apply_filters( 'post_thumbnail_html', $image ));
		$expected = '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w" sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px">';
		$this->assertEquals($expected, $thumbnail);
	}

	function test_picture()
	{
		update_option( 'selected_element', 'picture' );
		$post = get_post($this->post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		
		$expected = '<p><picture ><!--[if IE 9]><video style="display: none;"><![endif]--><source  srcset="http://example.org/wp-content/uploads/IMG_2089.jpg" media="(min-width: 1024px)"><source  srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 600px)"><source  srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 480px)"><!--[if IE 9]></video><![endif]--><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" ></picture></p>';
		
		$this->assertEquals($expected, $post);
		delete_option( 'selected_element' );
	}

	function test_span()
	{
		update_option( 'selected_element', 'span' );
		$post = get_post($this->post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));

		$expected = '<p><span data-picture data-alt=""><span data-src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" ></span><span data-src="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" data-media="(min-width: 480px)" ></span><span data-src="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" data-media="(min-width: 600px)" ></span><span data-src="http://example.org/wp-content/uploads/IMG_2089.jpg" data-media="(min-width: 1024px)" ></span><noscript><img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" alt=""></noscript></span></p>';

		$this->assertEquals($expected, $post);
		delete_option( 'selected_element' );
	}

	function test_multiple_img()
	{
		$image = '<img id="my-id" class="my-classes" src="'.$this->image_url.'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image.$image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w" sizes="(min-width: 600px) 1024px, 600px" id="my-id" class="my-classes">';
		$expected .= '<img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w" sizes="(min-width: 600px) 1024px, 600px" id="my-id" class="my-classes"></p>';
		$post = get_posts( array(
			'p' => $post,
			'rwp_settings' => array(
				'sizes' => array('medium', 'large')
			)
		) );
		$post = trim(apply_filters( 'the_content', $post[0]->post_content ));
		
		$this->assertEquals($expected, $post);
	}

	function test_multiple_img_with_custom_setting()
	{
		$image = '<img id="my-id" class="my-classes" src="'.$this->image_url.'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image.$image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w" sizes="(min-width: 600px) 1024px, 600px" id="my-id" class="my-custom-class">';
		$expected .= '<img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w" sizes="(min-width: 600px) 1024px, 600px" id="my-id" class="my-custom-class"></p>';
		$post = get_posts( array(
			'p' => $post,
			'rwp_settings' => array(
				'sizes' => array('medium', 'large'),
				'attributes' => array(
					'class' => 'my-custom-class'
				)
			)
		) );
		$post = trim(apply_filters( 'the_content', $post[0]->post_content ));
		
		$this->assertEquals($expected, $post);
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

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w" sizes="(min-width: 600px) 1024px, 600px"></p>';
		
		$this->assertEquals($expected, $post);
	}

	function test_img_gets_old_img_attributes()
	{
		$image = '<img id="my-id" class="my-classes" src="'.$this->image_url.'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w" sizes="(min-width: 600px) 1024px, 600px" id="my-id" class="my-classes"></p>';
		$post = get_post($post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		
		$this->assertEquals($expected, $post);
	}

	function test_data_responsive_false_attribute()
	{
		$image = '<img class="rwp-not-responsive" src="'.$this->image_url.'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img class="rwp-not-responsive" src="http://example.org/wp-content/uploads/2014/10/IMG_2089.jpg"></p>';
		$post = get_post($post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		$this->assertEquals($expected, $post);
	}

	function test_img_still_gets_old_img_attributes_when_overriding_one_attribute()
	{
		$image = '<img id="my-id" class="my-classes" src="'.$this->image_url.'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w" sizes="(min-width: 600px) 1024px" id="my-id" class="my-classes"></p>';
		$post = get_posts( array(
			'p' => $post,
			'rwp_settings' => array(
				'attributes' => array(
					'sizes' => '(min-width: 600px) 1024px'
				)
			)
		) );
		$post = trim(apply_filters( 'the_content', $post[0]->post_content ));
		
		$this->assertEquals($expected, $post);
	}

	function test_img_with_custom_sizes_and_custom_sizes_attributes()
	{
		$image = '<img id="my-id" class="my-classes" src="'.$this->image_url.'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w" sizes="(min-width: 600px) 1024px" id="my-id" class="my-classes"></p>';
		$post = get_posts( array(
			'p' => $post,
			'rwp_settings' => array(
				'sizes' => array('medium', 'large'),
				'attributes' => array(
					'sizes' => '(min-width: 600px) 1024px'
				)
			)
		) );
		$post = trim(apply_filters( 'the_content', $post[0]->post_content ));
		
		$this->assertEquals($expected, $post);
	}

	function test_old_attribute_gets_overwritten_when_new_are_passed_as_user_setting()
	{
		$image = '<img id="my-id" class="my-classes" src="'.$this->image_url.'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w" sizes="(min-width: 600px) 1024px" id="my-custom-id" class="my-classes"></p>';
		$post = get_posts( array(
			'p' => $post,
			'rwp_settings' => array(
				'attributes' => array(
					'id' => 'my-custom-id',
					'sizes' => '(min-width: 600px) 1024px'
				)
			)
		) );
		$post = trim(apply_filters( 'the_content', $post[0]->post_content ));
		
		$this->assertEquals($expected, $post);
	}

	function test_picture_element_still_gets_old_attributes_when_one_is_overrided()
	{
		update_option( 'selected_element', 'picture' );
		$image = '<img id="my-id" class="my-classes" src="'.$this->image_url.'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$post = get_posts( array(
			'p' => $post,
			'rwp_settings' => array(
				'sizes' => array('thumbnail', 'medium', 'large'),
				'attributes' => array(
					'picture' => array(
						'id' => 'custom-id',
						'class' => 'picture-element'
					),
					'source' => array(
						'data-foo' => 'bar'
			        )
				)
			)
		) );
		$expected = '<p><picture id="custom-id" class="picture-element">';
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source data-foo="bar" srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 600px)">';
				$expected .= '<source data-foo="bar" srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 480px)">';			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" id="my-id" class="my-classes">';
		$expected .= '</picture></p>';

		$post = trim(apply_filters( 'the_content', $post[0]->post_content ));
		$this->assertEquals($expected, $post);
		delete_option( 'selected_element' );
	}

	function test_ignores_image_formats()
	{
		update_option( 'ignored_image_formats', array('png') );
		$png = create_png();
		$large_image = wp_get_attachment_metadata( $png );
		$image = '<img src="'.$this->upload_url.'/'.$large_image['file'].'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img src="http://example.org/wp-content/uploads/2014/12/logo.png"></p>';
		$post = get_post($post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		
		$this->assertEquals($expected, $post);
		delete_option( 'ignored_image_formats' );
	}

	function test_ignores_hotlinked_images()
	{
		$image = '<img src="http://google.com/logo.png">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img src="http://google.com/logo.png"></p>';
		$post = get_post($post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		
		$this->assertEquals($expected, $post);
	}

	/*function test_ignores_large_image_when_medium_is_inserted()
	{
		$image = '<img src="'.$this->upload_url.'/'.$this->image_data['sizes']['medium']['file'].'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );
		$post = get_post($post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w" sizes="(min-width: 480px) 600px, 480px"></p>';
		
		$this->assertEquals($expected, $post);
	}*/

	function test_ignores_image_formats_from_query_settings()
	{
		$png = create_png();
		$large_image = wp_get_attachment_metadata( $png );
		$image = '<img src="'.$this->upload_url.'/'.$large_image['file'].'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img src="http://example.org/wp-content/uploads/2014/12/logo.png"></p>';
		$post = get_posts( array(
			'p' => $post,
			'rwp_settings' => array(
				'ignored_image_formats' => array('png')
			)
		) );
		$post = trim(apply_filters( 'the_content', $post[0]->post_content ));
		
		$this->assertEquals($expected, $post);
	}

}