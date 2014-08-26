<?php
$active = get_option( 'globally_active', 'on' );
?>
<h3><?php _e('Globally active'); ?></h3>
<p>
	<?php _e('If activated, RWP will find all <b>img</b> tags that <b>the_content</b> outputs. These will be replaced with the markup required by <a href="http://scottjehl.github.io/picturefill">Picturefill</a>.'); ?>
	<br>
	<?php _e('You can still use the <b>Picture::create()</b> function in your templates if you choose to not activate RWP globally.') ?>
</p>
<label>
	<?php _e('On'); ?>
	<input name="globally_active" type="radio" value="on" <?php echo ( $active == 'on' ) ? 'checked="checked"' : ''; ?>>
</label>
<label>
	<?php _e('Off'); ?>
	<input name="globally_active" type="radio" value="off" <?php echo (( $active == 'off' ) ? 'checked="checked"' : ''); ?>>
</label>