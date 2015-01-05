<h3><?php _e('Apply on filters'); ?></h3>
<p><?php _e('Lorem ipsum dolor sit amet');?></p>
<?php
$default_filters_labels = array( 'the_content' => 'Content', 'post_thumbnail_html' => 'Thumbnails' );
$default_filters = array( 'the_content' => 'on', 'post_thumbnail_html' => 'on' );
#delete_option( 'rwp_added_filters' );die();
$filters = get_option( 'rwp_added_filters', $default_filters );
#die(var_dump($filters));
/*if ( has_filter( 'rwp_add_filters' ) ) {
	$filters = apply_filters( 'rwp_add_filters', $filters );
}*/
$filters[] = array('plask' => 'off');
$html = '<ul>';
	for ($i=0; $i < count($filters); $i++) { 
		$checked_attribute = ( $filters[$i] == 'on' ) ? 'checked="checked"' : '';
		var_dump($filters[$i]);
	}
	/*foreach ( $filters as $filter => $status ) {
		$html .= '<li>';
			$html .= '<label>';
				$html .= '<input '.$checked_attribute.' name="rwp_added_filters[]['.$filter.']" value="on" type="checkbox">'.$filter;
			$html .= '</label>';
		$html .= '</li>';
	}*/
$html .= '</ul>';
echo $html;