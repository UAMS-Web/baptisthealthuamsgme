<?php
/**
 * Template Name: News
 *
 * This file adds a Landing page template to Utility Pro.
 *
 * @package Utility_Pro
 * @author Brent Passmore
 * @subpackage Customizations
 * @license GPL-2.0+
 */


remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'prefix_do_loop' );
/**
 * Outputs a custom loop
 *
 * @global mixed $paged current page number if paginated
 * @return void
 */
function prefix_do_loop() {

	global $paged;

	// accepts any wp_query args
	$args = (array(
		'post_type'      => '',
		'category_name'  => 'news', // use category slug
		'order'          => 'DSC',
		'orderby'       => 'date',
	 	'paged'          => $paged,
	 	'posts_per_page' => 5
	));

	genesis_custom_loop( $args );
}
genesis();