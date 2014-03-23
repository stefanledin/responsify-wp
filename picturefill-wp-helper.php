<?php
/*
Plugin Name: Picturefill WP helper
Version: 0.1-beta
Description: A helper class for creating the markup required for PictureFill (and more!)
Author: Stefan Ledin
Author URI: http://stefanledin.com
Plugin URI: https://github.com/stefanledin/picturefill-wp-helper
*/

require 'includes/picturefill.php';
require 'includes/element.php';
require 'includes/style.php';

// A factory class which creates an instance of the proper class.
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