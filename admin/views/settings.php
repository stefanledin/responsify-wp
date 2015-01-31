<div class="wrap">
	<h2>Responsify WP</h2>
	<form method="post" action="options.php">
		<?php 
		settings_fields( 'responsify-wp-settings' );
		do_settings_sections( 'responsify-wp-settings' );
		
		include 'content_filter.php';
		
		#include 'globally_active.php';
		
		include 'selected_element.php';

		include 'selected_sizes.php';
		
		include 'retina.php';

		include 'ignored_image_formats.php';
		
		include 'picturefill.php';
		?>
	</form>
</div>