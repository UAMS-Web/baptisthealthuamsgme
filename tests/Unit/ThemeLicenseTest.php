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
