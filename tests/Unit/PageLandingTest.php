<?php

/**
 * Unit tests for page_landing.php, the Landing page template.
 *
 * Mutation (macOS, working evidence only; a coverage ticket closes on a Linux
 * run): 29 tested, all killed. Every killable mutant is dead.
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Filters\expectAdded;

/**
 * Include the template once per process, asserting what it does at load time.
 *
 * The template declares a function, so it cannot be included twice; whichever test
 * loads it first carries the load-time expectations.
 */
function load_page_landing(): void
{
    if (array_key_exists('page_landing_loaded', $GLOBALS)) {
        return;
    }

    expectAdded('body_class')->once()->with('utility_add_body_class');
    expectAdded('genesis_pre_get_option_site_layout')->once()->with('__genesis_return_full_width_content');

    $removed = [];
    Functions\expect('remove_action')->andReturnUsing(function (...$args) use (&$removed): void {
        $removed[] = $args;
    });
    Functions\expect('genesis')->once();

    include dirname(__DIR__, 2).'/page_landing.php';

    expect($removed)->toBe([
        ['genesis_header', 'genesis_header_markup_open', 5],
        ['genesis_header', 'genesis_do_header'],
        ['genesis_header', 'genesis_header_markup_close', 15],
        ['genesis_after_header', 'genesis_do_nav'],
        ['genesis_before_loop', 'genesis_do_breadcrumbs'],
        ['genesis_entry_header', 'genesis_entry_header_markup_open', 5],
        ['genesis_entry_header', 'genesis_entry_header_markup_close', 15],
        ['genesis_before_footer', 'genesis_footer_widget_areas'],
        ['genesis_footer', 'genesis_footer_markup_open', 5],
        ['genesis_footer', 'genesis_do_footer'],
        ['genesis_footer', 'genesis_footer_markup_close', 15],
        ['genesis_before_header', 'utility_pro_add_bar'],
        ['genesis_before_footer', 'utility_pro_do_footer_nav'],
    ]);

    $GLOBALS['page_landing_loaded'] = true;
}

it('strips the header, navigation, breadcrumbs and footer, goes full width and runs Genesis', function (): void {
    load_page_landing();

    expect(function_exists('utility_add_body_class'))->toBeTrue();
});

it('adds the landing class to the body classes', function (): void {
    load_page_landing();

    expect(utility_add_body_class(['page', 'logged-in']))->toBe(['page', 'logged-in', 'utility-landing']);
});
