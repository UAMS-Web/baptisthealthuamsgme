<?php

/**
 * Unit tests for includes/widget-areas.php.
 *
 * Mutation (macOS, working evidence only; a coverage ticket closes on a Linux
 * run): 46 mutants tested, all killed. The file has no file-scope registration,
 * so nothing is left uncovered. Every killable mutant is dead.
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Filters\expectApplied;
use function Brain\Monkey\Functions\when;

it('registers the seven default widget areas in order with their ids, names and descriptions', function (): void {
    when('__')->returnArg();
    when('_x')->returnArg();

    $registered = [];
    Functions\expect('genesis_register_sidebar')
        ->times(7)
        ->andReturnUsing(function (array $area) use (&$registered): void {
            $registered[] = $area;
        });

    utility_pro_register_widget_areas();

    expect(array_column($registered, 'id'))->toBe([
        'utility-bar',
        'utility-home-welcome',
        'utility-home-gallery-1',
        'utility-home-gallery-2',
        'utility-home-gallery-3',
        'utility-home-gallery-4',
        'utility-call-to-action',
    ]);
    expect($registered)->toBe([
        ['id' => 'utility-bar', 'name' => 'Utility Bar', 'description' => 'This is the utility bar across the top of page.'],
        ['id' => 'utility-home-welcome', 'name' => 'Home Welcome', 'description' => 'This is the welcome section at the top of the home page.'],
        ['id' => 'utility-home-gallery-1', 'name' => 'Home Gallery 1', 'description' => 'Home Gallery 1 widget area on home page.'],
        ['id' => 'utility-home-gallery-2', 'name' => 'Home Gallery 2', 'description' => 'Home Gallery 2 widget area on home page.'],
        ['id' => 'utility-home-gallery-3', 'name' => 'Home Gallery 3', 'description' => 'Home Gallery 3 widget area on home page.'],
        ['id' => 'utility-home-gallery-4', 'name' => 'Home Gallery 4', 'description' => 'Home Gallery 4 widget area on home page.'],
        ['id' => 'utility-call-to-action', 'name' => 'Call to Action', 'description' => 'This is the Call to Action section on the home page.'],
    ]);
});

it('registers whatever list the utility_pro_default_widget_areas filter returns', function (): void {
    when('__')->returnArg();
    when('_x')->returnArg();

    $custom = [['id' => 'only-one', 'name' => 'Only', 'description' => 'Just one']];
    expectApplied('utility_pro_default_widget_areas')->once()->andReturn($custom);
    Functions\expect('genesis_register_sidebar')->once()->with($custom[0]);

    utility_pro_register_widget_areas();
});

it('registers nothing when the filter returns something other than a list', function (): void {
    when('__')->returnArg();
    when('_x')->returnArg();

    expectApplied('utility_pro_default_widget_areas')->once()->andReturn('none');
    Functions\expect('genesis_register_sidebar')->never();

    utility_pro_register_widget_areas();
});

it('skips a list entry that is not an area', function (): void {
    when('__')->returnArg();
    when('_x')->returnArg();

    $area = ['id' => 'only-one', 'name' => 'Only', 'description' => 'Just one'];
    expectApplied('utility_pro_default_widget_areas')->once()->andReturn(['not an area', $area]);
    Functions\expect('genesis_register_sidebar')->once()->with($area);

    utility_pro_register_widget_areas();
});

it('hands the filter the default list of seven areas', function (): void {
    when('__')->returnArg();
    when('_x')->returnArg();
    when('genesis_register_sidebar')->justReturn();

    expectApplied('utility_pro_default_widget_areas')
        ->once()
        ->with(Mockery::on(fn (array $areas): bool => count($areas) === 7));

    utility_pro_register_widget_areas();
});
