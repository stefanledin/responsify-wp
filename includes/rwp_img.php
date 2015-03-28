<?php
function rwp_img( $id, $settings = null ) {
	$native_mode = ( get_option( 'rwp_picturefill', 'on' ) == 'off' ) ? true : false;
	if ( $native_mode ) {
        $responsive_image = new Native_Img( $id, $settings );
    } else {
        $responsive_image = new Img( $id, $settings );
    }
    if ( isset($settings['output']) && $settings['output'] == 'attributes' ) {
        return $responsive_image->attributes;
    }
    return $responsive_image->markup;
}