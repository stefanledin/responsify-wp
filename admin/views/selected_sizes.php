<a name="image-sizes"></a>
<div class="postbox">
	<div class="inside">
		<h3><?php _e('Image sizes'); ?></h3>
		<p><?php _e('Select the image sizes that you want the plugin to use.');?></p>
		<?php
		global $_wp_additional_image_sizes;
		$selected_sizes = get_option( 'selected_sizes' );
		$image_sizes = get_intermediate_image_sizes();
		array_push( $image_sizes, 'full' );
		
		$html = '<ul>';
		foreach ( $image_sizes as $image_size ) {
			if ( !$selected_sizes ) {
				$checked_attribute = 'checked="checked"';
			} else {
				$checked_attribute = ((isset($selected_sizes[$image_size])) ? 'checked="checked"' : '');
			}
		    $image_dimention['width'] = get_option( $image_size . '_size_w' );
		    if ( isset( $image_dimention['width'] ) ) {
				$image_dimention['height'] = get_option( $image_size . '_size_h' );

				// Fix for the missing "full" size in $_wp_additional_image_sizes
		    } elseif ( isset( $_wp_additional_image_sizes[$image_size] ) ) {
		    	$image_dimention['width'] = $_wp_additional_image_sizes[$image_size]['width'];
		    	$image_dimention['height'] = $_wp_additional_image_sizes[$image_size]['height'];
		    }
			$image_dimention['print'] = ($image_dimention['width']) ? '('.$image_dimention['width'].' x '.$image_dimention['height'].' px)' : '';

			$html .= '<li>';
				$html .= '<label>';
		            $html .= '<input name="selected_sizes['.$image_size.']" '.$checked_attribute.' type="checkbox">'.$image_size . ' ' . $image_dimention['print'];
				$html .= '</label>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		echo $html;
		submit_button( 'Save' );
		?>
	</div>
</div>