<?php
/*
Plugin Name: Picturefill
Version: 0.1-alpha
Description: A helper class for creating the markup required for PictureFill (and more!)
Author: Stefan Ledin
Author URI: http://stefanledin.com
Plugin URI: http://github.com/stefanledin/picturefill
*/

class Picturefill
{
	protected $imageSizes = array();
	protected $id;
	protected $images;

	public function __construct($id, $settings)
	{
		$this->id = $id;
		// 1. Hämta alla förinställda bildstorlekar
		$this->getImageSizes();
		// 2. Hämta bilderna i antingen de valda storlekarna eller alla förinställda.
		$sizes = (isset($settings['sizes'])) ? $settings['sizes'] : $this->imageSizes;
		$this->images = $this->getImages($sizes);
		// 3. Sortera bilderna i storleksordning
		$this->orderImages();
	}
	public function getImages($sizes)
	{
		$images = array();
		foreach ($sizes as $size) {
			array_push($images, $this->getImage($size));
		}
		return $images;
	}

	protected function getImage($size)
	{
		return wp_get_attachment_image_src($this->id, $size);
	}

	protected function orderImages()
	{
		usort($this->images, function($img1, $img2) {
			return $img1[1] < $img2[1] ? -1 : 1;
		});
	}
	
	protected function getImageSizes()
	{
		$this->imageSizes = get_intermediate_image_sizes();
		array_push($this->imageSizes, 'full');
	}
}

class Element extends Picturefill
{
	public $markup;

	public function __construct($id, $settings)
	{
		parent::__construct($id, $settings);
		$this->markup = $this->buildMarkup();
	}

	protected function buildMarkup()
	{
		$alt = $this->getImageAttributes();
		$markup = '<span data-picture data-alt="'.$alt.'">';
			$markup .= '<span data-src="'.$this->images[0][0].'"></span>';
			for ($i=1; $i < count($this->images); $i++) { 
				$markup .= '<span data-src="'.$this->images[$i][0].'" data-media="(min-width: '.$this->images[$i-1][1].'px)"></span>';
			}
			$markup .= '<noscript>';
				$markup .= '<img src="'.$this->images[0][0].'" alt="'.$alt.'">';
			$markup .= '</noscript>';
		$markup .= '</span>';
		return $markup;
	}

	protected function getImageAttributes()
	{
		return get_post_meta($this->id, '_wp_attachment_image_alt', true);
	}
}

class Style extends Picturefill
{
	public $style;

	public function __construct($id, $settings)
	{
		parent::__construct($id, $settings);
		$this->style = $this->buildCss($settings['selector']);
	}

	protected function buildCss($selector)
	{
		$css = '<style>';
			$css .= $selector . ' {';
				$css .= 'background-image: url("'.$this->images[0][0].'");';
			$css .= '}';
			for ($i=1; $i < count($this->images); $i++) { 
				$css .= '@media screen and (min-width: '.$this->images[$i-1][1].'px) {';
					$css .= $selector . '{';
						$css .= 'background-image: url("'.$this->images[$i][0].'");';
					$css .= '}';
				$css .= '}';
			}
		$css .= '</style>';
		return $css;
	}
}

class Picture
{
	public static function create($type, $id, $settings = null)
	{
		switch (strtolower($type)) {
			case 'element':
				$picture = new Element($id, $settings);
				echo $picture->markup;
				break;
			case 'style':
				$picture = new Style($id, $settings);
				echo $picture->style;
				break;
		}
	}
}