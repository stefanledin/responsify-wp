<?php
class Test_Content_Filter_Retina extends WP_UnitTestCase {

	protected $attachment;
	protected $image_url;
	protected $image_data;
	protected $post;

	protected $upload_url;

	function setUp() {
		#update_option( 'rwp_added_filters', array( 'the_content', 'post_thumbnail_html' ) );
		update_option( 'rwp_retina', 'on' );

		add_image_size( 'thumbnail@1.5x' );
		add_image_size( 'thumbnail@2x' );
		add_image_size( 'medium@1.5x' );
		add_image_size( 'medium@2x' );
		add_image_size( 'large@1.5x' );
		add_image_size( 'large@2x' );
		
		$this->attachment = create_retina_attachment();
		$this->image_data = wp_get_attachment_metadata( $this->attachment );
		$this->upload_url = wp_upload_dir()['baseurl'];
		$this->image_url = wp_upload_dir()['baseurl'] . '/' . $this->image_data['file'];
		
		$image = '<img src="'.$this->image_url.'">';
		$this->post = wp_insert_post( array(
			'post_name' => 'test',
			'post_content' => $image,
			'post_status' => 'publish'
		) );
	}

	function tearDown() {
		delete_option( 'rwp_retina' );
	}

	function test_when_retina_is_disabled()
	{
		update_option( 'rwp_retina', 'off' );
		$post = get_post($this->post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		$expected = '<p><img srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 480w, http://example.org/wp-content/uploads/retina-600x800.jpg 600w, http://example.org/wp-content/uploads/retina-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/retina.jpg 2448w" sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"></p>';
		$this->assertEquals($expected, $post);
		delete_option( 'rwp_retina' );
	}

	function test_default()
	{
		$post = get_post($this->post);
		$post = trim(apply_filters( 'the_content', $post->post_content ));
		$expected = '<p><img ';
			$expected .= 'srcset="http://example.org/wp-content/uploads/retina-480x640.jpg 480w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-720x960.jpg 720w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-960x1280.jpg 960w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-600x800.jpg 600w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-900x1200.jpg 900w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1200x1600.jpg 1200w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina-1024x1365.jpg 1024w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-1536x2047.jpg 1536w, ';
				$expected .= 'http://example.org/wp-content/uploads/retina-2048x2730.jpg 2048w, ';
			
			$expected .= 'http://example.org/wp-content/uploads/retina.jpg 2448w" ';
			
			$expected .= 'sizes="(min-width: 1024px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px"';
		$expected .= '></p>';

		$this->assertEquals($expected, $post);
	}
}