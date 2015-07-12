<a name="picturefill"></a>
<div class="postbox">
	<div class="inside">
		<?php
		$active = get_option( 'rwp_picturefill', 'on' );
		?>
		<h3><?php _e('Picturefill'); ?></h3>
		<p>
			<?php _e('RWP generates markup for the <a href="http://scottjehl.github.io/picturefill">Picturefill</a> polyfill by default. If you choose to turn Picturefill off, the responsive images will only work in very modern browsers. This is not recommended.'); ?>
		</p>
		<label>
			<?php _e('On (recommended)'); ?>
			<input name="rwp_picturefill" type="radio" value="on" <?php echo ( $active == 'on' ) ? 'checked="checked"' : ''; ?>>
		</label>
		<label>
			<?php _e('Off (native mode)'); ?>
			<input name="rwp_picturefill" type="radio" value="off" <?php echo (( $active == 'off' ) ? 'checked="checked"' : ''); ?>>
		</label>
		<?php submit_button( 'Save' ); ?>
	</div>
</div>