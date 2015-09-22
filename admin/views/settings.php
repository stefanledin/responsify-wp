<div class="wrap">
	<h2>Responsify WP</h2>
	<p>
		<a href="#use-on">Use on</a> | 
		<a href="#markup-pattern">Markup pattern</a> | 
		<a href="#image-sizes">Image sizes</a> | 
		<a href="#retina">Retina</a> | 
		<a href="#custom-media-queries">Custom media queries</a> | 
		<a href="#ignored-image-formats">Ignored image formats</a> | 
		<a href="#picturefill">Picturefill</a> |
		<a href="#debug-mode">Debug mode</a>
	</p>
	<form method="post" action="options.php">
		<script type="text/javascript">
			window.rwp = window.rwp || {};
			rwp.image_sizes = <?php echo json_encode(get_intermediate_image_sizes());?>;
			rwp.image_sizes.push('full');
			rwp.customMediaQueries = <?php echo json_encode(get_option('rwp_custom_media_queries'));?>;
		</script>
		<?php 
		settings_fields( 'responsify-wp-settings' );
		do_settings_sections( 'responsify-wp-settings' );
		
		include 'content_filter.php';
		
		include 'selected_element.php';

		include 'selected_sizes.php';
		
		include 'retina.php';
		
		include 'media_queries.php';

		include 'ignored_image_formats.php';
		
		include 'picturefill.php';
		
		include 'debug_mode.php';
		?>
		<div class="postbox">
			<div class="inside">
				<h3>Thank you!</h3>
				<p>Thanks for using Responsify WP. If you find RWP useful, please consider leaving a <a href="https://wordpress.org/support/view/plugin-reviews/responsify-wp" target="_blank">review at wordpress.org</a>.<br>
				You can also show your support by writing a tweet, a blog post or by simply telling your friends about RWP.</p>
				<p>If something is not working as expected or if you need any help, please contact me in the <a href="https://wordpress.org/support/plugin/responsify-wp">support forums</a>.</p>
				<p>Thanks for caring about responsive images!</p>
			</div>
		</div>
	</form>
</div>