<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$rwp_settings = array(
	'selected_sizes',
	'globally_active',
	'rwp_added_filters',
	'rwp_picturefill',
	'rwp_retina',
	'selected_element',
	'ignored_image_formats'
);
foreach ( $rwp_settings as $rwp_setting ) {
	delete_option( $rwp_setting );
}