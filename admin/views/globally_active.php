<?php
$active = get_option( 'globally_active', 'off' );
?>
<h3><?php _e('Globally active'); ?></h3>
<p><?php _e('Lorem ipsum dolor sit amet'); ?></p>
<label>
	<?php _e('On'); ?>
	<input name="globally_active" type="radio" value="on" <?php echo ( $active == 'on' ) ? 'checked="checked"' : ''; ?>>
</label>
<label>
	<?php _e('Off'); ?>
	<input name="globally_active" type="radio" value="off" <?php echo (( $active == 'off' ) ? 'checked="checked"' : ''); ?>>
</label>
<?php submit_button( 'Save' ); ?>