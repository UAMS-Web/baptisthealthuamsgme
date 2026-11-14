<?php

declare(strict_types=1);
/**
 * Template Name: News
 *
 * This file adds a Landing page template to Utility Pro.
 *
 * @author Brent Passmore
 * @license GPL-2.0+
 */
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'prefix_do_loop');
/**
 * Outputs a custom loop
 *
 * @global mixed $paged current page number if paginated
 *
 * @return void
 */
function prefix_do_loop()
{

    global $paged;

    // accepts any wp_query args
    $args = ([
        'post_type' => '',
        'category_name' => 'news', // use category slug
        'order' => 'DSC',
        'orderby' => 'date',
        'paged' => $paged,
        'posts_per_page' => 5,
    ]);

    genesis_custom_loop($args);
}
genesis();
