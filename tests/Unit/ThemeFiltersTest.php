<?php

/**
 * Unit tests for the pure filter callbacks in functions.php.
 */

declare(strict_types=1);

it('sets the author box gravatar size to 96 pixels whatever size comes in', function (int $incoming): void {
    expect(utility_pro_author_box_gravatar_size($incoming))->toBe(96);
})->with([0, 48, 96, 512]);

it('replaces the footer credits with the Baptist Health UAMS GME line', function (): void {
    $credits = sp_footer_creds_filter('Existing credits');

    expect($credits)
        ->toContain('Baptist Health UAMS GME')
        ->toContain('[footer_copyright]')
        ->not->toContain('Existing credits');
});
