<a name="use-on"></a>
<div class="postbox">
	<div class="inside">
		<h3><?php _e('Use on:'); ?></h3>
		<?php
		$default_filter_labels = array(
			'the_content' => 'Content',
			'post_thumbnail_html' => 'Thumbnails'
		);
		$default_filters = array( 'the_content' => 'on', 'post_thumbnail_html' => 'on' );
		$selected_filters = get_option( 'rwp_added_filters', $default_filters );

		$html = '<ul>';

			foreach ( $default_filters as $filter => $status ) {
				$checked_attribute = ( isset($selected_filters[$filter]) ) ? 'checked="checked"' : '';
				$html .= '<li>';
					$html .= '<label>';
						$html .= '<input '.$checked_attribute.' name="rwp_added_filters['.$filter.']" type="checkbox">';
						$html .= $default_filter_labels[$filter];
					$html .= '</label>';
				$html .= '</li>';
			}
			
			$custom_filters = array();
			if ( has_filter( 'rwp_add_filters' ) ) {
				$custom_filters = apply_filters( 'rwp_add_filters', $custom_filters );
			}
			if ( $custom_filters ) {
				foreach ($custom_filters as $custom_filter) {
					$html .= '<li>';
						$html .= '<label>';
							$html .= '<input checked="checked" disabled="disabled" type="checkbox">'.$custom_filter;
						$html .= '</label>';
					$html .= '</li>';
				}
			}

		$html .= '</ul>';
		echo $html;
		?>
		<p><?php _e('You can add more filters that RWP should be applied to using the <code><a href="https://github.com/stefanledin/responsify-wp#filters" target="_blank">rwp_add_filters</a></code> filter.');?></p>
		<?php submit_button( 'Save' ); ?>
	</div>
</div>