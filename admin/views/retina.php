<?php
$active = get_option( 'rwp_retina', 'off' );
?>
<h3><?php _e('Retina (beta)'); ?></h3>
<div class="input-group">
	<label>
		<?php _e('On'); ?>
		<input name="rwp_retina" type="radio" value="on" class="js-has-message" data-message="retina-sizes" <?php echo ( $active == 'on' ) ? 'checked="checked"' : ''; ?>>
	</label>
	<label>
		<?php _e('Off'); ?>
		<input name="rwp_retina" type="radio" value="off" <?php echo (( $active == 'off' ) ? 'checked="checked"' : ''); ?>>
	</label>
	<div class="option-message" id="retina-sizes" <?php echo ( $active == 'on' ) ? 'style="display: block"' : ''; ?>>
		<?php
		_e('Add image sizes', 'rwp');
		$image_sizes = get_intermediate_image_sizes();
		foreach ($image_sizes as $image_size) {
			if ( ! strpos($image_size, '@') ) {
				echo '<li>' . $image_size;
			}
		}
		?>

	</div>
</div>