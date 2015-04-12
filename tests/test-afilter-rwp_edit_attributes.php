<?php
class Test_Rwp_Edit_Attributes extends WP_UnitTestCase {

	protected $attachment;
	protected $image;
	protected $upload_url;
	
	function setUp() {
		$this->attachment = create_attachment();
		$this->image_data = wp_get_attachment_metadata( $this->attachment );
		$this->upload_url = wp_upload_dir()['baseurl'];
		$this->image = '<img src="'.$this->upload_url.'/'.$this->image_data['file'].'" class="alignnone">';

		add_filter( 'rwp_edit_attributes', array( $this, 'edit_attributes' ) );
	}

	function tearDown() {
		remove_filter( 'rwp_edit_attributes', array( $this, 'edit_attributes' ) );
		delete_option( 'rwp_picturefill' );
		delete_option( 'selected_element' );
	}

	function edit_attributes( $attributes ) {
		if ( isset($attributes['picture']) ) {
			$attributes['img']['class'] = 'alignleft';
		} else {
			$attributes['class'] = 'alignleft';
			$attributes['sizes'] = '(min-width: 1337px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px';
		}
		return $attributes;
	}

	function test_replaces_classname_and_sizes_attributes_on_img() {
		$expected = '<p><img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w" sizes="(min-width: 1337px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px" class="alignleft"></p>';
		$output = trim(apply_filters( 'the_content', $this->image ));
		$this->assertEquals($expected, $output);
	}
	function test_replaces_classname_and_sizes_attributes_on_native_img() {
		update_option( 'rwp_picturefill', 'off' );
		$expected = '<p><img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg 480w, http://example.org/wp-content/uploads/IMG_2089-600x800.jpg 600w, http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg 1024w, http://example.org/wp-content/uploads/IMG_2089.jpg 2448w" sizes="(min-width: 1337px) 2448px, (min-width: 600px) 1024px, (min-width: 480px) 600px, 480px" class="alignleft"></p>';
		$output = trim(apply_filters( 'the_content', $this->image ));
		$this->assertEquals($expected, $output);
		delete_option( 'rwp_picturefill' );
	}

	function test_replaces_classname_on_picture() {
		update_option( 'selected_element', 'picture' );
		$expected = '<p><picture >';
			$expected .= '<!--[if IE 9]><video style="display: none;"><![endif]-->';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089.jpg" media="(min-width: 1024px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 600px)">';
				$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 480px)">';
			$expected .= '<!--[if IE 9]></video><![endif]-->';
			$expected .= '<img srcset="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" class="alignleft">';
		$expected .= '</picture></p>';
		$output = trim(apply_filters( 'the_content', $this->image ));
		$this->assertEquals($expected, $output);
		delete_option( 'selected_element' );
	}
	function test_replaces_classname_on_native_picture() {
		update_option( 'rwp_picturefill', 'off' );
		update_option( 'selected_element', 'picture' );
		$expected = '<p><picture >';
			$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089.jpg" media="(min-width: 1024px)">';
			$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-1024x1365.jpg" media="(min-width: 600px)">';
			$expected .= '<source  srcset="http://example.org/wp-content/uploads/IMG_2089-600x800.jpg" media="(min-width: 480px)">';
			$expected .= '<img src="http://example.org/wp-content/uploads/IMG_2089-480x640.jpg" class="alignleft">';
		$expected .= '</picture></p>';
		$output = trim(apply_filters( 'the_content', $this->image ));
		$this->assertEquals($expected, $output);
		delete_option( 'rwp_picturefill' );
		delete_option( 'selected_element' );
	}


}