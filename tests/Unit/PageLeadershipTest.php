<?php

/**
 * Unit tests for page_leadership.php, the Leadership page template.
 *
 * Mutation (macOS, working evidence only; a coverage ticket closes on a Linux
 * run): 11 tested, all killed. Every killable mutant is dead.
 */

declare(strict_types=1);

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

use function Brain\Monkey\Actions\expectAdded;
use function Brain\Monkey\Actions\expectRemoved;

/**
 * Include the template once per process, asserting what it does at load time.
 *
 * The template declares functions, so it cannot be included twice; whichever test
 * loads it first carries the load-time expectations.
 */
function load_page_leadership(): void
{
    if (array_key_exists('page_leadership_loaded', $GLOBALS)) {
        return;
    }

    expectAdded('genesis_meta')->once()->with('mpp_leadership_page');
    Functions\expect('genesis')->once();

    include dirname(__DIR__, 2).'/page_leadership.php';

    $GLOBALS['page_leadership_loaded'] = true;
}

it('hooks the leadership setup into genesis_meta and runs Genesis', function (): void {
    load_page_leadership();

    expect(function_exists('mpp_leadership_page'))->toBeTrue();
});

it('swaps the loop for the leadership widget area when the sidebar is active', function (): void {
    load_page_leadership();

    Functions\expect('is_active_sidebar')->once()->with('page-leadership')->andReturn(true);
    Filters\expectAdded('genesis_pre_get_option_site_layout')->once()->with('__genesis_return_full_width_content');
    Filters\expectAdded('body_class')->once()->with('mpp_body_class');
    expectRemoved('genesis_loop')->once()->with('genesis_do_loop');
    expectAdded('genesis_loop')->once()->with('mpp_leadership_page_widget');

    mpp_leadership_page();
});

it('leaves the default loop alone when the sidebar is inactive', function (): void {
    load_page_leadership();

    Functions\expect('is_active_sidebar')->once()->with('page-leadership')->andReturn(false);
    Filters\expectAdded('genesis_pre_get_option_site_layout')->never();
    Filters\expectAdded('body_class')->never();

    expectRemoved('genesis_loop')->never();
    expectAdded('genesis_loop')->never();

    mpp_leadership_page();
});

it('adds the mpp-home body class', function (): void {
    load_page_leadership();

    expect(mpp_body_class(['page']))->toBe(['page', 'mpp-home']);
});

it('prints the leadership widget area inside its wrapper', function (): void {
    load_page_leadership();

    Functions\expect('genesis_widget_area')
        ->once()
        ->with('page-leadership', [
            'before' => '<div id="leadership"><div class="wrap">',
            'after' => '</div></div>',
        ]);

    mpp_leadership_page_widget();
});
