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

	function test_when_rule_is_default()
	{
		$custom_media_queries = array(
			'cid' => array(
				'rule' => array(
	                'default' => 'true',
	                'when' => array(
                    	'key' => 'page_id',
                    	'compare' => 'equals',
                    	'value' => '1'
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
      	$wp_post_object = (object) array(
      		'ID' => 13,
      		'post_name' => 'test',
      		'post_title' => 'Test'
  		);
		$custom_media_queries = new Custom_Media_Queries( $custom_media_queries );
		
		$this->assertTrue( $custom_media_queries->should_be_applied_when('post', $wp_post_object) );
	}

	function test_default_is_ignored()
	{
		$custom_media_queries = array(
			'cid' => array(
				'rule' => array(
	                'default' => 'true',
	                'when' => array(
                    	'key' => 'page_id'
	                )
				),
				'smallestImage' => 'thumbnail',
				'breakpoints' => array(
					array( 'image_size' => 'medium', 'property' => 'min-width', 'value' => '500px' ),
					array( 'image_size' => 'large', 'property' => 'min-width', 'value' => '900px' ),
					array( 'image_size' => 'full', 'property' => 'min-width', 'value' => '1440px' )
				)
            ),
            'cid2' => array(
            	'rule' => array(
            		'default' => 'false',
            		'when' => array(
            			'key' => 'page_slug',
            			'value' => 'test',
            			'compare' => 'equals'
        			),
        		),
    			'smallestImage' => 'medium',
    			'breakpoints' => array(
    				array( 'image_size' => 'large', 'property' => 'min-width', 'value' => '1024px' ),
					array( 'image_size' => 'full', 'property' => 'min-width', 'value' => '1337px' )
				)
        	)
      	);
      	$wp_post_object = (object) array(
      		'ID' => 13,
      		'post_name' => 'test',
      		'post_title' => 'Test'
  		);
  		$expected = array(
			'sizes' => array('medium', 'large', 'full'),
			'media_queries' => array(
				'large' => array( 'property' => 'min-width', 'value' => '1024px' ),
				'full' => array( 'property' => 'min-width', 'value' => '1337px' )
			)
		);

		$custom_media_queries = new Custom_Media_Queries( $custom_media_queries );
		
		$this->assertTrue( $custom_media_queries->should_be_applied_when('post', $wp_post_object) );
		$this->assertEquals( $expected, $custom_media_queries->get_settings() );
	}

	function test_when_media_queries_should_not_be_applied()
	{
		$custom_media_queries = array(
			'cid' => array(
				'rule' => array(
	                'default' => 'false',
	                'when' => array(
                    	'key' => 'page_id',
                    	'compare' => 'equals',
                    	'value' => '1'
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
      	$wp_post_object = (object) array(
      		'ID' => 13,
      		'post_name' => 'test',
      		'post_title' => 'Test'
  		);
		$custom_media_queries = new Custom_Media_Queries( $custom_media_queries );
		
		$this->assertFalse( $custom_media_queries->should_be_applied_when('post', $wp_post_object) );
	}

	function test_when_page_slug_is_test()
	{
		$custom_media_queries = array(
			'cid' => array(
				'rule' => array(
	                'default' => 'false',
	                'when' => array(
                    	'key' => 'page_slug',
                    	'compare' => 'equals',
                    	'value' => 'test'
	                )
				),
				'smallestImage' => 'thumbnail',
				'breakpoints' => array(
					array( 'image_size' => 'medium', 'property' => 'min-width', 'value' => '500px' ),
					array( 'image_size' => 'large', 'property' => 'min-width', 'value' => '900px' ),
					array( 'image_size' => 'full', 'property' => 'min-width', 'value' => '1440px' )
				)
            ),
            'cid2' => array(
            	'rule' => array(
	                'default' => 'false',
	                'when' => array(
                    	'key' => 'page_id',
                    	'compare' => 'equals',
                    	'value' => '1'
	                )
				)
        	)
      	);
      	$wp_post_object = (object) array(
      		'ID' => 13,
      		'post_name' => 'test',
      		'post_title' => 'Test'
  		);
		$custom_media_queries = new Custom_Media_Queries( $custom_media_queries );
		
		$expected = array(
			'sizes' => array('thumbnail', 'medium', 'large', 'full'),
			'media_queries' => array(
				'medium' => array( 'property' => 'min-width', 'value' => '500px' ),
				'large' => array( 'property' => 'min-width', 'value' => '900px' ),
				'full' => array( 'property' => 'min-width', 'value' => '1440px' )
			)
		);

		$this->assertTrue( $custom_media_queries->should_be_applied_when('post', $wp_post_object) );
		$this->assertEquals($expected, $custom_media_queries->get_settings());
	}

	function test_when_image_is_medium_size() 
	{
		$custom_media_queries = array(
			'cid' => array(
				'rule' => array(
	                'default' => 'false',
	                'when' => array(
                    	'key' => 'image',
                    	'image' => 'size',
                    	'compare' => 'equals',
                    	'value' => 'medium'
	                )
				),
				'smallestImage' => 'thumbnail',
				'breakpoints' => array(
					array( 'image_size' => 'medium', 'property' => 'min-width', 'value' => '444px' )
				)
            )
      	);
      	$attributes = array(
      		'img' => array(
      			'class' => 'size-medium wp-image'
  			)
  		);
  		$expected = array(
  			'sizes' => array('thumbnail', 'medium'),
  			'media_queries' => array(
  				'medium' => array( 'property' => 'min-width', 'value' => '444px' )
			)
		);

      	$custom_media_queries = new Custom_Media_Queries( $custom_media_queries );
      	
      	$this->assertTrue( $custom_media_queries->should_be_applied_when( 'image', $attributes ) );
      	$this->assertEquals($expected, $custom_media_queries->get_settings());
	}

	function test_when_page_slug_is_not_test()
	{
		$custom_media_queries = array(
			'cid' => array(
				'rule' => array(
	                'default' => 'false',
	                'when' => array(
                    	'key' => 'page_slug',
                    	'compare' => 'not_equals',
                    	'value' => 'test'
	                )
				),
				'smallestImage' => 'thumbnail',
				'breakpoints' => array(
					array( 'image_size' => 'medium', 'property' => 'min-width', 'value' => '500px' ),
					array( 'image_size' => 'large', 'property' => 'min-width', 'value' => '900px' ),
					array( 'image_size' => 'full', 'property' => 'min-width', 'value' => '1440px' )
				)
            ),
            'cid2' => array(
            	'rule' => array(
            		'default' => false,
            		'when' =>  array(
	            		'key' => 'page_slug',
	            		'compare' => 'equals',
	            		'value' => 'yet-another-test'
            		)
        		)
        	)
      	);
      	$wp_post_object = (object) array(
      		'ID' => 13,
      		'post_name' => 'other-test',
      		'post_title' => 'Other Test'
  		);
		$custom_media_queries = new Custom_Media_Queries( $custom_media_queries );
		
		$expected = array(
			'sizes' => array('thumbnail', 'medium', 'large', 'full'),
			'media_queries' => array(
				'medium' => array( 'property' => 'min-width', 'value' => '500px' ),
				'large' => array( 'property' => 'min-width', 'value' => '900px' ),
				'full' => array( 'property' => 'min-width', 'value' => '1440px' )
			)
		);

		$this->assertTrue( $custom_media_queries->should_be_applied_when('post', $wp_post_object) );
		$this->assertEquals($expected, $custom_media_queries->get_settings());
	}

	function test_when_image_size_is_medium()
	{
		$custom_media_queries = array(
			'cid' => array(
				'rule' => array(
	                'default' => 'false',
	                'when' => array(
                    	'key' => 'image',
                    	'image' => 'size',
                    	'compare' => 'equals',
                    	'value' => 'medium'
	                )
				),
				'smallestImage' => 'thumbnail',
				'breakpoints' => array(
					array( 'image_size' => 'medium', 'property' => 'min-width', 'value' => '500px' )
				)
			)
		);
		$attributes = array(
			'img' => array(
				'class' => 'wp-image size-medium',
				'alt' => 'Some image alt'
			)
		);
		$expected = array(
			'sizes' => array('thumbnail', 'medium'),
			'media_queries' => array(
				'medium' => array( 'property' => 'min-width', 'value' => '500px' )
			)
		);
		$custom_media_queries = new Custom_Media_Queries( $custom_media_queries );
		
		$this->assertTrue($custom_media_queries->should_be_applied_when('image', $attributes));
		$this->assertEquals($expected, $custom_media_queries->get_settings());
	}

	function test_default_settings_should_not_override_other_settings_when_checking_image()
	{
		$custom_media_queries = array(
			'cid' => array(
				'rule' => array(
	                'default' => 'true',
	                'when' => array(
                    	'key' => 'page_slug',
                    	'compare' => 'equals',
                    	'value' => 'test'
	                )
				),
				'smallestImage' => 'thumbnail',
				'breakpoints' => array(
					array( 'image_size' => 'medium', 'property' => 'min-width', 'value' => '300px' ),
					array( 'image_size' => 'large', 'property' => 'min-width', 'value' => '1024px' ),
					array( 'image_size' => 'full', 'property' => 'min-width', 'value' => '2048px' )
				)
            ),
            'cid1' => array(
				'rule' => array(
	                'default' => 'false',
	                'when' => array(
                    	'key' => 'page_slug',
                    	'compare' => 'equals',
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
      	$wp_post_object = (object) array(
      		'ID' => 13,
      		'post_name' => 'test',
      		'post_title' => 'Test'
  		);
  		$attributes = array(
			'img' => array(
				'class' => 'wp-image size-medium',
				'alt' => 'Some image alt'
			)
		);

		$custom_media_queries = new Custom_Media_Queries( $custom_media_queries );
		
		$expected = array(
			'sizes' => array('thumbnail', 'medium', 'large', 'full'),
			'media_queries' => array(
				'medium' => array( 'property' => 'min-width', 'value' => '500px' ),
				'large' => array( 'property' => 'min-width', 'value' => '900px' ),
				'full' => array( 'property' => 'min-width', 'value' => '1440px' )
			)
		);
		if ( $custom_media_queries->should_be_applied_when('post', $wp_post_object) ) {
			$settings = $custom_media_queries->get_settings();
		}
		if ( $custom_media_queries->should_be_applied_when('image', $attributes) ) {
			$settings = $custom_media_queries->get_settings();
		}

		$this->assertEquals($expected, $settings);
	}

}