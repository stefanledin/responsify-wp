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
    $image_dimention['width'] = get_option( $image_size . '_size_w' );
    $image_dimention['height'] = get_option( $image_size . '_size_h' );
    $image_dimention['print'] = ($image_dimention['width']) ? '('.$image_dimention['width'].' x '.$image_dimention['height'].' px)' : '';

	$html .= '<li>';
		$html .= '<label>';
            $html .= '<input name="selected_sizes['.$image_size.']" '.$checked_attribute.' type="checkbox">'.$image_size . ' ' . $image_dimention['print'];
		$html .= '</label>';
	$html .= '</li>';
}
$html .= '</ul>';
echo $html;
?>