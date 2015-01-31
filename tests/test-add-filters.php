<?php
class Test_Add_Filters extends WP_UnitTestCase {
	
	function setUp() {
		add_filter( 'rwp_add_filters', array( $this, 'add_thumbnail_filter' ) );
	}

	function add_thumbnail_filter( $filters ) {
		$filters[] = 'post_thumbnail_html';
		return $filters;
	}

	function test_filter_adds_filter() {
		delete_option( 'rwp_added_filters' );
		$filters = get_option( 'rwp_added_filters', array('the_content') );
		if ( has_filter( 'rwp_add_filters' ) ) {
			$filters = apply_filters( 'rwp_add_filters', $filters );
		}
		$expected = array( 'the_content', 'post_thumbnail_html' );
		$this->assertEquals($expected, $filters);
	}

}