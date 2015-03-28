<?php
function rwp_style( $id, $settings = null ) {
	$responsive_image = new Style( $id, $settings );
    return $responsive_image->style;
}