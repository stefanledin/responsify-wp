<?php
function create_attachment( $name = 'IMG_2089' )
{
	$attachment = array(
		'guid' => 'http://example.org/wp-content/uploads/2014/10/'.$name.'.jpg',
		'post_mime_type' => 'image/jpeg',
		'post_title' => 'Responsify',
		'post_status' => 'inherit'
	);
	$img = wp_insert_attachment( $attachment, $name.'.jpg' );
	wp_update_attachment_metadata( $img, array(
		'width' => 2448,
		'height' => 3264,
		'file' => '2014/10/'.$name.'.jpg',
		'sizes' => array(
			'thumbnail' => array(
                'file' => $name.'-480x640.jpg',
                'width' => 480,
                'height' => 640
            ),
            'medium' => array(
                'file' => $name.'-600x800.jpg',
                'width' => 600,
                'height' => 800
            ),
            'large' => array(
                'file' => $name.'-1024x1365.jpg',
                'width' => 1024,
                'height' => 1365
            )
		)
	) );
	return $img;
}
function create_retina_attachment()
{
	$attachment = array(
		'guid' => 'http://example.org/wp-content/uploads/2014/10/retina.jpg',
		'post_mime_type' => 'image/jpeg',
		'post_title' => 'Responsify',
		'post_status' => 'inherit'
	);
	$img = wp_insert_attachment( $attachment, 'retina.jpg' );
	wp_update_attachment_metadata( $img, array(
		'width' => 2448,
		'height' => 3264,
		'file' => '2014/10/retina.jpg',
		'sizes' => array(
			'thumbnail' => array(
                'file' => 'retina-480x640.jpg',
                'width' => 480,
                'height' => 640
            ),
            'thumbnail@1.5x' => array(
                'file' => 'retina-720x960.jpg',
                'width' => 720,
                'height' => 960
            ),
            'thumbnail@2x' => array(
                'file' => 'retina-960x1280.jpg',
                'width' => 960,
                'height' => 1280
            ),
            'medium' => array(
                'file' => 'retina-600x800.jpg',
                'width' => 600,
                'height' => 800
            ),
            'medium@1.5x' => array(
                'file' => 'retina-900x1200.jpg',
                'width' => 900,
                'height' => 1200
            ),
            'medium@2x' => array(
                'file' => 'retina-1200x1600.jpg',
                'width' => 1200,
                'height' => 1600
            ),
            'large' => array(
                'file' => 'retina-1024x1365.jpg',
                'width' => 1024,
                'height' => 1365
            ),
            'large@1.5x' => array(
                'file' => 'retina-1536x2047.jpg',
                'width' => 1536,
                'height' => 2047
            ),
            'large@2x' => array(
                'file' => 'retina-2048x2730.jpg',
                'width' => 2048,
                'height' => 2730
            )
		)
	) );
	return $img;
}

function create_png()
{
	$attachment = array(
		'guid' => 'http://example.org/wp-content/uploads/2014/12/logo.png',
		'post_mime_type' => 'image/png',
		'post_title' => 'PNG',
		'post_status' => 'inherit'
	);
	$img = wp_insert_attachment( $attachment, 'logo.png' );
	wp_update_attachment_metadata( $img, array(
		'width' => 2448,
		'height' => 3264,
		'file' => '2014/12/logo.png',
		'sizes' => array(
			'thumbnail' => array(
                'file' => 'logo-480x640.png',
                'width' => 480,
                'height' => 640
            ),
            'medium' => array(
                'file' => 'logo-600x800.png',
                'width' => 600,
                'height' => 800
            ),
            'large' => array(
                'file' => 'logo-1024x1365.png',
                'width' => 1024,
                'height' => 1365
            )
		)
	) );
	return $img;
}
