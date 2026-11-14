<?php

/**
 * Loads functions.php once per process, inside the running test, and asserts everything it does at load time.
 *
 * functions.php (and the files it includes) declares functions, so it cannot be included twice. Whichever
 * test runs first loads it here, so Pest attributes the file-scope lines to a test and a mutation run covers
 * them: every test calls load_theme() from tests/Pest.php, so every mutation child checks these assertions.
 * With is_admin() true the setup function also includes the plugin installer and the license updater, which
 * load the real vendored updater class. Hooks are read back with has_action() and has_filter().
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Actions\expectRemoved;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;

function load_theme(): void
{
    if (array_key_exists('theme_loaded', $GLOBALS)) {
        return;
    }

    // Required here, not at the top, so Patchwork (loaded by the bootstrap) sees these files and tests can still mock them.
    require_once __DIR__.'/theme-stubs.php';

    $GLOBALS['theme_loaded'] = true;
    $GLOBALS['theme_calls'] = [];
    $GLOBALS['tgmpa_calls'] = [];

    $root = dirname(__DIR__, 2);

    stubTranslationFunctions();
    when('_x')->returnArg(1);
    when('_n_noop')->alias(fn (string $single, string $plural): array => [$single, $plural]);
    when('get_template_directory')->justReturn($root);
    when('get_stylesheet_directory')->justReturn($root);
    Functions\expect('load_child_theme_textdomain')->once()->with('utility-pro', $root.'/languages');
    expectRemoved('genesis_after_endwhile')->once()->with('genesis_posts_nav');

    require_once $root.'/functions.php';

    $registered = [
        ['action', 'genesis_setup', 'utility_pro_setup', 15],
        ['filter', 'genesis_footer_creds_text', 'utility_pro_footer_creds', 10],
        ['filter', 'genesis_footer_creds_text', 'sp_footer_creds_filter', 10],
        ['filter', 'genesis_author_box_gravatar_size', 'utility_pro_author_box_gravatar_size', 10],
        ['action', 'wp_enqueue_scripts', 'utility_pro_enqueue_assets', 10],
        ['action', 'wp_enqueue_scripts', 'utility_pro_enqueue_fonts', 10],
        ['filter', 'genesis_attr_nav-footer', 'utility_pro_footer_nav_schema', 10],
        ['filter', 'wp_nav_menu_args', 'utility_pro_footer_menu_args', 10],
        ['filter', 'widget_text', 'do_shortcode', 10],
        ['filter', 'theme_page_templates', 'utility_pro_remove_genesis_page_templates', 10],
        ['filter', 'genesis_attr_nav-footer', 'utility_pro_add_nav_secondary_id', 10],
        ['filter', 'genesis_skip_links_output', 'utility_pro_add_nav_secondary_skip_link', 10],
    ];

    // Run the setup function with is_admin() true, so it includes the two admin-only files.
    utility_pro_setup();

    $registered = [
        ...$registered,
        ['action', 'genesis_before_header', 'utility_pro_add_bar', 10],
        ['filter', 'the_content', 'utility_pro_featured_image', 10],
        ['action', 'genesis_before_footer', 'utility_pro_do_footer_nav', 10],
        ['action', 'genesis_after_endwhile', 'utility_pro_post_pagination', 10],
        ['filter', 'get_search_form', 'utility_pro_get_search_form', 25],
        ['action', 'tgmpa_register', 'utility_pro_register_required_plugins', 10],
        ['action', 'admin_menu', 'utility_pro_move_license_page_menu_item', 12],
    ];

    foreach ($registered as [$kind, $hook, $callback, $priority]) {
        $found = $kind === 'action' ? has_action($hook, $callback) : has_filter($hook, $callback);

        expect($found)->toBe($priority, "{$hook} -> {$callback}");
    }

    $calls = $GLOBALS['theme_calls'];

    expect(constant('CHILD_THEME_NAME'))->toBe('utility-pro')
        ->and(constant('CHILD_THEME_URL'))->toBe('https://store.carriedils.com/utility-pro')
        ->and(CHILD_THEME_VERSION)->toBe('1.3.2')
        ->and($calls['supports'])->toBe([
            ['html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']],
            ['genesis-responsive-viewport'],
            ['custom-background', ['wp-head-callback' => '__return_false']],
            ['genesis-accessibility', ['404-page', 'headings', 'skip-links']],
            ['genesis-footer-widgets', 3],
            ['genesis-style-selector', [
                'utility-pro-purple' => 'Purple',
                'utility-pro-green' => 'Green',
                'utility-pro-red' => 'Red',
            ]],
            ['genesis-structural-wraps', [
                'footer', 'footer-widgets', 'footernav', 'header', 'home-gallery', 'menu-footer', 'nav', 'site-inner', 'site-tagline',
            ]],
            ['genesis-menus', [
                'primary' => 'Primary Navigation Menu',
                'footer' => 'Footer Navigation Menu',
            ]],
        ])
        ->and(array_slice($calls['sidebars'], 0, 1))->toBe([[
            'id' => 'page-leadership',
            'name' => 'Page - Leadership',
            'description' => 'This is the leadership section to the Leadership Page.',
        ]])
        ->and(array_column($calls['sidebars'], 'id'))->toHaveCount(8)
        ->and($calls['sizes'])->toBe([
            ['leadership', 210, 280, true],
            ['feature-large', 960, 330, true],
        ])
        ->and($calls['unregistered_sidebars'])->toBe(['sidebar-alt'])
        ->and($calls['layouts'])->toBe(['content-sidebar-sidebar', 'sidebar-content-sidebar', 'sidebar-sidebar-content']);

    // The license updater is the real vendored class, built with this theme's store details.
    expect($GLOBALS['updater'])->toBeInstanceOf(EDD_Theme_Updater_Admin::class);

    // Some of its properties are dynamic (never declared), so read them all through the object's own scope.
    $properties = (fn (): array => get_object_vars($this))->call($GLOBALS['updater']);
    $read = static fn (string $property): mixed => $properties[$property] ?? null;

    expect($read('remote_api_url'))->toBe('https://store.carriedils.com')
        ->and($read('item_name'))->toBe('Utility Pro')
        ->and($read('theme_slug'))->toBe('utility-pro')
        ->and($read('version'))->toBe('1.3.2')
        ->and($read('author'))->toBe('Carrie Dils')
        ->and($read('strings'))->toHaveCount(21)->toMatchArray([
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
}
