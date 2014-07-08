<div class="wrap">
	<h2>Responsify WP</h2>
	<p>Select the image sizes that you want the plugin to use.</p>
	<form method="post" action="options.php">
		<?php
		settings_fields( 'responsify-wp-settings' );
		do_settings_sections( 'responsify-wp-settings' );
		$options = get_option( 'selected_sizes' );
		$image_sizes = get_intermediate_image_sizes();
		array_push($image_sizes, 'full');

		$html = '<ul>';
		foreach ( $image_sizes as $image_size ) {
			if ( !$options ) {
				$checked_attribute = 'checked="checked"';
			} else {
				$checked_attribute = ((isset($options[$image_size])) ? 'checked="checked"' : '');
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
	</form>
</div>