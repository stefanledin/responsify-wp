<?php
/**
 * A factory class which creates an instance of the proper class.
 */
class Picture
{
    /**
     * Returns markup.
     *
     * @param $type
     * @param $id
     * @param null $settings
     * @return string
     */
    public static function create ( $type, $id, $settings = null )
	{
		switch (strtolower($type)) {
            case 'span':
                $span = new Span( $id, $settings );
                return $span->markup;
                break;
            case 'img':
                $img = new Img( $id, $settings );
                return $img->markup;
                break;
			case 'element':
			case 'picture':
                if ( get_option('selected_element') == 'span' ) {
                    $picture = new Span( $id, $settings );
                } else {
                    $picture = new Element( $id, $settings );
                }
				return $picture->markup;
				break;
			case 'style':
				$picture = new Style( $id, $settings );
				return $picture->style;
				break;
		}
	}
}