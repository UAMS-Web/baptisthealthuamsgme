<?php

/**
 * Unit tests for includes/theme-license.php.
 *
 * The file is only loaded by functions.php inside `is_admin()`, so the bootstrap
 * never includes it. A recording stand-in for `EDD_Theme_Updater_Admin` lets the
 * file's own `class_exists()` guard skip the vendored updater, and keeps the
 * constructor arguments for the assertions. The file is included once per
 * process (it declares a function); the hook registration it makes is read back
 * from Brain Monkey in the same call. Every assertion about what the file does at
 * load time sits in one test, because only the test that includes it is covered
 * for those lines.
 *
 * Mutation (macOS, working evidence only): 42 mutants tested, all killed. The three
 * concatenation mutants on the guarded `include` of the vendored updater report as
 * uncovered, because the stand-in class makes the guard skip it; every killable
 * mutant is dead.
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\stubTranslationFunctions;

if (! class_exists('EDD_Theme_Updater_Admin')) {
    class EDD_Theme_Updater_Admin
    {
        public function __construct(public array $config = [], public array $strings = []) {}

        public function license_page(): void {}
    }
}

/**
 * Include the file once and return the priority it registered `admin_menu` at.
 */
function load_theme_license(): int|false
{
    // A global, not a function static: Patchwork re-compiles functions in files it loads, which resets statics.
    if (! array_key_exists('theme_license_priority', $GLOBALS)) {
        stubTranslationFunctions();

        include dirname(__DIR__, 2).'/includes/theme-license.php';

        $GLOBALS['theme_license_priority'] = has_action('admin_menu', 'utility_pro_move_license_page_menu_item');
    }

    return $GLOBALS['theme_license_priority'];
}

beforeEach(function (): void {
    stubTranslationFunctions();
});

it('loads the updater and hooks the menu move into admin_menu at priority 12', function (): void {
    expect(load_theme_license())->toBe(12)
        ->and($GLOBALS['updater'])->toBeInstanceOf(EDD_Theme_Updater_Admin::class)
        ->and($GLOBALS['updater']->config)->toBe([
            'remote_api_url' => 'https://store.carriedils.com',
            'item_name' => 'Utility Pro',
            'theme_slug' => 'utility-pro',
            'version' => '1.3.1',
            'author' => 'Carrie Dils',
        ])
        ->and($GLOBALS['updater']->strings)
        ->toHaveCount(21)
        ->toMatchArray([
            'theme-license' => 'Utility Pro License',
            'enter-key' => 'Enter your theme license key.',
            'license-key' => 'License Key',
            'license-action' => 'License Action',
            'deactivate-license' => 'Deactivate License',
            'activate-license' => 'Activate License',
            'status-unknown' => 'License status is unknown.',
            'renew' => 'Renew?',
            'unlimited' => 'unlimited',
            'license-key-is-active' => 'License key is active.',
            'expires%s' => 'Expires %s.',
            '%1$s/%2$-sites' => 'You have %1$s / %2$s sites activated.',
            'license-key-expired-%s' => 'License key expired %s.',
            'license-key-expired' => 'License key has expired.',
            'license-keys-do-not-match' => 'License keys do not match.',
            'license-is-inactive' => 'License is inactive.',
            'license-key-is-disabled' => 'License key is disabled.',
            'site-is-inactive' => 'Site is inactive.',
            'license-status-unknown' => 'License status is unknown.',
            'update-notice' => "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.",
            'update-available' => '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.',
        ]);
});

it('moves the license page from Appearance to the Genesis menu', function (): void {
    load_theme_license();

    Functions\expect('remove_submenu_page')
        ->once()
        ->with('themes.php', 'utility-pro-license')
        ->andReturn(['Menu title', 'manage_options', 'utility-pro-license', 'Page title']);
    Functions\expect('add_submenu_page')
        ->once()
        ->with('genesis', 'Page title', 'Menu title', 'manage_options', 'utility-pro-license', [$GLOBALS['updater'], 'license_page']);

    utility_pro_move_license_page_menu_item();
});
