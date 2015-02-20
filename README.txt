=== Responsify WP ===
Contributors: stefanledin
Tags: responsive images, picture, srcset, sizes, picture element, picture markup, picturefill, images, retina, responsive background
Requires at least: 3.8.1
Tested up to: 4.1
Stable tag: 1.7.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Responsive images. Plug and play.  

== Description ==

Responsify WP is the WordPress plugin that cares about responsive images.

* Use ``img`` with srcset/sizes attributes.
* ...or the ``picture`` element.
* Works with or without [Picturefill](http://scottjehl.github.io/picturefill/).
* Supports high resolution images (retina).
* Custom media queries.
* Handpick which image sizes to use.
* Responsive background images.

Responsify WP finds all images inside the_content() and makes them responsive.
For example, you might have a template that looks like this:  

	<article>
		<h1><?php the_title();?></h1>
		<?php the_content();?>
	</article>

That will output something like this:
	
	<article>
		<h1>Hello world</h1>
		<p>Lorem ipsum dolor sit amet...</p>
		<img src="large.jpg" alt="Image description">
	</article>
	
But once you have activated the plugin, it will look like this instead:

	<article>
		<h1>Hello world</h1>
		<p>Lorem ipsum dolor sit amet...</p>
		<img sizes="(min-width: 300px) 1024px, (min-width: 150x) 300px, 150px"
            srcset="thumbnail.jpg 150w,
            medium.jpg 300w,
            large.jpg 1024w"
            alt="Image description">
	</article>

You can also choose to use the ``picture`` element instead:

	<article>
		<h1>Hello world</h1>
		<p>Lorem ipsum dolor sit amet...</p>
		<picture>
		    <source srcset="full-size.jpg" media="(min-width: 1024px)">
		    <source srcset="large.jpg" media="(min-width: 300px)">
		    <source srcset="medium.jpg" media="(min-width: 150px)">
		    <img srcset="thumbnail.jpg" alt="Image description">
		</picture>
	</article>

It also works with high resolution (retina) images:

	<article>
		<h1>Hello world</h1>
		<p>Lorem ipsum dolor sit amet...</p>
		<picture>
		    <source srcset="full-size.jpg" media="(min-width: 1024px)">
		    <source srcset="large.jpg, large_retina.jpg 2x" media="(min-width: 300px)">
		    <source srcset="medium.jpg, medium_retina.jpg 2x" media="(min-width: 150px)">
		    <img srcset="thumbnail.jpg, thumbnail_retina.jpg 2x" alt="Image description">
		</picture>
	</article>

The different versions of the image in the examples above is in the standard ``thumbnail``, ``medium``, ``large`` and ``full`` sizes. 
The **media queries** are based on the width of the "previous" image.  
Any **custom sizes** of the image will also be found and used.

### Settings
You can **select which image sizes** that the plugin should use from the RWP settings page.  
These settings can be overwritten from your templates.  

	<?php

	// Using get_posts()
	$posts = get_posts( array(
		'post_type' => 'portfolio',
		'rwp_settings' => array(
			'sizes' => array('large', 'full')
		)
	) );

	// Using WP_Query()
	$query = new WP_Query( array(
		'category_name' => 'wordpress',
		'rwp_settings' => array(
			'sizes' => array('large', 'full')
		)
	) );
	if ( $query->have_posts() ) {
		// ...
	}
	?>

**Available settings:**

* Select which image sizes to use.
* Set/override attributes.
* Set custom media queries.
* Turn on/off retina.
* Ignore image formats.

### Picture::create( $type, $attachment_id, $settings )
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

		// Generate an <img> tag with srcset/sizes attributes.
		echo Picture::create( 'img', $thumbnail_id );

		// Generate a <picture> element
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

1. Select the image sizes that you want to use in your templates. It's also 
possible to specify your own media queries.
2. Use the Picture::create() function to generate responsive images inside your templates.
3. Congratulations! A responsive header image.
4. You can also use the Picture::create( 'style' ) function to generate CSS and media queries for large background images.
5. A <style> tag will be created and contains the generated media queries for the background.
6.
7.

== Changelog ==
= 1.7.1 =
* Bugfix. The $content_width variable in functions.php could make the generated markup to be slightly incorret.

= 1.7.0 =
* Works on featured images out of the box.
* Apply RWP on custom content filters using the rwp_add_filters filter.
* Support for high resolution (retina) images.
* Picture::create('attributes') returns only the generated attributes.

= 1.6.5 =
* Critical PHP 5.3 bugfix

= 1.6.4 =
* Bugfix. The default value of the sizes attribute didn't work as expected in browsers with native support.
* Minor bugfixes and improvements.
* Added the ability to disable Picturefill.

= 1.6.3 =
* Updated Picturefill to version 2.2.0
* Minor bugfixes
* Tested with WordPress 4.1

= 1.6.2 =
* Added the ability to ignore image formats.
* Bugfixes.

= 1.6.1 =
* Critical bugfix. If the picture element was selected before 1.6.0, it stopped working.

= 1.6.0 =
* RWP now supports the sizes/srcset attributes. It's the new default markup pattern.
* Bugfixes and improvements.

= 1.5.2 =
* Bugfix. Custom media queries works with the picture element now.

= 1.5.1 =
* All attributes on the original img tag are now preserved and passed on to the new element.
* External images will be ignored.
* Bugfixes
* Thanks to habannah for all of her help with pointing out issues with RWP.

= 1.5.0 =
* Now it's possible to select if you want to use span or the real picture element.
* It's also possible to only use the Picture::create() without replacing any img tags in the content.
* Bugfixes.

= 1.4.3 =
* Bugfix. If an image is beeing inserted by a shortcode, the generated markup could be replaced.

= 1.4.2 =
* Bugfix. On PHP 5.3.28, the plugin could make the site crash. Not anymore!

= 1.4.1 =
* Bugfix. If an image doesn't exists in a selected size, WordPress returns the full size image instead. That would break the media queries.  
* Tested with WordPress 4 beta 2.
* Improved documentation.

= 1.4 =
* Now it's possible to set custom media queries.

= 1.3 =
* Settings can be passed in the query.

= 1.2 =
* The content filter now works on PHP 5.3

== Upgrade Notice ==
= 1.7.1 =
* Bugfix. The $content_width variable in functions.php could make the generated markup to be slightly incorret.

= 1.7.0 =
* Works on featured images out of the box.
* Apply RWP on custom content filters using the rwp_add_filters filter.
* Support for high resolution (retina) images.
* Picture::create('attributes') returns only the generated attributes.

= 1.6.5 =
* Critical PHP 5.3 bugfix

= 1.6.4 = 
* Bugfixes and improvements.
* Added the ability to turn off Picturefill.

= 1.6.3 =
* Updated Picturefill to version 2.2.0
* Minor bugfixes

= 1.6.2 =
* Added the ability to ignore image formats.
* Bugfixes.

= 1.6.1 =
* Critical bugfix. If the picture element was selected before 1.6.0, it stopped working.

= 1.6.0 =
* Support for the sizes/srcset attributes. It's the default markup pattern now.

= 1.5.2 =
* Bugfix. Custom media queries works with the picture element now.

= 1.5.1 =
* All attributes on the original img tag are now preserved and passed on to the new element.
* External images will be ignored.
* Bugfixes

= 1.5.0 =
Added the possibility to choose if you want to use span or the real picture element. Also bugfixes. 

= 1.4.3 =
Bugfix. If an image is beeing inserted by a shortcode, the generated markup could be replaced.

= 1.4.2 =
Bugfix. On PHP 5.3.28, the plugin could make the site crash. Not anymore!

= 1.4.1 =
Bugfix. If an image doesn't exists in a selected size, WordPress returns the full size image instead. That would break the media queries.

