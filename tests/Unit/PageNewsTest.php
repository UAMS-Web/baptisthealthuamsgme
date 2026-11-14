<?php

/**
 * Unit tests for page_news.php, the News page template.
 *
 * Mutation (macOS, working evidence only; a coverage ticket closes on a Linux
 * run): 13 tested, all killed. Every killable mutant is dead.
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Actions\expectAdded;
use function Brain\Monkey\Actions\expectRemoved;

/**
 * Include the template once per process, asserting what it does at load time.
 *
 * The template declares a function, so it cannot be included twice; whichever test
 * loads it first carries the load-time expectations.
 */
function load_page_news(): void
{
    if (array_key_exists('page_news_loaded', $GLOBALS)) {
        return;
    }

    expectRemoved('genesis_loop')->once()->with('genesis_do_loop');
    expectAdded('genesis_loop')->once()->with('prefix_do_loop');
    Functions\expect('genesis')->once();

    include dirname(__DIR__, 2).'/page_news.php';

    $GLOBALS['page_news_loaded'] = true;
}

it('swaps the Genesis loop for the news loop and runs Genesis', function (): void {
    load_page_news();

    expect(function_exists('prefix_do_loop'))->toBeTrue();
});

it('runs a custom loop over the news category, five posts a page', function (): void {
    load_page_news();

    $GLOBALS['paged'] = 3;
    Functions\expect('genesis_custom_loop')
        ->once()
        ->with([
            'post_type' => '',
            'category_name' => 'news',
            'order' => 'DSC',
            'orderby' => 'date',
            'paged' => 3,
            'posts_per_page' => 5,
        ]);

    prefix_do_loop();
});
