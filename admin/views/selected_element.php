<?php 
$selected_element = get_option( 'selected_element', 'span' );
?>
<h3><?php _e('Markup pattern'); ?></h3>
<p>
	<?php _e('By default, RWP will use <a href="https://github.com/scottjehl/picturefill/blob/1.2.1/README.md">Picturefill 1.2.1</a> and the markup pattern that uses <b>span</b> elements.'); ?>
	<br>
	<?php _e('If you rather want to use the new <b>picture</b> element (<a href="http://scottjehl.github.io/picturefill/">Picturefill 2.1.0</a>), simply select it below.') ?>
</p>
<div class="input-group">
	<label>
		<?php _e('span'); ?>
		<input name="selected_element" type="radio" value="span" <?php echo ( $selected_element == 'span' ) ? 'checked="checked"' : ''; ?>>
	</label>
	<label>
		<?php _e('picture'); ?>
		<input name="selected_element" class="js-has-message" data-message="picture-snippet" type="radio" value="picture" <?php echo ( $selected_element == 'picture' ) ? 'checked="checked"' : ''; ?>>
	</label>
</div>
<div class="option-message" id="picture-snippet" <?php echo ( $selected_element == 'picture' ) ? 'style="display: block;"' : ''; ?>>
	<?php _e('Notice that this snippet is required for the <b>picture</b> element to work in all browsers:') ?>
	<br><br>
	<code>
		&ltscript&gt
		<br>
		    &nbsp;&nbsp;&nbsp;&nbsp;document.createElement( "picture" );
		    <br>
		&lt/script&gt
	</code>
	<br><br>
	<small><?php _e('Note that if you are already including a recent version of the HTML5 Shiv (sometimes packaged with Modernizr), you may not need this line as it is included there as well.'); ?></small>
</div>
