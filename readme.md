# Responsify WP
[Responsify WP](https://wordpress.org/plugins/responsify-wp/) is the WordPress plugin that cares about responsive images.

* Use ``img`` with srcset/sizes attributes.
* ...or the ``picture`` element.
* Works with or without [Picturefill](http://scottjehl.github.io/picturefill/).
* Supports high resolution images (retina).
* Custom media queries.
* Handpick which image sizes to use.
* Responsive background images.

**Basic demonstration**  
[https://www.youtube.com/watch?v=3ThYWO6vHKI](https://www.youtube.com/watch?v=3ThYWO6vHKI&spfreload=10)  
**Demo site**  
[http://responsifywp.com/demo/](http://responsifywp.com/demo/)

### Content
- [Description](#description)
- [Settings](#settings)
	- [Image sizes](#settings-image-sizes)
	- [Sizes attribute](#settings-sizes-attribute)
	- [Media queries](#settings-media-queries)
	- [Retina](#settings-retina)	
- [Retina](#retina)
	- [Setup](#retina-setup)
	- [Examples](#retina-examples)
- [Functions](#functions)
	- [Element/Img](#functions-element)
	- [Style](#functions-style)
	- [Attributes](#functions-attributes)
	- [Reference](#functions-reference)
		- [Settings](#functions-reference-settings)
		- [Example - sizes](#functions-reference-example-sizes)
		- [Example - custom media queries](#functions-reference-example-custom-media-queries)
		- [Example - attributes](#functions-reference-example-attributes)
		    - [img](#functions-reference-example-attributes-img)
		    - [picture](#functions-reference-example-attributes-picture)
		    - [span](#functions-reference-example-attributes-span)
- [Filters](#filters)
	- [Edit generated element](#filters-edit-generated-element)
	- [Edit attributes](#filters-edit-attributes)
	- [Add filters](#filters-add-filters)
- [Ignore images](#ignore-images)

## <a name="description"></a>Description
In short, it will replace all ``<img>`` tags within ``the_content`` (or other filters that you can [add yourself](#filters)) with responsive images.
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
	<img sizes="(min-width: 1024px) 1440px, (min-width: 300px) 1024px, (min-width: 150px) 300px, 150px" 
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

## <a name="settings"></a>Settings
### <a name="settings-image-sizes"></a>Image sizes
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
foreach( $posts as $post ) {
	// ...
}

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

### <a name="settings-sizes-attribute"></a>Sizes attribute
By default, ``<img>`` tags with ``sizes``/``srcset`` is the selected markup pattern. You can override the calculated value of the ``sizes`` attribute by doing this:   
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

### <a name="settings-media-queries"></a>Media queries
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

### <a name="settings-retina"></a>Retina
On the RWP settings page, you can choose if high resolution (retina) images should be used or not. This can be overwritten 
by setting ``retina`` to either ``true`` or ``false`` in the ``rwp_settings`` array.  
If set to ``true``, RWP will use all images that has the ``@[value]x`` suffix in the name, like ``thumbnail@2x`` or 
``medium@1.5x``.

````php
<?php
$posts = get_posts(array(
    'rwp_settings' => array(
        'retina' => true // or false
    )
));
?>
````

In this example, the three image sizes has a retina version which is twice the size.

````html
<picture>
    <source srcset="full.jpg" media="(min-width: 1024px)">
    <source srcset="large.jpg, large_retina.jpg 2x" media="(min-width: 300px)">
    <source srcset="medium.jpg, medium_retina.jpg 2x" media="(min-width: 150px)">
    <source srcset="thumbnail.jpg, thumbnail_retina.jpg 2x">
    <img srcset="thumbnail.jpg" alt="Image description">
</picture>
````

You can also add image sizes for other pixel densities, like ``1.5x`` or ``3x``. This is the same example that also has a 
retina version that's three times as large.

````html
<picture>
    <source srcset="full.jpg" media="(min-width: 1024px)">
    <source srcset="large.jpg, large_retina.jpg 2x, large_retina_xl.jpg 3x" media="(min-width: 300px)">
    <source srcset="medium.jpg, medium_retina.jpg 2x, medium_retina_xl.jpg 3x" media="(min-width: 150px)">
    <source srcset="thumbnail.jpg, thumbnail_retina.jpg 2x, thumbnail_retina_xl.jpg 3x">
    <img srcset="thumbnail.jpg" alt="Image description">
</picture>
````

You can select which versions that you wanna use by default on the RWP settings page. This can also be overwritten by passing 
an array of pixel densities to ``rwp_settings``.

````php
<?php
$posts = get_posts(array(
    'rwp_settings' => array(
        'retina' => array( '1.5x', '2x' )
    )
));
?>
````

For a single density, you can of course pass it as a string.

````php
<?php
'rwp_settings' => array(
	'retina' => '2x'
)
?>
````

## <a name="retina"></a>Retina
A new feature in Responsify WP 1.7 is the support for high resolution (retina) images. This means that devices with 
high pixel density screens will receive larger images which will look crisp and sharp.  
However, larger images means heavier images, which is something RWP is meant to prevent. With that said, this is how 
the generated markup looks like:  

````html
<!-- Picture element -->
<picture>
    <source srcset="large.jpg, large_retina.jpg 2x" media="(min-width: 300px)">
    <source srcset="medium.jpg, medium_retina.jpg 2x" media="(min-width: 150px)">
    <img srcset="thumbnail.jpg" alt="Image description">
</picture>

<!-- img with srcset/sizes -->
<img srcset="
	thumbnail.jpg 150w,
	medium.jpg 300w,
	medium_retina.jpg 600w,
	large.jpg 1024w,
	large_retina.jpg 2048w"
	sizes="(min-width: 300px) 1024px, (min-width: 150px) 300px, 150px">
````

### <a name="retina-setup"></a>Setup  
If you want to use retina images, they has to be generated first. This requires you to manually add custom image sizes 
using the [`add_image_size`](http://codex.wordpress.org/Function_Reference/add_image_size) function.  
Let's use the three default image sizes that ships with WordPress and their default settings as an example:

- `thumbnail` 150x150px
- `medium` 300x300px
- `large` 1024x1024px

If you want to create retina versions of these image sizes, you should add something like this to your `functions.php`:

````php
<?php
add_image_size( 'medium@2x', 600, 600 );
add_image_size( 'large@2x', 2048, 2048 );
?>
````

As you can see, the `@2x` suffix indicates that `medium@2x` is twice as large as `medium` for example. 
There's no `thumbnail@2x` since that would be the same as `medium`.  
Images that are uploaded to WordPress from now on will be generated in these sizes. If you want to create retina versions 
of existing images, use the [Regenerate thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) plugin.  
RWP works with other pixel densities beside of `2x` as well. `1.5x` or `3x` will also work just fine. The important thing 
is that you name your custom image sizes like this: 

````
[original]@[value]x`  
````

### <a name="retina-examples"></a>Examples  
Add `1.5x` and `2x` versions of the default image sizes: 
````php
<?php
add_image_size( 'thumbnail@1.5x', 225, 225 );
add_image_size( 'medium@1.5x', 450, 450 );
add_image_size( 'medium@2x', 600, 600 );
add_image_size( 'large@2x', 2048, 2048 );
?>
````

It of course works with other custom sizes as well!

````php
<?php
add_image_size( 'tablet-landscape', 800, 600 );
add_image_size( 'tablet-landscape@2x', 1600, 1200 );
add_image_size( 'tablet-landscape@3x', 2400, 1800 );
?>
````

`thumbnail`, `medium` and `large` will work as usual:

````html
<!-- Picture element -->
<picture>
    <source srcset="large.jpg" media="(min-width: 800px)">
    <source srcset="tablet_landscape.jpg, tablet_landscape@2x.jpg 2x, tablet_landscape@3x.jpg 3x" media="(min-width: 150px)">
    <source srcset="medium.jpg" media="(min-width: 150px)">
    <img srcset="thumbnail.jpg" alt="Image description">
</picture>

<!-- img with srcset/sizes -->
<img srcset="
	thumbnail.jpg 150w,
	medium.jpg 300w,
	tablet_landscape.jpg 800w,
	tablet_landscape@2x.jpg 1600w,
	tablet_landscape@3x.jpg 2400w,
	large.jpg 1024w,"
	sizes="(min-width: 800px) 1024px, (min-width: 300px) 800px, (min-width: 150px) 300px, 150px">
````

## <a name="functions"></a>Functions 
RWP provides a number of functions that can generate responsive images in your templates. 
### <a name="functions-element"></a>Element / Img
Based on an attachment ID, you can generate either a **picture** element or a **img** tag with ``srcset``/``sizes`` attributes.

````php
<?php echo rwp_img( $attachment_id ); ?>
````
 
````php
<?php echo rwp_picture( $attachment_id ); ?>
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
	echo rwp_picture( $thumbnail_id );
	?>
</header>
````

You can also specify which sizes of the image that should be used:

````php
<?php 
$thumbnail_id = get_post_thumbnail_id( $post->ID );
echo rwp_picture( $thumbnail_id, array(
	'sizes' => array( 'medium', 'large', 'full' )
) ); 
?>
````

### <a name="functions-style"></a>Style
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
echo rwp_style( $dynamic_header_image_ID, array(
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
echo rwp_style( $dynamic_header_image_ID, array(
	'selector' => '#hero',
	'sizes' => array('medium', 'large', 'full'),
	'media_queries' => array(
		'large' => 'min-width: 500px',
		'full' => 'min-width: 1024px'
	)
) );
?>
````
### <a name="functions-attributes"></a>Attributes
The ``rwp_attributes()`` function returns an array of attributes for the selected element. This might be 
useful if you for example want to create the elements later with Javascript.

````php
<?php
$img_attributes = rwp_attributes( $attachment_id, array(
	'element' => 'img'
) );

// $img_attributes equals this:
$img_attributes = array(
	'srcset' => 'thumbnail.jpg 150w, medium.jpg 300w, large.jpg 1024w',
	'sizes' => '(min-width: 300px) 1024px (min-width: 150px) 300px, 150px'
);
?>
````

It of course works with the picture element to:

```php
<?php
$picture_attributes = rwp_attributes( $attachment_id, array(
	'element' => 'picture'
) );

// $picture_attributes equals this:
$picture_attributes = array(
	'source' => array(
		array(
			'srcset' => 'large.jpg',
			'media' => '(min-width: 300px)'
		),
		array(
			'srcset' => 'medium.jpg',
			'media' => '(min-width: 150px)'
		),
	),
	'img' => array(
		'srcset' => 'thumbnail.jpg'
	)
);
?>
````

It is also possible to pass custom settings to the function.

```php
<?php
$picture_attributes = rwp_attributes( $attachment_id, array(
	'element' => 'picture',
	'sizes' => array('thumbnail', 'medium'),
	'media_queries' => array(
		'medium' => 'min-width: 1024px'
	)
) );

// $picture_attributes equals this:
$picture_attributes = array(
	'source' => array(
		array(
			'srcset' => 'medium.jpg',
			'media' => '(min-width: 1024px)'
		)
	),
	'img' => array(
		'srcset' => 'thumbnail.jpg'
	)
);
?>
````

### <a name="functions-reference"></a>Reference
RWP is providing the following functions:

- ``rwp_img( $attachment_id, $settings )``
- ``rwp_picture( $attachment_id, $settings )``
- ``rwp_span( $attachment_id, $settings )``
- ``rwp_attributes( $attachment_id, $settings )``
- ``rwp_style( $attachment_id, $settings )``
  
#### <a name="functions-reference-settings"></a>Settings
These are the settings that is currently avaliable:

* **sizes** (array): The image sizes that you want to use.
* **media_queries** (array): An associative array of names of the image sizes and a custom media query.
* **attributes** (array): An associative array of attribute names and values that you want to add on the element (see example).
* **retina** (bool|string|array): True/false. String or array of pixel densities (x descriptor).
* **ignored_image_formats** (array): An array of image formats that you want RWP to ignore.

##### <a name="functions-reference-example-sizes"></a>Example - sizes

````php
<?php
$settings = array(
	'sizes' => array('medium', 'large')
);

echo rwp_img( $attachment_id, $settings );
echo rwp_picture( $attachment_id, $settings );
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

##### <a name="functions-reference-example-custom-media-queries"></a>Example - custom media queries

````php
<?php
$settings = array(
	'sizes' => array('thumbnail', 'medium', 'large'),
	'media_queries' => array(
		'medium' => 'min-width: 500px',
		'large' => 'min-width: 1024px'
	)
);
echo rwp_picture( $attachment_id, $settings );
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

##### <a name="functions-reference-example-attributes"></a>Example - attributes

###### <a name="functions-reference-example-attributes-img"></a>img

````php
<?php
$settings = array(
	'attributes' => array(
		'id' => 'responsive-image',
		'sizes' => '(min-width: 500px) 1024px, 300px'
	)
);
echo rwp_img( $attachment_id, $settings );
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

###### <a name="functions-reference-example-attributes-picture"></a>picture

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
echo rwp_picture( $attachment_id, $settings );
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

###### <a name="functions-reference-example-attributes-span"></a>span

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
echo rwp_picture( $attachment_id, $settings );
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

## <a name="filters"></a>Filters   
### <a name="filters-edit-generated-element"></a>Edit generated element  
The ``rwp_edit_generated_element`` filter allows you to edit and modify the generated element before it's inserted back into the content.  
````php
<?php
function edit_responsive_image( $element ) {
	// Do something with $element
	return $element;
}
add_filter( 'rwp_edit_generated_element', 'edit_responsive_image' );
?>
````
One use case might be replacing the ``srcset`` attribute with ``data-srcset`` for implementing some kind of lazy load solution.  
````php
<?php
function replace_srcset_with_data_srcset( $element ) {
	$element = str_replace('srcset', 'data-srcset', $element);
	return $element;
}
add_filter( 'rwp_edit_generated_element', 'replace_srcset_with_data_srcset' );
?>
````
### <a name="filters-edit-attributes"></a>Edit attributes  
The ``rwp_edit_attributes`` filter allows you to edit the attributes of the generated element.  
````php
<?php
function edit_attributes( $attributes ) {
	// Do something with $attributes...
	return $attributes;
}
add_filter( 'rwp_edit_attributes', 'edit_attributes' );
?>
````
**Example**
````html
<!-- Original image -->
<img src="large.jpg" class="size-large" alt="My image">
````
````php
<?php
function edit_attributes( $attributes ) {
	// $attributes equals this:
	// $attributes = array(
	//   'sizes' => '(min-width: 300px) 1024px, (min-width: 150px) 300px, 150px',
	//   'class' => 'size-large',
	//   'alt' => 'My image'
	// );
	return $attributes;
}
add_filter( 'rwp_edit_attributes', 'edit_attributes' );
?>
````
This can be useful if you for example want to edit the ``sizes`` attribute in specific situations.  
The following example overrides the generated ``sizes`` attribute when the original image has the ``size-large`` class.
````php
<?php
function edit_attributes( $attributes ) {
	if ( is_integer( strpos( $attributes['class'], 'size-large') ) ) {
		$attributes['sizes'] = '(min-width: 500px) 1024px, (min-width: 300px) 300px, 150px';
	}
	return $attributes;
}
add_filter( 'rwp_edit_attributes', 'edit_attributes' );
?>
````

### <a name="filters-add-filters"></a>Add filters  
The ``rwp_add_filters`` filter allows you to add additional filters (confusing, I know) that RWP 
should be applied on.  
RWP is by default applied to the ``post_thumbnail_html`` and ``the_content`` filters. Any images found inside 
these filters will be made responsive.  

````php
<?php
function add_filters( $filters ) {
	$filters[] = 'my_custom_filter';
	return $filters; // Equals array( 'the_content', 'post_thumbnail_html', 'my_custom_filter' )
}
add_filter( 'rwp_add_filters', 'add_filters' );	
?>
````

## <a name="ignore-images"></a>Ignored images  
There might be times when you simply don't want RWP to do anything with an image. This can be achived by adding the 
`rwp-not-responsive` class to the image.

