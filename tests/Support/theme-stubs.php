<?php

/**
 * Plain stand-ins for what the theme's load-time code calls and the Unit suite has no WordPress to supply.
 *
 * They are ordinary functions and classes, not Brain Monkey stubs, so a test may still mock any of them
 * with Brain Monkey after the loader has run. The ones that record keep their calls in `$GLOBALS`.
 */

declare(strict_types=1);

if (! function_exists('is_admin')) {
    function is_admin(): bool
    {
        return true;
    }
}

if (! function_exists('add_theme_support')) {
    function add_theme_support(string $feature, mixed ...$args): void
    {
        $GLOBALS['theme_calls']['supports'][] = [$feature, ...$args];
    }
}

if (! function_exists('genesis_register_sidebar')) {
    /**
     * @param  array<string, mixed>  $sidebar
     */
    function genesis_register_sidebar(array $sidebar): void
    {
        $GLOBALS['theme_calls']['sidebars'][] = $sidebar;
    }
}

if (! function_exists('add_image_size')) {
    function add_image_size(string $name, int $width, int $height, bool $crop): void
    {
        $GLOBALS['theme_calls']['sizes'][] = [$name, $width, $height, $crop];
    }
}

if (! function_exists('unregister_sidebar')) {
    function unregister_sidebar(string $id): void
    {
        $GLOBALS['theme_calls']['unregistered_sidebars'][] = $id;
    }
}

if (! function_exists('genesis_unregister_layout')) {
    function genesis_unregister_layout(string $layout): void
    {
        $GLOBALS['theme_calls']['layouts'][] = $layout;
    }
}

if (! function_exists('tgmpa')) {
    /**
     * @param  array<int, array<string, mixed>>  $plugins
     * @param  array<string, mixed>  $config
     */
    function tgmpa(array $plugins, array $config = []): void
    {
        $GLOBALS['tgmpa_calls'][] = [$plugins, $config];
    }
}

if (! function_exists('wp_parse_args')) {
    /**
     * @param  array<string, mixed>  $args
     * @param  array<string, mixed>  $defaults
     * @return array<string, mixed>
     */
    function wp_parse_args(array $args, array $defaults = []): array
    {
        return array_merge($defaults, $args);
    }
}

if (! function_exists('get_template')) {
    function get_template(): string
    {
        return 'utility-pro';
    }
}

if (! function_exists('sanitize_key')) {
    function sanitize_key(string $key): string
    {
        return strtolower($key);
    }
}

// The vendored plugin installer declares its class and helper behind guards, and requires WordPress' list table outside its own.
if (! class_exists('TGM_Plugin_Activation')) {
    class TGM_Plugin_Activation {}
}

if (! class_exists('WP_List_Table')) {
    class WP_List_Table {}
}
