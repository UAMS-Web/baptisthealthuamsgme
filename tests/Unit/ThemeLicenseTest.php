<?php

/**
 * Unit tests for includes/theme-license.php.
 *
 * The file is included by `utility_pro_setup()` inside `load_theme()` (tests/Support/load-theme.php), which
 * asserts the updater it builds, its strings and the `admin_menu` registration against the real vendored class.
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\expect;

it('moves the license page from Appearance to the Genesis menu', function (): void {
    expect('remove_submenu_page')
        ->once()
        ->with('themes.php', 'utility-pro-license')
        ->andReturn(['Menu title', 'manage_options', 'utility-pro-license', 'Page title']);
    expect('add_submenu_page')
        ->once()
        ->with('genesis', 'Page title', 'Menu title', 'manage_options', 'utility-pro-license', [$GLOBALS['updater'], 'license_page']);

    utility_pro_move_license_page_menu_item();
});

it('passes on only the first four fields of the removed item, and an empty string for a missing or non-string one', function (): void {
    expect('remove_submenu_page')->times(3)->andReturn(
        ['Menu title', 'manage_options', 'utility-pro-license', 'Page title', 'extra', 'more'],
        ['Menu title', 'manage_options', 'utility-pro-license'],
        [null, 'manage_options', 'utility-pro-license', 'Page title'],
    );
    expect('add_submenu_page')->once()->with('genesis', 'Page title', 'Menu title', 'manage_options', 'utility-pro-license', [$GLOBALS['updater'], 'license_page']);
    expect('add_submenu_page')->once()->with('genesis', '', 'Menu title', 'manage_options', 'utility-pro-license', [$GLOBALS['updater'], 'license_page']);
    expect('add_submenu_page')->once()->with('genesis', 'Page title', '', 'manage_options', 'utility-pro-license', [$GLOBALS['updater'], 'license_page']);

    utility_pro_move_license_page_menu_item();
    utility_pro_move_license_page_menu_item();
    utility_pro_move_license_page_menu_item();
});

it('moves nothing when the Appearance item is not there', function (): void {
    expect('remove_submenu_page')->once()->with('themes.php', 'utility-pro-license')->andReturn(false);
    expect('add_submenu_page')->never();

    utility_pro_move_license_page_menu_item();
});

it('moves nothing when the updater was not built', function (): void {
    $updater = $GLOBALS['updater'];
    $GLOBALS['updater'] = null;

    try {
        expect('remove_submenu_page')->once()->andReturn(['Menu title', 'manage_options', 'utility-pro-license', 'Page title']);
        expect('add_submenu_page')->never();

        utility_pro_move_license_page_menu_item();
    } finally {
        $GLOBALS['updater'] = $updater;
    }
});
