<?php

class Test_Content_Filter extends WP_UnitTestCase {
	protected $attachment;
	protected $image_url;
	protected $image_data;
	protected $post;

	protected $upload_url;

	function setUp() {
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

	function test_span()
	{
		update_option( 'selected_element', 'span' );
		$post = get_post($this->post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));

		$expected = '<p><span data-picture data-alt=""><span data-src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" ></span><span data-src="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" data-media="(min-width: 112px)" ></span><span data-src="http://example.org/wp-content/uploads/IMG_2089.jpg" data-media="(min-width: 225px)" ></span><span data-src="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" data-media="(min-width: 279px)" ></span><noscript><img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" alt=""></noscript></span></p>';

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

	function test_img_gets_old_img_attributes()
	{
		$image = '<img id="my-id" class="my-classes" src="'.$this->image_url.'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 225px) 474px, 225px" id="my-id" class="my-classes"></p>';
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

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 112w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089.jpg 279w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 225px) 474px" id="my-id" class="my-classes"></p>';
		$post = get_posts( array(
			'p' => $post,
			'rwp_settings' => array(
				'attributes' => array(
					'sizes' => '(min-width: 225px) 474px'
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

		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 112w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089.jpg 279w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 225px) 474px" id="my-custom-id" class="my-classes"></p>';
		$post = get_posts( array(
			'p' => $post,
			'rwp_settings' => array(
				'attributes' => array(
					'id' => 'my-custom-id',
					'sizes' => '(min-width: 225px) 474px'
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
				$expected .= '<source data-foo="bar" srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 225px)">';
				$expected .= '<source data-foo="bar" srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 112px)">';
			$expected .= '<source data-foo="bar" srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" id="my-id" class="my-classes">';
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

	function test_ignores_large_image_when_medium_is_inserted()
	{
		$image = '<img src="'.$this->upload_url.'/2014/10/'.$this->image_data['sizes']['medium']['file'].'">';
		$post = wp_insert_post( array(
			'post_name' => 'png',
			'post_content' => $image,
			'post_status' => 'publish'
		) );
		$post = get_post($post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 112w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 225w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 474w" sizes="(min-width: 225px) 474px, (min-width: 112px) 225px, 112px"></p>';
		
		$this->assertEquals($expected, $post);
	}

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