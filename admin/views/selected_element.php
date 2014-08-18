<?php 
$selected_element = get_option( 'selected_element', 'span' );
?>
<h3><?php _e('Markup pattern'); ?></h3>
<p>
	<?php _e('By default, RWP will use <a href="https://github.com/scottjehl/picturefill/blob/1.2.1/README.md">Picturefill 1.2.1</a> and the markup pattern that uses <b>span</b> elements.'); ?>
	<br>
	<?php _e('If you rather want to use the new <b>picture</b> element (<a href="http://scottjehl.github.io/picturefill/">Picturefill 2.1.0</a>), simply select it below.') ?>
</p>
<label>
	<?php _e('span'); ?>
	<input name="selected_element" type="radio" value="span" <?php echo ( $selected_element == 'span' ) ? 'checked="checked"' : ''; ?>>
</label>
<label>
	<?php _e('picture'); ?>
	<input name="selected_element" type="radio" value="picture" <?php echo ( $selected_element == 'picture' ) ? 'checked="checked"' : ''; ?>>
</label>
