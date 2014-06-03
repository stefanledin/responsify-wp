<?php
// A factory class which creates an instance of the proper class.
class Picture
{
	public static function create ( $type, $id, $settings = null )
	{
		switch (strtolower($type)) {
			case 'element':
				$picture = new Element( $id, $settings );
				return $picture->markup;
				break;
			case 'style':
				$picture = new Style( $id, $settings );
				return $picture->style;
				break;
		}
	}
}