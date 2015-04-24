<?php
function rwp_img( $id, $settings = null ) {
	return Picture::create( 'img', $id, $settings );
}

function rwp_picture( $id, $settings = null ) {
	return Picture::create( 'element', $id, $settings );
}

function rwp_style( $id, $settings = null ) {
	return Picture::create( 'style', $id, $settings );
}

function rwp_attributes( $id, $settings = null ) {
	return Picture::create( 'attributes', $id, $settings );
}