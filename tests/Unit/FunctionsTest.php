<?php

/**
 * Unit tests for functions.php.
 *
 * Mutation (macOS, working evidence only; a coverage ticket closes on a Linux
 * run): 81 mutants tested, all killed; 34 report as uncovered, in two groups.
 * Thirty-one sit on file-scope statements that the bootstrap runs inside a
 * discarded Brain Monkey session (the text domain call, the `require` and
 * `include` paths, and the `genesis_setup`, `genesis_footer_creds_text` and
 * `genesis_author_box_gravatar_size` registrations), recorded on #14. Six sit
 * on the two `include` paths inside the `is_admin()` branch of the setup
 * function, which would redeclare functions that the suggested plugins and theme
 * license tests already load in the same process; those two files are tested
 * directly. Every killable mutant is dead.
 */

declare(strict_types=1);

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use function Brain\Monkey\Actions\expectAdded;
use function Brain\Monkey\Actions\expectRemoved;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;

it('prints the utility bar widget area inside its wrapper', function (): void {
    Functions\expect('genesis_widget_area')
        ->once()
        ->with('utility-bar', [
            'before' => '<div class="utility-bar"><div class="wrap">',
            'after' => '</div></div>',
        ]);

    utility_pro_add_bar();
});

it('prepends the featured image on a single post with a thumbnail', function (): void {
    when('is_singular')->justReturn(true);
    when('has_post_thumbnail')->justReturn(true);
    when('get_the_ID')->justReturn(42);
    Functions\expect('get_the_post_thumbnail')->once()->with(42, 'feature-large')->andReturn('<img src="x.jpg">');

    expect(utility_pro_featured_image('<p>Body</p>'))
        ->toBe('<div class="featured-image"><img src="x.jpg"></div><p>Body</p>');
});

it('asks for single posts only', function (): void {
    Functions\expect('is_singular')->once()->with('post')->andReturn(false);
    when('has_post_thumbnail')->justReturn(true);

    expect(utility_pro_featured_image('<p>Body</p>'))->toBe('<p>Body</p>');
});

it('leaves the content alone when it is not a single post', function (): void {
    when('is_singular')->justReturn(false);
    when('has_post_thumbnail')->justReturn(true);

    Functions\expect('get_the_post_thumbnail')->never();

    expect(utility_pro_featured_image('<p>Body</p>'))->toBe('<p>Body</p>');
});

it('leaves the content alone when the post has no thumbnail', function (): void {
    when('is_singular')->justReturn(true);
    when('has_post_thumbnail')->justReturn(false);

    Functions\expect('get_the_post_thumbnail')->never();

    expect(utility_pro_featured_image('<p>Body</p>'))->toBe('<p>Body</p>');
});

it('replaces the footer credits with the Utility Pro credit line', function (): void {
    expect(utility_pro_footer_creds('old'))
        ->toBe('Powered by WordPress and the <a href="https://store.carriedils.com/downloads/utility-pro/?utm_source=Utility%20Pro%20Footer%20Credits&utm_medium=Distributed%20Theme&utm_campaign=Utility%20Pro%20Theme" rel="nofollow">Utility Pro</a> theme for Genesis Framework.');
});

it('replaces the footer credits with the Baptist Health UAMS GME line', function (): void {
    expect(sp_footer_creds_filter('old'))
        ->toBe('[footer_copyright] &middot; <a href="http://baptisthealthuams-gme.com">Baptist Health UAMS GME</a> &middot;  All Rights Reserved');
});

it('sets the author box gravatar to 96 pixels whatever size it was given', function (int $size): void {
    expect(utility_pro_author_box_gravatar_size($size))->toBe(96);
})->with([32, 70, 96, 200]);

it('sets up the theme: constants, supports, sizes, layouts, widget areas and hooks', function (): void {
    stubTranslationFunctions();
    when('is_admin')->justReturn(false);

    $supports = [];
    Functions\expect('add_theme_support')->andReturnUsing(function (...$args) use (&$supports): void {
        $supports[] = $args;
    });
    $sidebars = [];
    Functions\expect('genesis_register_sidebar')->once()->andReturnUsing(function (array $sidebar) use (&$sidebars): void {
        $sidebars[] = $sidebar;
    });
    $sizes = [];
    Functions\expect('add_image_size')->andReturnUsing(function (...$args) use (&$sizes): void {
        $sizes[] = $args;
    });
    Functions\expect('unregister_sidebar')->once()->with('sidebar-alt');
    $layouts = [];
    Functions\expect('genesis_unregister_layout')->andReturnUsing(function (string $layout) use (&$layouts): void {
        $layouts[] = $layout;
    });
    Functions\expect('utility_pro_register_widget_areas')->once();

    expectAdded('genesis_before_header')->once()->with('utility_pro_add_bar');
    Filters\expectAdded('the_content')->once()->with('utility_pro_featured_image');
    expectAdded('genesis_before_footer')->once()->with('utility_pro_do_footer_nav');
    expectRemoved('genesis_after_endwhile')->once()->with('genesis_posts_nav');
    expectAdded('genesis_after_endwhile')->once()->with('utility_pro_post_pagination');
    Filters\expectAdded('get_search_form')->once()->with('utility_pro_get_search_form', 25);

    // Constants are process-wide: another test may have defined one already, and PHP warns when define() repeats it.
    set_error_handler(static fn (int $level, string $message): bool => str_contains($message, 'already defined'));

    try {
        utility_pro_setup();
    } finally {
        restore_error_handler();
    }

    expect(CHILD_THEME_NAME)->toBe('utility-pro')
        ->and(CHILD_THEME_URL)->toBe('https://store.carriedils.com/utility-pro')
        ->and(CHILD_THEME_VERSION)->toBe('1.3.1')
        ->and($supports)->toBe([
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
        ->and($sidebars)->toBe([[
            'id' => 'page-leadership',
            'name' => 'Page - Leadership',
            'description' => 'This is the leadership section to the Leadership Page.',
        ]])
        ->and($sizes)->toBe([
            ['leadership', 210, 280, true],
            ['feature-large', 960, 330, true],
        ])
        ->and($layouts)->toBe(['content-sidebar-sidebar', 'sidebar-content-sidebar', 'sidebar-sidebar-content']);
});
