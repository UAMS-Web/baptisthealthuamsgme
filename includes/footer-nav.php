<?php

declare(strict_types=1);
/**
 * Utility Pro.
 *
 * @link         http://www.carriedils.com/utility-pro
 *
 * @author       Carrie Dils
 * @copyright    Copyright (c) 2015, Carrie Dils
 * @license      GPL-2.0+
 */

/**
 * Output footer navigation menu.
 *
 * @since  1.0.0
 */
function utility_pro_do_footer_nav(): void
{

    genesis_nav_menu(
        [
            'menu_class' => 'menu genesis-nav-menu menu-footer',
            'theme_location' => 'footer',
        ]
    );
}

add_filter('genesis_attr_nav-footer', 'utility_pro_footer_nav_schema');
/**
 * Add schema markup to the footer navigation menu.
 *
 * Replaces the Genesis `genesis_attributes_nav()` callback, which Genesis deprecated in 3.1.0 and which raises a
 * deprecation notice whenever the footer menu renders.
 *
 * @since  1.3.3
 *
 * @param  array<string, mixed>  $attributes  Existing footer navigation attributes.
 * @return array<string, mixed> Attributes with the schema.org navigation microdata added.
 */
function utility_pro_footer_nav_schema($attributes)
{
    $attributes['itemscope'] = true;
    $attributes['itemtype'] = 'https://schema.org/SiteNavigationElement';

    return $attributes;
}

add_filter('wp_nav_menu_args', 'utility_pro_footer_menu_args');
/**
 * Reduce the footer navigation menu to one level depth.
 *
 * @since  1.0.0
 *
 * @param  array<string, mixed>  $args  Existing footer menu args.
 * @return array<string, mixed> Amended footer menu args.
 */
function utility_pro_footer_menu_args($args)
{

    if ($args['theme_location'] !== 'footer') {
        return $args;
    }

    $args['depth'] = 1;

    return $args;
}
