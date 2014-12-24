#Responsify WP
Responsify WP is the WordPress plugin that cares about responsive images. It uses the excellent
[Picturefill polyfill](https://github.com/scottjehl/picturefill) created by [Scott Jehl](http://scottjehl.com) to make 
it work across most browsers.  
###Content
- [Description](#description)
- [Settings](#settings)
	- [Image sizes](#settings-image-sizes)
	- [Sizes attribute](#settings-sizes-attribute)
	- [Media queries](#settings-media-queries)
- [Functions](#functions)
	- [Element/Img](#functions-element)
	- [Style](#functions-style)
	- [Reference](#functions-reference)
		- [Settings](#functions-reference-settings)
		- [Example - sizes](#functions-reference-example-sizes)
		- [Example - custom media queries](#functions-reference-example-custom-media-queries)
		- [Example - attributes](#functions-reference-example-attributes)
		    - [img](#functions-reference-example-attributes-img)
		    - [picture](#functions-reference-example-attributes-picture)
		    - [span](#functions-reference-example-attributes-span)

##<a name="description"></a>Description
In short, it will replace all ``<img>`` tags within ``the_content`` with responsive images.
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
	<img src="full-size.jpg" alt="Image description">
</article>
````

But once you have activated the plugin, it will look like this instead:

````html
<article>
	<h1>Hello world</h1>
	<p>Lorem ipsum dolor sit amet...</p>
	<img sizes="(min-width 1024px) 1440px, (min-width: 300px) 1024px, (min-width: 150px) 300px, 150px" 
	    srcset="thumbnail.jpg 150w,
	    medium.jpg 300w,
	    large.jpg 1024w,
	    full-size.jpg 1440w"
	    alt="Image description">
</article>
````

On the RWP settings page, you can select between using an ``<img>`` tag with ``sizes``/``srcset`` attributes and the ``<picture>`` element.

````html
<picture>
    <source srcset="full-size.jpg" media="(min-width: 1024px)">
    <source srcset="large.jpg" media="(min-width: 300px)">
    <source srcset="medium.jpg" media="(min-width: 150px)">
    <img srcset="thumbnail.jpg" alt="Image description">
</picture>
````

Congratulations! You're now serving images with an appropriate size to the users.    
The different versions of the image in the examples above is in the standard ``thumbnail``, ``medium``, ``large`` and ``full`` sizes. 
Any **custom sizes** of the image will also be found and used.  
The **media queries** that the ``picture`` element are using is based on the width of the "previous" image.  

RWP also has support for the old markup pattern that uses ``<span>`` tags. However, this solution has some 
limitations and is not the recommended.

````html
<span data-picture data-alt="Image description">
    <span data-src="thumbnail.jpg"></span>
    <span data-src="medium.jpg" data-media="(min-width: 150px)"></span>
    <span data-src="large.jpg" data-media="(min-width: 300px)"></span>
    <span data-src="full-size.jpg" data-media="(min-width: 1024px)"></span>
    <noscript>
        <img src="thumbnail.jpg" alt="Image description">
    </noscript>
</span>
````

##<a name="settings"></a>Settings
###<a name"settings-image-sizes"></a>Image sizes
You can **select which image sizes** that the plugin should use from the settings page.  
These settings can be overwritten from your templates. 

````php
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
````

###<a name"settings-sizes-attribute"></a>Sizes attribute
By default, ``<img>`` tags with ``sizes``/``srcset`` is the selected markup pattern. ``100vw`` is the default value of 
the ``sizes`` attribute, but it's possible to specify your own. 
````php
<?php
$posts = get_posts(array(
	'post_type' => 'portfolio',
	'rwp_settings' => array(
		'sizes' => array('medium', 'large'),
		'attributes' => array(
			'sizes' => '(min-width: 500px) 1024px, 300px'
		)
	)
));
?>
````

This will produce the following:
````html
<img srcset="medium.jpg 300w, large.jpg 1024w" sizes="(min-width: 500px) 1024px, 300px)" src="medium.jpg">
````

``large.jpg`` will be selected when the window width is at least 500px. On smaller screens, ``medium.jpg`` will be selected.  

###<a name"settings-media-queries"></a>Media queries
If you've selected the ``picture`` element in the settings, you can specify your own media queries for the different image sizes.

````php
<?php
$posts = get_posts(array(
	'post_type' => 'portfolio',
	'rwp_settings' => array(
		'sizes' => array('thumbnail', 'medium', 'large'),
		'media_queries' => array(
			'medium' => 'min-width: 500px',
			'large' => 'min-width: 1024px'
		)
	)
));
?>
````

In the example above, ``thumbnail`` is the smallest image size and should therefore have no media query specified. 
``medium`` will be selected if the screen is 500 px or larger. The same goes for ``large`` if the screen is at least 1024 px.

````html
<picture>
    <source srcset="large.jpg" media="(min-width: 1024px)">
    <source srcset="medium.jpg" media="(min-width: 500px)">
    <img srcset="thumbnail.jpg" alt="Image description">
</picture>
````

##<a name="functions"></a>Functions  
If you want to generate Picturefill markup in other places of the template, the ``Picture::create()`` function allows you to do that.  
###<a name="functions-element"></a>Element / Img
Based on an attachment ID, you can generate either a ``picture`` **element** or a **img** tag with ``srcset``/``sizes`` attributes.

````php
<?php echo Picture::create( 'img', $attachment_id ); ?>
````
 
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

###<a name="functions-style"></a>Style
There might be times when you have a ``div`` with a very large background image. It's very easy to replace the image with a smaller one using media queries in your stylesheet, but that requires you to hard code the filename.  
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
	background-image: url("thumbnail.jpg");
}
@media screen and (min-width: 150px) {
	#hero {
		background-image: url("medium.jpg");
	}
} 
// And so on...
	
````

You can of course specify sizes and media queries here to.

````php
<?php
echo Picture::create( 'style', $dynamic_header_image_ID, array(
	'selector' => '#hero',
	'sizes' => array('medium', 'large', 'full'),
	'media_queries' => array(
		'large' => 'min-width: 500px',
		'full' => 'min-width 1024px'
	)
) );
?>
````

###<a name="functions-reference"></a>Reference

````php
<?php
echo Picture::create( $type, $attachment_id, $settings );
?>
````

* **$type**: (string) (required). 'img', 'element' or 'style'.
* **$attachment_id**: (integer) (required). The ID of the attachment.
* **$settings**: (array) (optional).

####<a name="functions-reference-settings"></a>Settings
These are the settings that is currently avaliable:

* **sizes** (array): The image sizes that you want to use.
* **media_queries** (array): An associative array of names of the image sizes and a custom media query.
* **attributes** (array): An associative array of attribute names and values that you want to add on the element (see example).
* **ignored_image_formats** (array): An array of image formats that you want RWP to ignore.

#####<a name="functions-reference-example-sizes"></a>Example - sizes

````php
<?php
$settings = array(
	'sizes' => array('medium', 'large')
);

echo Picture::create( 'img', $attachment_id, $settings );
echo Picture::create( 'element', $attachment_id, $settings );
?>
````

````html
<img sizes="(min-width: 300px) 1024px, 300px"
    srcset="large.jpg 1024w,
  medium.jpg 300w"
    alt="Image description">
    
<picture>
    <source srcset="large.jpg" media="(min-width: 300px)">
    <img srcset="medium.jpg" alt="Image description">
</picture>
````

#####<a name="functions-reference-example-custom-media-queries"></a>Example - custom media queries

````php
<?php
$settings = array(
	'sizes' => array('thumbnail', 'medium', 'large'),
	'media_queries' => array(
		'medium' => 'min-width: 500px',
		'large' => 'min-width: 1024px'
	)
);
echo Picture::create( 'element', $attachment_id, $settings );
?>
````

Notice that you **should not** specify a media query for the smallest image size. You **must** specify a media query for 
all the selected image sizes.

````html
<picture>
    <source srcset="large.jpg" media="(min-width: 1024px)">
    <source srcset="medium.jpg" media="(min-width: 500px)">
    <img srcset="thumbnail.jpg" alt="Image description">
</picture>
````

#####<a name="functions-reference-example-attributes"></a>Example - attributes

######<a name="functions-reference-example-attributes-img"></a>img

````php
<?php
$settings = array(
	'attributes' => array(
		'id' => 'responsive-image',
		'sizes' => '(min-width: 500px) 1024px, 300px'
	)
);
echo Picture::create( 'img', $attachment_id, $settings );
?>
````

````html
<img id="responsive-image" sizes="(min-width: 500px) 1024px, 300px" 
	    srcset="thumbnail.jpg 150w,
	    medium.jpg 300w,
	    large.jpg 1024w,
	    full-size.jpg 1440w"
	    alt="Image description">
````

######<a name="functions-reference-example-attributes-picture"></a>picture

````php
<?php
$settings = array(
	'attributes' => array(
		'picture' => array(
			'id' => 'picture-element'
		),
		'source' => array(
			'data-foo' => 'bar'
		),
		'img' => array(
		    'id' => 'responsive-image'
		)
	)
);
echo Picture::create( 'element', $attachment_id, $settings );
?>
````

````html
<picture id="picture-element">
    <source data-foo="bar" srcset="full-size.jpg" media="(min-width: 1024px)">
    <source data-foo="bar" srcset="large.jpg" media="(min-width: 300px)">
    <source data-foo="bar" srcset="medium.jpg" media="(min-width: 150px)">
    <img id="responsive-image" srcset="thumbnail.jpg" alt="Image description">
</picture>
````

######<a name="functions-reference-example-attributes-span"></a>span

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
echo Picture::create( 'element', $attachment_id, $settings );
?>
````

````html
<span data-picture id="picture-element" data-alt="Overrides the alternative text of the image">
	<span class="responsive-image" data-src="thumbnail.jpg"></span>
	<span class="responsive-image" data-src="medium.jpg" data-media="(min-width: 150px)"></span>
	<span class="responsive-image" data-src="large.jpg" data-media="(min-width: 300px)"></span>
	<span class="responsive-image" data-src="full-size.jpg" data-media="(min-width: 1024px)"></span>
	<noscript>
		<img src="thumbnail.jpg" alt="Image description">
	</noscript>
</span>
````
