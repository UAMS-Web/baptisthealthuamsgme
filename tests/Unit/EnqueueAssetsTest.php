<?php

/**
 * Unit tests for utility_pro_enqueue_assets() in includes/enqueue-assets.php.
 *
 * Mutation (macOS, working evidence only): 44 mutants tested, all killed. The
 * file-scope `add_action()` registration is unreachable from the Unit layer
 * (the bootstrap runs it inside a discarded Brain Monkey session), so its mutant
 * reports as uncovered; every killable mutant is dead.
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\when;

it('enqueues the theme scripts, localizes the menu strings and sets the background image', function (): void {
    if (! defined('CHILD_THEME_VERSION')) {
        define('CHILD_THEME_VERSION', '9.9.9');
    }

    when('__')->returnArg();
    when('get_stylesheet_directory_uri')->justReturn('https://example.test/theme');
    when('get_background_image')->justReturn('https://example.test/bg.jpg');

    expect('wp_style_add_data')->once()->with('utility-pro', 'rtl', 'replace');

    expect('wp_enqueue_script')->once()->with(
        'genwpacc-dropdown',
        'https://example.test/theme/js/genwpacc-dropdown.js',
        ['jquery'],
        false,
        true,
    );
    expect('wp_enqueue_script')->once()->with(
        'utility-pro-responsive-menu',
        'https://example.test/theme/js/responsive-menu.min.js',
        ['jquery'],
        '1.0.0',
        true,
    );
    expect('wp_enqueue_script')->once()->with(
        'utility-pro',
        'https://example.test/theme/js/responsive-menu.args.js',
        ['utility-pro-responsive-menu'],
        CHILD_THEME_VERSION,
        true,
    );
    expect('wp_enqueue_script')->once()->with(
        'utility-pro-backstretch',
        'https://example.test/theme/js/backstretch.min.js',
        ['jquery'],
        '2.0.1',
        true,
    );
    expect('wp_enqueue_script')->once()->with(
        'utility-pro-backstretch-args',
        'https://example.test/theme/js/backstretch.args.js',
        ['utility-pro-backstretch'],
        CHILD_THEME_VERSION,
        true,
    );

    expect('wp_localize_script')->once()->with('utility-pro-responsive-menu', 'utilityMenuPrimaryL10n', [
        'buttonText' => 'Menu',
        'buttonLabel' => 'Primary Navigation Menu',
        'subButtonText' => 'Sub Menu',
        'subButtonLabel' => 'Sub Menu',
    ]);
    expect('wp_localize_script')->once()->with('utility-pro-responsive-menu', 'utilityMenuFooterL10n', [
        'buttonText' => 'Footer Menu',
        'buttonLabel' => 'Footer Navigation Menu',
        'subButtonText' => 'Sub Menu',
        'subButtonLabel' => 'Sub Menu',
    ]);
    expect('wp_localize_script')->once()->with('utility-pro', 'utilityBackstretchL10n', [
        'src' => 'https://example.test/bg.jpg',
    ]);

    utility_pro_enqueue_assets();
});
