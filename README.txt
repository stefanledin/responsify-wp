=== Responsify WP ===
Contributors: stefanledin
Tags: responsive images, picture, picture element, picture markup, picturefill, images, responsive background
Requires at least: 3.8.1
Tested up to: 3.9.1
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Responsify WP helps you generate the markup required for responsive images. 

== Description ==

Responsify WP helps you with responsive images. It creates the markup required for [Picturefill](https://github.com/scottjehl/picturefill), a polyfill for the ``<picture>`` element.  
In short, it will replace all ``<img>`` tags within ``the_content`` with the markup that is required by Picturefill.
For example, you might have a template that looks like this:  

	<article>
		<h1><?php the_title();?></h1>
		<?php the_content();?>
	</article>

This might output something like this:
	
	<article>
		<h1>Hello world</h1>
		<p>Lorem ipsum dolor sit amet...</p>
		<img src="example.com/wp-content/uploads/2014/03/IMG_4540.jpg" alt="Image description">
	</article>
	
But once you have activated the plugin, it will look like this instead:

	<article>
		<h1>Hello world</h1>
		<p>Lorem ipsum dolor sit amet...</p>
		<span data-picture data-alt="Image description">
			<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-150x150.jpg"></span>
			<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-300x199.jpg" data-media="(min-width: 150px)"></span>
			<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-1024x681.jpg" data-media="(min-width: 300px)"></span>
			<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540.jpg" data-media="(min-width: 1024px)"></span>
			<noscript>
				<img src="example.com/wp-content/uploads/2014/03/IMG_4540-150x150.jpg" alt="Image description">
			</noscript>
		</span>
	</article>

The different versions of the image in the example above is in the standard ``thumbnail``, ``medium``, ``large`` and ``full`` sizes. 
The **media queries** are based on the width of the "previous" image.  
Any **custom sizes** of the image will also be found and used.  
It is also possible to **select which sizes** that should be used from the RWP settings page inside WordPress.

### Picture::create()
In your templates, you can use the ``Picture::create()`` function to generate Picturefill markup.  
Let's say that you have the following markup for a very large header image:

	<header>
		<?php the_post_thumbnail( 'full' ); ?>
	</header>

As you probably know, ``the_post_thumbnail()`` will just create a regular ``<img>`` tag for the full-size image in this case. 
But you don't want to send a big 1440px image to a mobile device. This can easily be solved like this:

	<header>
		<?php
		$thumbnail_id = get_post_thumbnail_id( $post->ID );
		echo Picture::create( 'element', $thumbnail_id );
		?>
	</header>

Full documentation and examples can be found at [GitHub](https://github.com/stefanledin/responsify-wp).

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Responsify WP'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `responsify-wp.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `responsify-wp.zip`
2. Extract the `responsify-wp` directory to your computer
3. Upload the `responsify-wp` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Screenshots ==

1. Use the Picture::create() function to generate Picturefill markup inside your templates.
2. Congratulations! A responsive header image.
3. You can also generate CSS and media queries for large background images.

