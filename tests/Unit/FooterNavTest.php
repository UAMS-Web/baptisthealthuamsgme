<?php

/**
 * Unit tests for the footer navigation callbacks in includes/footer-nav.php.
 *
 * Mutation (macOS, working evidence only): 8 mutants tested, all killed. The two
 * file-scope `add_filter()` registrations are unreachable from the Unit
 * layer (the bootstrap runs them inside a discarded Brain Monkey session), so
 * their two mutants report as uncovered; every killable mutant is dead.
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

it('prints the Genesis footer menu for the footer theme location', function (): void {
    Functions\expect('genesis_nav_menu')
        ->once()
        ->with([
            'menu_class' => 'menu genesis-nav-menu menu-footer',
            'theme_location' => 'footer',
        ]);

    utility_pro_do_footer_nav();
});

it('limits the footer menu to one level and keeps its other arguments', function (): void {
    expect(utility_pro_footer_menu_args(['theme_location' => 'footer', 'depth' => 0, 'echo' => false]))
        ->toBe(['theme_location' => 'footer', 'depth' => 1, 'echo' => false]);
});

it('leaves menus in other locations unchanged', function (string $location): void {
    $args = ['theme_location' => $location, 'depth' => 0];

    expect(utility_pro_footer_menu_args($args))->toBe($args);
})->with(['primary', 'secondary', '']);
