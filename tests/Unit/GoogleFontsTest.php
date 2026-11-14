<?php

/**
 * Unit tests for includes/google-fonts.php.
 *
 * The translators' "on"/"off" switches are WordPress `_x()` calls; each test
 * answers them per font so every branch of the URL builder runs.
 *
 * Mutation (macOS, working evidence only): 13 mutants tested, all killed. One
 * mutant is uncovered and cannot be reached from the Unit layer: removing the
 * file-scope `add_action('wp_enqueue_scripts', ...)` registration. The Unit
 * bootstrap loads the file inside a Brain Monkey session that is discarded, so no
 * test runs that line, and a `@pest-mutate-ignore` above a statement cannot
 * suppress a mutator that targets the statement itself. Every killable mutant is
 * dead.
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\when;

/**
 * Answer the two `_x()` font switches.
 */
function fonts_switches(string $enriqueta, string $openSans): void
{
    when('_x')->alias(
        fn (string $text, string $context): string => str_contains($context, 'Enriqueta') ? $enriqueta : $openSans,
    );
}

it('builds the URL for both fonts with each family and the subsets encoded', function (): void {
    fonts_switches('on', 'on');

    Functions\expect('add_query_arg')
        ->once()
        ->with(
            [
                'family' => 'Enriqueta%3A400%2C700%7COpen+Sans%3A400%2C700',
                'subset' => 'latin%2Clatin-ext',
            ],
            '//fonts.googleapis.com/css',
        )
        ->andReturn('https://fonts.example/css');

    expect(utility_pro_fonts_url())->toBe('https://fonts.example/css');
});

it('leaves out a font whose switch is off', function (string $enriqueta, string $openSans, string $family): void {
    fonts_switches($enriqueta, $openSans);

    Functions\expect('add_query_arg')
        ->once()
        ->with(
            ['family' => $family, 'subset' => 'latin%2Clatin-ext'],
            '//fonts.googleapis.com/css',
        )
        ->andReturn('url');

    expect(utility_pro_fonts_url())->toBe('url');
})->with([
    'Enriqueta off' => ['off', 'on', 'Open+Sans%3A400%2C700'],
    'Open Sans off' => ['on', 'off', 'Enriqueta%3A400%2C700'],
]);

it('returns an empty string and builds no URL when both fonts are off', function (): void {
    fonts_switches('off', 'off');

    Functions\expect('add_query_arg')->never();

    expect(utility_pro_fonts_url())->toBe('');
});

it('enqueues the fonts stylesheet from the built URL', function (): void {
    fonts_switches('on', 'on');
    when('add_query_arg')->justReturn('https://fonts.example/css');

    Functions\expect('wp_enqueue_style')
        ->once()
        ->with('utility-pro-fonts', 'https://fonts.example/css', [], null);

    utility_pro_enqueue_fonts();
});
