<?php

/**
 * Unit tests for functions.php.
 *
 * The file-scope statements and `utility_pro_setup()` are asserted by `load_theme()` in
 * tests/Support/load-theme.php, which every test runs.
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

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
