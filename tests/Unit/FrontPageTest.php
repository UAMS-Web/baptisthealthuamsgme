<?php

/**
 * Unit tests for front-page.php, the home page template.
 *
 * Mutation (macOS, working evidence only; a coverage ticket closes on a Linux
 * run): 46 tested, all killed. Every killable mutant is dead.
 */

declare(strict_types=1);

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use function Brain\Monkey\Actions\expectAdded;
use function Brain\Monkey\Functions\when;

/**
 * Include the template once per process, asserting what it does at load time.
 *
 * The template declares functions, so it cannot be included twice; whichever test
 * loads it first carries the load-time expectations.
 */
function load_front_page(): void
{
    if (array_key_exists('front_page_loaded', $GLOBALS)) {
        return;
    }

    expectAdded('genesis_meta')->once()->with('utility_pro_homepage_setup');
    Functions\expect('genesis')->once();

    include dirname(__DIR__, 2).'/front-page.php';

    $GLOBALS['front_page_loaded'] = true;
}

/**
 * Stub which of the three home widget areas are active.
 *
 * @param  array<string, bool>  $active  Sidebar id => active, for the ids the setup function asks about.
 */
function stub_home_sidebars(array $active): void
{
    when('is_active_sidebar')->alias(fn (string $id): bool => $active[$id] ?? false);
}

it('hooks the home setup into genesis_meta and runs Genesis', function (): void {
    load_front_page();

    expect(function_exists('utility_pro_homepage_setup'))->toBeTrue();
});

it('does nothing when no home widget area is active', function (): void {
    load_front_page();
    stub_home_sidebars([]);
    expectAdded('genesis_after_header')->never();
    Filters\expectAdded('genesis_site_title_wrap')->never();

    utility_pro_homepage_setup();
});

it('adds only the welcome section when only that area is active, on page one', function (): void {
    load_front_page();
    stub_home_sidebars(['utility-home-welcome' => true]);
    when('get_query_var')->justReturn('');
    expectAdded('genesis_after_header')->once()->with('utility_pro_add_home_welcome');
    Filters\expectAdded('genesis_site_title_wrap')->once()->with('utility_pro_return_h1');

    utility_pro_homepage_setup();
});

it('adds only the gallery section when only that area is active', function (): void {
    load_front_page();
    stub_home_sidebars(['utility-home-gallery-1' => true]);
    when('get_query_var')->justReturn('');
    expectAdded('genesis_after_header')->once()->with('utility_pro_add_home_gallery');
    Filters\expectAdded('genesis_site_title_wrap')->once()->with('utility_pro_return_h1');

    utility_pro_homepage_setup();
});

it('adds only the call to action section when only that area is active', function (): void {
    load_front_page();
    stub_home_sidebars(['utility-call-to-action' => true]);
    when('get_query_var')->justReturn('');
    expectAdded('genesis_after_header')->once()->with('utility_pro_add_call_to_action');
    Filters\expectAdded('genesis_site_title_wrap')->once()->with('utility_pro_return_h1');

    utility_pro_homepage_setup();
});

it('adds all three sections when all three areas are active', function (): void {
    load_front_page();
    stub_home_sidebars([
        'utility-home-welcome' => true,
        'utility-home-gallery-1' => true,
        'utility-call-to-action' => true,
    ]);
    when('get_query_var')->justReturn('1');
    expectAdded('genesis_after_header')->times(3);

    utility_pro_homepage_setup();
});

it('skips the sections after page one but still filters the site title', function (): void {
    load_front_page();
    stub_home_sidebars([
        'utility-home-welcome' => true,
        'utility-home-gallery-1' => true,
        'utility-call-to-action' => true,
    ]);
    Functions\expect('get_query_var')->with('page')->andReturn('2');
    expectAdded('genesis_after_header')->never();
    Filters\expectAdded('genesis_site_title_wrap')->once()->with('utility_pro_return_h1');

    utility_pro_homepage_setup();
});

it('returns h1 for the site title wrap', function (): void {
    load_front_page();

    expect(utility_pro_return_h1())->toBe('h1');
});

it('prints the welcome widget area inside its wrapper', function (): void {
    load_front_page();
    Functions\expect('genesis_widget_area')
        ->once()
        ->with('utility-home-welcome', [
            'before' => '<div class="home-welcome"><div class="wrap">',
            'after' => '</div></div>',
        ]);

    utility_pro_add_home_welcome();
});

it('prints the four gallery areas between the structural wrap and its close', function (): void {
    load_front_page();
    $calls = [];
    when('genesis_attr')->alias(function (string $context) use (&$calls): string {
        $calls[] = ['attr', $context];

        return 'class="home-gallery"';
    });
    when('genesis_structural_wrap')->alias(function (...$args) use (&$calls): void {
        $calls[] = ['wrap', ...$args];
    });
    when('genesis_widget_area')->alias(function (string $id, array $args) use (&$calls): void {
        $calls[] = ['area', $id, $args];
    });

    ob_start();
    utility_pro_add_home_gallery();
    $output = ob_get_clean();

    expect($output)->toBe('<div class="home-gallery"></div>')
        ->and($calls)->toBe([
            ['attr', 'home-gallery'],
            ['wrap', 'home-gallery'],
            ['area', 'utility-home-gallery-1', ['before' => '<div class="home-gallery-1 widget-area">', 'after' => '</div>']],
            ['area', 'utility-home-gallery-2', ['before' => '<div class="home-gallery-2 widget-area">', 'after' => '</div>']],
            ['area', 'utility-home-gallery-3', ['before' => '<div class="home-gallery-3 widget-area">', 'after' => '</div>']],
            ['area', 'utility-home-gallery-4', ['before' => '<div class="home-gallery-4 widget-area">', 'after' => '</div>']],
            ['wrap', 'home-gallery', 'close'],
        ]);
});

it('prints the call to action widget area inside its wrapper', function (): void {
    load_front_page();
    Functions\expect('genesis_widget_area')
        ->once()
        ->with('utility-call-to-action', [
            'before' => '<div class="call-to-action-bar"><div class="wrap">',
            'after' => '</div></div>',
        ]);

    utility_pro_add_call_to_action();
});

it('runs a custom posts loop with the query args merged over the defaults', function (): void {
    load_front_page();
    $GLOBALS['query_args'] = ['posts_per_page' => 3];
    when('get_query_var')->justReturn(4);
    Functions\expect('genesis_custom_loop')
        ->once()
        ->with(['post_type' => 'post', 'paged' => 4, 'posts_per_page' => 3]);

    utility_pro_front_loop();
});

it('runs the custom posts loop with the defaults when there are no query args', function (): void {
    load_front_page();
    unset($GLOBALS['query_args']);
    when('get_query_var')->justReturn(4);
    Functions\expect('genesis_custom_loop')->once()->with(['post_type' => 'post', 'paged' => 4]);

    utility_pro_front_loop();
});
