<a name="retina"></a>
<div class="postbox">
	<div class="inside">
		<?php
		$active = get_option( 'rwp_retina', 'off' );
		?>
		<h3><?php _e('Retina'); ?></h3>
		<p><?php _e('Turn on this option if you want to deliver high resolution images to devices with high pixel density (retina).', 'rwp');?></p>
		<p><code>
		    &lt;picture&gt;<br>
		    &nbsp;&nbsp;&nbsp;&nbsp;&lt;source srcset=&quot;large.jpg, large_retina.jpg 2x&quot; media=&quot;(min-width: 300px)&quot;&gt;<br>
		    &nbsp;&nbsp;&nbsp;&nbsp;&lt;source srcset=&quot;medium.jpg, medium_retina.jpg 2x&quot; media=&quot;(min-width: 150px)&quot;&gt;<br>
		    &nbsp;&nbsp;&nbsp;&nbsp;&lt;img srcset=&quot;thumbnail.jpg, thumbnail_retina.jpg 2x&quot;&gt;<br>
		    &lt;/picture&gt;
		</code></p>

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
				<p><?php _e('RWP will look for and use image sizes with the <code>@2x</code> suffix in their names as retina images. For example, <code>thumbnail@2x</code> will be selected as a retina version of <code>thumbnail</code>', 'rwp');?>.</p>
				<p><?php _e('You\'ll need to add these image sizes manually by adding something like the following snippet to your <code>functions.php</code> file:', 'rwp');?></p>
				<?php
				$image_sizes = get_intermediate_image_sizes();
				$html = '<ul>';
				foreach ( $image_sizes as $image_size ) {
					if ( ! strpos($image_size, '@') ) {
						$html .= '<li><code>add_image_size( ' . $image_size . '@2x, $width, $height )</code></li>';
					}
				}
				$html .= '</ul>';
				echo $html;
				?>
				<p><a href="http://codex.wordpress.org/Function_Reference/add_image_size" target="_blank"><?php _e('Read more about the add_image_size() function.', 'rwp');?></a></p>
				<p><?php _e('You can of course add sizes for other pixel densities than <code>2x</code> to. <code>1.5x</code>, <code>3x</code> or whatever you want will work just fine.', 'rwp');?></p>
			</div>

		</div>
		<?php submit_button( 'Save' ); ?>
	</div>
</div>