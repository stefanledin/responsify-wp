<a name="ignored-image-formats"></a>
<div class="postbox">
	<div class="inside">
		<h3><?php _e('Ignored image formats'); ?></h3>
		<p><?php _e('RWP should ignore images of the follwing file formats:');?></p>
		<?php
		$file_formats = array( 'jpg', 'png', 'gif' );
		$ignored_image_formats = get_option( 'ignored_image_formats' );
		$html = '<ul>';
			foreach ( $file_formats as $format ) {
				$checked_attribute = (isset($ignored_image_formats[$format])) ? 'checked="checked"' : '';
				$html .= '<li>';
					$html .= '<label>';
						$html .= '<input '.$checked_attribute.' name="ignored_image_formats['.$format.']" type="checkbox">'.$format;
					$html .= '</label>';
				$html .= '</li>';
			}
		$html .= '</ul>';
		echo $html;
		submit_button( 'Save' );
		?>
	</div>
</div>