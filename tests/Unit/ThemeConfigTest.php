<?php

/**
 * Unit tests for the filter callbacks and helpers in includes/theme-config.php.
 *
 * The file-scope hook registrations are asserted by `load_theme()` in
 * tests/Support/load-theme.php, which every test runs. Every mutant is killed.
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\when;

it('removes only the Genesis blog page template', function (): void {
    $templates = [
        'page_blog.php' => 'Blog',
        'page_landing.php' => 'Landing',
    ];

    expect(utility_pro_remove_genesis_page_templates($templates))
        ->toBe(['page_landing.php' => 'Landing']);
});

it('leaves a template list without the blog template unchanged', function (): void {
    expect(utility_pro_remove_genesis_page_templates(['page_news.php' => 'News']))
        ->toBe(['page_news.php' => 'News']);
});

it('adds the footer navigation id and keeps the other attributes', function (): void {
    expect(utility_pro_add_nav_secondary_id(['class' => 'nav-footer', 'id' => 'old']))
        ->toBe(['class' => 'nav-footer', 'id' => 'genesis-nav-footer']);
});

it('inserts a footer skip link after the first existing link when the footer menu exists', function (): void {
    when('has_nav_menu')->justReturn(true);
    when('__')->returnArg();

    $links = ['genesis-content' => 'Skip to content', 'genesis-nav-primary' => 'Skip to primary navigation'];

    expect(utility_pro_add_nav_secondary_skip_link($links))->toBe([
        'genesis-content' => 'Skip to content',
        'genesis-nav-footer' => 'Skip to footer navigation',
        'genesis-nav-primary' => 'Skip to primary navigation',
    ]);
});

it('passes the skip links through when there is no footer menu', function (): void {
    when('has_nav_menu')->justReturn(false);

    $links = ['genesis-content' => 'Skip to content', 'genesis-nav-primary' => 'Skip to primary navigation'];

    expect(utility_pro_add_nav_secondary_skip_link($links))->toBe($links);
});

it('asks for the footer menu by its location', function (): void {
    Functions\expect('has_nav_menu')->once()->with('footer')->andReturn(false);

    utility_pro_add_nav_secondary_skip_link([]);
});

it('uses numbered pagination when the Genesis option is numeric', function (): void {
    when('__')->returnArg();
    Functions\expect('genesis_get_option')->once()->with('posts_nav')->andReturn('numeric');
    Functions\expect('the_posts_pagination')
        ->once()
        ->with([
            'mid_size' => 2,
            'before_page_number' => '<span class="screen-reader-text">Page </span>',
        ]);
    Functions\expect('the_posts_navigation')->never();

    utility_pro_post_pagination();
});

it('uses previous and next navigation for any other Genesis option', function (): void {
    when('__')->returnArg();
    when('genesis_get_option')->justReturn('prev-next');

    Functions\expect('the_posts_navigation')->once();
    Functions\expect('the_posts_pagination')->never();

    utility_pro_post_pagination();
});
