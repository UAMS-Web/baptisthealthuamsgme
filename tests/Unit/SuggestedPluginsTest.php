<?php

/**
 * Unit tests for includes/suggested-plugins.php.
 *
 * The file is only loaded by functions.php inside `is_admin()`, so the bootstrap
 * never includes it. Stand-in `TGM_Plugin_Activation` and `WP_List_Table` classes let the
 * vendored library's own `class_exists()` guards skip WordPress' files, and `tgmpa()` is mocked per
 * test. The file is included once per process (it declares a function); the hook
 * registration it makes is read back from Brain Monkey in the same call.
 *
 * Mutation (macOS, working evidence only): 54 mutants tested, all killed.
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;

if (! class_exists('TGM_Plugin_Activation')) {
    class TGM_Plugin_Activation {}
}

// The vendored file requires WordPress' list table outside its own guard.
if (! class_exists('WP_List_Table')) {
    class WP_List_Table {}
}

/**
 * Include the file once and return the priority it registered `tgmpa_register` at.
 */
function load_suggested_plugins(): int|false
{
    // A global, not a function static: Patchwork re-compiles functions in files it loads, which resets statics.
    if (! array_key_exists('suggested_plugins_priority', $GLOBALS)) {
        stubTranslationFunctions();
        when('_x')->returnArg(1);
        when('_n_noop')->alias(fn (string $single, string $plural): array => [$single, $plural]);

        include dirname(__DIR__, 2).'/includes/suggested-plugins.php';

        $GLOBALS['suggested_plugins_priority'] = has_action('tgmpa_register', 'utility_pro_register_required_plugins');
    }

    return $GLOBALS['suggested_plugins_priority'];
}

beforeEach(function (): void {
    stubTranslationFunctions();
    when('_x')->returnArg(1);
    when('_n_noop')->alias(fn (string $single, string $plural): array => [$single, $plural]);
    if (! function_exists('tgmpa')) {
        when('tgmpa')->justReturn();
    }
});

it('hooks the registration into tgmpa_register at the default priority', function (): void {
    expect(load_suggested_plugins())->toBe(10);
});

it('registers the three recommended plugins', function (): void {
    load_suggested_plugins();

    $captured = null;
    Functions\expect('tgmpa')->once()->andReturnUsing(function (array $plugins, array $config) use (&$captured): void {
        $captured = [$plugins, $config];
    });

    utility_pro_register_required_plugins();

    expect($captured[0])->toBe([
        ['name' => 'Better Font Awesome', 'slug' => 'better-font-awesome', 'required' => false],
        ['name' => 'Genesis E-News Extended', 'slug' => 'genesis-enews-extended', 'required' => false],
        ['name' => 'WP Accessibility', 'slug' => 'wp-accessibility', 'required' => false],
    ]);
});

it('passes the installer configuration', function (): void {
    load_suggested_plugins();

    $config = null;
    Functions\expect('tgmpa')->once()->andReturnUsing(function (array $plugins, array $passed) use (&$config): void {
        $config = $passed;
    });

    utility_pro_register_required_plugins();

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
        ->and($config['strings']['page_title'])->toBe('Install Required Plugins')
        ->and($config['strings']['menu_title'])->toBe('Install Plugins')
        ->and($config['strings']['nag_type'])->toBe('updated');
});
