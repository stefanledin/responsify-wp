<?php
function create_attachment()
{
	$attachment = array(
		'guid' => 'http://example.org/wp-content/uploads/2014/10/IMG_2089.jpg',
		'post_mime_type' => 'image/jpeg',
		'post_title' => 'Responsify',
		'post_status' => 'inherit'
	);
	$img = wp_insert_attachment( $attachment, 'IMG_2089.jpg' );
	wp_update_attachment_metadata( $img, array(
		'width' => 2448,
		'height' => 3264,
		'file' => '2014/10/IMG_2089.jpg',
		'sizes' => array(
			'thumbnail' => array(
                'file' => 'IMG_2089-480x640.jpg',
                'width' => 480,
                'height' => 640
            ),
            'medium' => array(
                'file' => 'IMG_2089-600x800.jpg',
                'width' => 600,
                'height' => 800
            ),
            'large' => array(
                'file' => 'IMG_2089-1024x1365.jpg',
                'width' => 1024,
                'height' => 1365
            )
		)
	) );
	return $img;
}