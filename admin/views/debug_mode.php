<a name="debug-mode"></a>
<div class="postbox">
	<div class="inside">
		<?php
		$active = get_option( 'rwp_debug_mode', 'off' );
		?>
		<h3><?php _e('Debug mode'); ?></h3>
		<p>
			<?php _e('Add HTML comments with handy information about what RWP has done with the image.'); ?>
		</p>
		<label>
			<?php _e('On (recommended)'); ?>
			<input name="rwp_debug_mode" type="radio" value="on" <?php echo ( $active == 'on' ) ? 'checked="checked"' : ''; ?>>
		</label>
		<label>
			<?php _e('Off (native mode)'); ?>
			<input name="rwp_debug_mode" type="radio" value="off" <?php echo (( $active == 'off' ) ? 'checked="checked"' : ''); ?>>
		</label>
		<?php submit_button( 'Save' ); ?>
	</div>
</div>