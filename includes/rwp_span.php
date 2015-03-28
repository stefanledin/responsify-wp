<?php
function rwp_span( $id, $settings = null ) {
    $responsive_image = new Span( $id, $settings );
	if ( isset($settings['output']) && $settings['output'] == 'attributes' ) {
        return $responsive_image->attributes;
    }
    return $responsive_image->markup;
}