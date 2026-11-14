<?php

declare(strict_types=1);
/**
 * This file adds the leadership page template to the UASys Theme.
 *
 * @author Brent Passmore
 */

/*
Template Name: Leadership Page
*/

add_action('genesis_meta', 'mpp_leadership_page');
/**
 * Add widget support for homepage. If no widgets active, display the default loop.
 */
function mpp_leadership_page()
{

    if (is_active_sidebar('page-leadership')) {

        // Force content-sidebar layout setting
        add_filter('genesis_pre_get_option_site_layout', '__genesis_return_full_width_content');

        // Add mpp-home body class
        add_filter('body_class', 'mpp_body_class');

        // Remove the default Genesis loop
        remove_action('genesis_loop', 'genesis_do_loop');

        // Add homepage widgets
        add_action('genesis_loop', 'mpp_leadership_page_widget');

    }

}

function mpp_body_class($classes)
{

    $classes[] = 'mpp-home';

    return $classes;

}

function mpp_leadership_page_widget()
{

    genesis_widget_area('page-leadership', [
        'before' => '<div id="leadership"><div class="wrap">',
        'after' => '</div></div>',
    ]);

}

genesis();
