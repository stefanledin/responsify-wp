<?php 
$selected_element = get_option( 'selected_element', 'span' );
?>
<h3><?php _e('Select element'); ?></h3>
<p><?php _e('Should RWP use span och picture?'); ?></p>
<label>
	<?php _e('span'); ?>
	<input name="selected_element" type="radio" value="span" <?php echo ( $selected_element == 'span' ) ? 'checked="checked"' : ''; ?>>
</label>
<label>
	<?php _e('picture'); ?>
	<input name="selected_element" type="radio" value="picture" <?php echo ( $selected_element == 'picture' ) ? 'checked="checked"' : ''; ?>>
</label>
<?php submit_button( 'Save' ); ?>