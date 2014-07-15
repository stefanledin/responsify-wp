#Responsify WP
This is a Wordpress plugin that helps you with responsive images. It's based on the excellent
[Picturefill script](https://github.com/scottjehl/picturefill) created by [Scott Jehl](http://scottjehl.com).  
##What it does
In short, it will replace all ``<img>`` tags within ``the_content`` with the markup that is required by Picturefill.
For example, you might have a template that looks like this:  

````html
<article>
	<h1><?php the_title();?></h1>
	<?php the_content();?>
</article>
````

This might output something like this:

````html
<article>
	<h1>Hello world</h1>
	<p>Lorem ipsum dolor sit amet...</p>
	<img src="example.com/wp-content/uploads/2014/03/IMG_4540.jpg" alt="Image description">
</article>
````

But once you have activated the plugin, it will look like this instead:

````html
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
````

Congratulations! You're now serving images with an appropriate size to the users.  
The different versions of the image in the example above is in the standard ``thumbnail``, ``medium``, ``large`` and ``full`` sizes. 
The **media queries** are based on the with of the "previous" image.  
Any **custom sizes** of the image will also be found and used.  
It is possible to **select which sizes** that should be used from the RWP settings page inside WordPress.

##Functions
If you want to generate Picturefill markup in other places of the template, the ``Picture::create()`` function allows you to do that.  

````php
<?php echo Picture::create( 'element', $attachment_id ); ?>
```` 

Let's say that you have the following markup for a very large header image:

````html
<header>
	<?php the_post_thumbnail( 'full' ); ?>
</header>
````

As you probably know, ``the_post_thumbnail()`` will just create a regular ``<img>`` tag for the full-size image in this case. 
But you does of course not want to send a big 1440px image to a mobile device. This can easily be solved like this:

````html
<header>
	<?php
	$thumbnail_id = get_post_thumbnail_id( $post->ID );
	echo Picture::create( 'element', $thumbnail_id );
	?>
</header>
````

You can also specify which sizes of the image that should be used:

````php
<?php 
$thumbnail_id = get_post_thumbnail_id( $post->ID );
echo Picture::create( 'element', $thumbnail_id, array(
	'sizes' => array( 'medium', 'large', 'full' )
) ); 
?>
````

###Background images
But what's the deal with the first argument? ``element``? Well, there might be times when you have a ``div`` with a very large background image. It's very easy to replace the image with a smaller one using media queries in your stylesheet, but that requires you to hard code the filename.  
What if it's some kind of header image that can be changed later by the administrator of the site? In that case, you cannot hard code the filename inside your stylesheet.  
You might have done something like this in the past:

````html
<header id="hero" style="background-image: url(<?php echo $dynamic_header_image;?>)">
	<h1>Overlying headline</h1>
</header>
````

Instead, you could do this:

````php
<?php
echo Picture::create( 'style', $dynamic_header_image_ID, array(
	'selector' => '#hero'
) );
?>
<header id="hero">
	<h1>Overlying headline</h1>
</header>
````

This will generate a ``style`` tag containing the following:

````css
#hero {
	background-image: url("example.com/wp-content/uploads/2014/03/IMG_4540-150x150.jpg");
}
@media screen and (min-width: 150px) {
	#hero {
		background-image: url("example.com/wp-content/uploads/2014/03/IMG_4540-300x199.jpg");
	}
} 
// And so on...
	
````

You can of course specify the sizes that you wanna use in the same way as in the previous example. 

##Function reference

````php
<?php
echo Picture::create( $type, $attachment_id, $settings );
?>
````

* **$type**: (string) (required). 'element' or 'style'.
* **$attachment_id**: (integer) (required). The ID of the attachment.
* **$settings**: (array) (optional).

###Settings
These are the settings that is currently avaliable:

* **sizes** (array): The image sizes that you want to use.
* **media_queries** (array): An associative array of names of the image sizes and a custom media query.
* **attributes** (array): An associative array of attribute names and values that you want to have on the ``span`` tags.

####Example - sizes

````php
<?php
$settings = array(
	'sizes' => array('medium', 'large')
);
?>
````

````html
<span data-picture>
	<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-300x199.jpg" data-media="(min-width: 150px)"></span>
	<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-1024x681.jpg" data-media="(min-width: 300px)"></span>
	<noscript>
		<img src="example.com/wp-content/uploads/2014/03/IMG_4540-300x199.jpg" alt="Image description">
	</noscript>
</span>
````

####Example - custom media queries

````php
<?php
$settings = array(
	'sizes' => array('thumbnail', 'medium', 'large'),
	'media_queries' => array(
		'medium' => 'min-width: 500px',
		'large' => 'min-width: 1024px'
	)
);
?>
````

Notice that you **should not** specify a media query for the smallest image size. You **must** specify a media query for 
all the selected image sizes.

````html
<span data-picture>
	<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-150x150.jpg"></span>
	<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-300x199.jpg" data-media="(min-width: 550px)"></span>
	<span data-src="example.com/wp-content/uploads/2014/03/IMG_4540-1024x681.jpg" data-media="(min-width: 1024px)"></span>
	<noscript>
		<img src="example.com/wp-content/uploads/2014/03/IMG_4540-150x150.jpg" alt="Image description">
	</noscript>
</span>
````

####Example - attributes

````php
<?php
$settings = array(
	'attributes' => array(
		'picture_span' => array(
			'id' => 'picture-element',
			'data-alt' => 'Overrides the alternative text of the image'
		),
		'src_span' => array(
			'class' => 'responsive-image'
		)
	)
);
?>
````

````html
<span data-picture id="picture-element" data-alt="Overrides the alternative text of the image">
	<span class="responsive-image" data-src="example.com/wp-content/uploads/2014/03/IMG_4540-150x150.jpg"></span>
	<span class="responsive-image" data-src="example.com/wp-content/uploads/2014/03/IMG_4540-300x199.jpg" data-media="(min-width: 150px)"></span>
	<span class="responsive-image" data-src="example.com/wp-content/uploads/2014/03/IMG_4540-1024x681.jpg" data-media="(min-width: 300px)"></span>
	<span class="responsive-image" data-src="example.com/wp-content/uploads/2014/03/IMG_4540.jpg" data-media="(min-width: 1024px)"></span>
	<noscript>
		<img src="example.com/wp-content/uploads/2014/03/IMG_4540-150x150.jpg" alt="Image description">
	</noscript>
</span>
````
