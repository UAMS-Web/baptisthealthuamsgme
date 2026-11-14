<?php

/**
 * Unit tests for includes/suggested-plugins.php.
 *
 * The file is included by `utility_pro_setup()` inside `load_theme()` (tests/Support/load-theme.php), which
 * asserts its hook registration. `tgmpa()` is a recording stand-in from tests/Support/theme-stubs.php.
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;

beforeEach(function (): void {
    Theme_Stub_Calls::$tgmpa = [];
    stubTranslationFunctions();
    when('_x')->returnArg(1);
    when('_n_noop')->alias(fn (string $single, string $plural): array => [$single, $plural]);
});

it('registers the three recommended plugins', function (): void {
    utility_pro_register_required_plugins();

    expect(Theme_Stub_Calls::$tgmpa)->toHaveCount(1)
        ->and(Theme_Stub_Calls::$tgmpa[0][0])->toBe([
            ['name' => 'Better Font Awesome', 'slug' => 'better-font-awesome', 'required' => false],
            ['name' => 'Genesis E-News Extended', 'slug' => 'genesis-enews-extended', 'required' => false],
            ['name' => 'WP Accessibility', 'slug' => 'wp-accessibility', 'required' => false],
        ]);
});

it('passes the installer configuration', function (): void {
    utility_pro_register_required_plugins();

    $config = Theme_Stub_Calls::$tgmpa[0][1];

    expect($config)
        ->toMatchArray([
            'domain' => 'utility-pro',
            'default_path' => '',
            'parent_slug' => 'themes.php',
            'menu' => 'install-required-plugins',
            'has_notices' => true,
            'dismissable' => true,
            'dismiss_msg' => '',
            'is_automatic' => false,
            'message' => '',
        ])
        ->and($config['strings'])->toHaveKeys([
            'page_title', 'menu_title', 'installing', 'oops', 'install_link', 'activate_link', 'return',
            'plugin_activated', 'complete', 'nag_type',
            'notice_can_install_required', 'notice_can_install_recommended', 'notice_cannot_install',
            'notice_can_activate_required', 'notice_can_activate_recommended', 'notice_cannot_activate',
            'notice_ask_to_update', 'notice_cannot_update',
        ])
        ->and($config['strings'])->toMatchArray([
            'page_title' => 'Install Required Plugins',
            'menu_title' => 'Install Plugins',
            'nag_type' => 'updated',
        ]);
});
