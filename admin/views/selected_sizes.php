<h3><?php _e('Image sizes'); ?></h3>
<p><?php _e('Select the image sizes that you want the plugin to use.');?></p>
<?php
$selected_sizes = get_option( 'selected_sizes' );
$image_sizes = get_intermediate_image_sizes();
array_push($image_sizes, 'full');

$html = '<ul>';
foreach ( $image_sizes as $image_size ) {
	if ( !$selected_sizes ) {
		$checked_attribute = 'checked="checked"';
	} else {
		$checked_attribute = ((isset($selected_sizes[$image_size])) ? 'checked="checked"' : '');
	}
	$html .= '<li>';
		$html .= '<label>';
			$html .= '<input name="selected_sizes['.$image_size.']" '.$checked_attribute.' type="checkbox">'.$image_size;
		$html .= '</label>';
	$html .= '</li>';
}
$html .= '</ul>';
echo $html;

submit_button( 'Save' );
?>