<a name="debug-mode"></a>
<div class="postbox">
	<div class="inside">
		<?php
		$active = get_option( 'rwp_debug_mode', 'off' );
		?>
		<h3><?php _e('Debug mode'); ?></h3>
		<p>
			<?php _e('Add HTML comments with handy information about the image and what RWP could or could not do.'); ?>
		</p>
		<label>
			<?php _e('On'); ?>
			<input name="rwp_debug_mode" type="radio" value="on" <?php echo ( $active == 'on' ) ? 'checked="checked"' : ''; ?>>
		</label>
		<label>
			<?php _e('Off'); ?>
			<input name="rwp_debug_mode" type="radio" value="off" <?php echo (( $active == 'off' ) ? 'checked="checked"' : ''); ?>>
		</label>
		<?php submit_button( 'Save' ); ?>
	</div>
</div>