<?php
function rwp_picture( $id, $settings = null ) {
	$native_mode = ( get_option( 'rwp_picturefill', 'on' ) == 'off' ) ? true : false;
	if ( get_option('selected_element') == 'span' ) {
        $responsive_image = new Span( $id, $settings );
    } else {
        if ( $native_mode ) {
            $responsive_image = new Native_Element( $id, $settings );
        } else {
            $responsive_image = new Element( $id, $settings );
        }
    }
    if ( isset($settings['output']) && $settings['output'] == 'attributes' ) {
        return $responsive_image->attributes;
    }
    return $responsive_image->markup;
}