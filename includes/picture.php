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
        $native_mode = ( get_option( 'rwp_picturefill', 'on' ) == 'off' ) ? true : false;

		switch ( strtolower($type) ) {
            case 'attributes':
                $default_settings = array( 'output' => 'attributes' );
                $settings = ( $settings ) ? array_merge($settings, $default_settings) : $default_settings;
                $responsive_image = self::create( $settings['element'], $id, $settings );
                return $responsive_image;
                break;

            case 'span':
                $responsive_image = new Span( $id, $settings );
                break;
            case 'img':
                if ( $native_mode ) {
                    $responsive_image = new Native_Img( $id, $settings );
                } else {
                    $responsive_image = new Img( $id, $settings );
                }
                break;
			case 'element':
			case 'picture':
                if ( get_option('selected_element') == 'span' ) {
                    $responsive_image = new Span( $id, $settings );
                } else {
                    if ( $native_mode ) {
                        $responsive_image = new Native_Element( $id, $settings );
                    } else {
                        $responsive_image = new Element( $id, $settings );
                    }
                }
				break;
			case 'style':
				$responsive_image = new Style( $id, $settings );
				return $responsive_image->style;
				break;
		}
        
        if ( isset($settings['output']) && $settings['output'] == 'attributes' ) {
            return $responsive_image->attributes;
        }
        
        $element = $responsive_image->markup;
        if ( has_filter( 'rwp_edit_generated_element' ) ) {
            $element = apply_filters( 'rwp_edit_generated_element', $element );
        }
        return $element;
	}
}