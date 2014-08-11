<div class="wrap">
	<h2>Responsify WP</h2>
	<form method="post" action="options.php">
		<?php 
		settings_fields( 'responsify-wp-settings' );
		do_settings_sections( 'responsify-wp-settings' );
		$active = get_option( 'globally_active' );
		if ( $active == '' ) {
			$active = 'off';
		}
		?>
		<h3><?php _e('Globally active'); ?></h3>
		<p><?php _e('Lorem ipsum dolor sit amet'); ?></p>
		<label>
			<?php _e('On'); ?>
			<input name="globally_active" type="radio" value="on" <?php echo ( $active == 'on' ) ? 'checked="checked"' : ''; ?>>
		</label>
		<label>
			<?php _e('Off'); ?>
			<input name="globally_active" type="radio" value="off" <?php echo ( $active == 'off' ) ? 'checked="checked"' : ''; ?>>
		</label>
		<?php submit_button( 'Save' ); ?>
		
		<h3><?php _e('Select element'); ?></h3>
		<p><?php _e('Should RWP use span och picture?'); ?></p>
		<label>
			<?php _e('span'); ?>
			<input name="globally_active" type="radio" value="on" <?php echo ( $active == 'on' ) ? 'checked="checked"' : ''; ?>>
		</label>
		<label>
			<?php _e('picture'); ?>
			<input name="globally_active" type="radio" value="off" <?php echo ( $active == 'off' ) ? 'checked="checked"' : ''; ?>>
		</label>
		<?php submit_button( 'Save' ); ?>

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
	</form>
</div>