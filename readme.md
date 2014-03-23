#Picturefill helper
This is a little Wordpress plugin that helps you with responsive images. It's based on the excellent
[Picturefill script](https://github.com/scottjehl/picturefill) created by [Scott Jehl](http://scottjehl.com).  
##What it does
In short, it generates all the necessary markup that is required by Picturefill. All you have to do is to pass the attachment ID as an argument.  The plugin will then find all sizes of the image, including your custom image sizes, and generate the markup.
###Example
``<?php Picture::create('element', 27); ?>``  
This will create the following markup:

	<span data-picture="" data-alt="Image description">
		<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-150x150.jpg"></span>
		<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-300x199.jpg" data-media="(min-width: 150px)"></span>
		<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-480x319.jpg" data-media="(min-width: 300px)"></span>
		<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-880x585.jpg" data-media="(min-width: 480px)"></span>
		<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-1024x681.jpg" data-media="(min-width: 880px)"></span>
		<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540.jpg" data-media="(min-width: 1024px)"></span>
		<noscript>
			<img src="example.com/wp-content/uploads/2014/03/IMG_4540-150x150.jpg" alt="Image description">
		</noscript>
	</span>
	
Besides of ``thumbnail``, ``medium``, ``large`` and ``full``, there are two custom image sizes used in this example.  The media queries are based on the width of the "previous" image.  
You can also specify which sizes that should be used:  
	
	<?php 
	Picture::create('element', 27, array(
		'sizes' => array('medium', 'large', 'full')
	)); 
	?>
	
But what's the deal with the first argument? ``element``? Well, there might be times when you have a ``div`` with a very large background image. It's very easy to replace the image with a smaller one using media queries in your stylesheet, but that requires you to hard code the name of the image.  
What if it's some kind of header image that can be changed later by the administrator of the site? In that case, you cannot hard code the filename inside your stylesheet.  
Instead, you could do this:

	<?php
	Picture::create('style', 27, array(
		'selector' => '#header'
	));
	?>

This will generate the following:

	<style>
	#header {
		background-image: url("example.com/wp-content/uploads/2014/03/IMG_4540-150x150.jpg");
	}
	@media screen and (min-width: 150px) {
		#header {
		background-image: url("example.com/wp-content/uploads/2014/03/IMG_4540-300x199.jpg");
		}
	} 
	// And so on...
	</style>
	
You can of course specify the sizes that you wanna use in the same way as in the previous example. 

##It's a beta
This plugin is a work in progress and doesn't even have a real name yet. There are more features that I want to implement, like the ability to specify custom media queries along with the image sizes for example.  
And of course, it should work with ``the_content()`` also...
	

