<?php

/**
 * Unit tests for Utility_Pro_Search_Form in includes/class-search-form.php.
 *
 * Mutation (macOS, working evidence only): 26 mutants tested, all killed.
 */

declare(strict_types=1);

use function Brain\Monkey\Filters\expectApplied;
use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;

beforeEach(function (): void {
    stubTranslationFunctions();
    stubEscapeFunctions();
    when('wp_parse_args')->alias(fn (array $args, array $defaults): array => array_merge($defaults, $args));
    when('home_url')->justReturn('https://example.test/');
    when('get_search_query')->justReturn('');
});

it('builds a search form posting to the site root', function (): void {
    $html = (new Utility_Pro_Search_Form)->get_form();

    expect($html)
        ->toStartWith('<form method="get" action="https://example.test/" role="search" class="search-form">')
        ->toEndWith('</form>')
        ->toContain('<input type="search" name="s"')
        ->toContain('<button type="submit" aria-label="Search"><span class="search-button-text">Search</span></button>');
});

it('labels the input with the same id the input carries', function (): void {
    $html = (new Utility_Pro_Search_Form)->get_form();

    preg_match('/<label for="([^"]+)" class="screen-reader-text">Search site<\/label>/', $html, $label);
    preg_match('/<input [^>]*id="([^"]+)"/', $html, $input);

    expect($label)->toHaveCount(2)
        ->and($input)->toHaveCount(2)
        ->and($label[1])->toStartWith('searchform-')
        ->and($input[1])->toBe($label[1]);
});

it('gives each form instance its own id', function (): void {
    preg_match('/for="([^"]+)"/', (new Utility_Pro_Search_Form)->get_form(), $first);
    preg_match('/for="([^"]+)"/', (new Utility_Pro_Search_Form)->get_form(), $second);

    expect($first[1])->not->toBe($second[1]);
});

it('keeps one id across repeated calls on the same instance', function (): void {
    $form = new Utility_Pro_Search_Form;

    expect($form->get_form())->toBe($form->get_form());
});

it('leaves the placeholder empty', function (): void {
    expect((new Utility_Pro_Search_Form)->get_form())->toContain('placeholder=""');
});

it('prefills the input with the filtered search query', function (): void {
    when('get_search_query')->justReturn('heart');
    expectApplied('the_search_query')->once()->with('heart')->andReturn('heart care');

    expect((new Utility_Pro_Search_Form)->get_form())->toContain('value="heart care"');
});

it('leaves the value empty when there is no search query', function (): void {
    expect((new Utility_Pro_Search_Form)->get_form())->toContain('value=""');
});

it('lets custom strings override the defaults', function (): void {
    $html = (new Utility_Pro_Search_Form([
        'label' => 'Find',
        'placeholder' => 'Type here',
        'button' => 'Go',
        'button_label' => 'Go search',
    ]))->get_form();

    expect($html)
        ->toContain('>Find</label>')
        ->toContain('placeholder="Type here"')
        ->toContain('aria-label="Go search"><span class="search-button-text">Go</span>');
});

it('runs the Genesis filters over the default strings', function (): void {
    expectApplied('genesis_search_form_label')->once()->with('Search site')->andReturn('Site search');
    expectApplied('genesis_search_text')->once()->with('')->andReturn('Query');
    expectApplied('genesis_search_button_text')->once()->with('Search')->andReturn('Look');
    expectApplied('genesis_search_button_label')->once()->with('Search')->andReturn('Look up');

    expect((new Utility_Pro_Search_Form)->get_form())
        ->toContain('>Site search</label>')
        ->toContain('placeholder="Query"')
        ->toContain('aria-label="Look up"><span class="search-button-text">Look</span>');
});

it('renders the label, then the input, then the button', function (): void {
    $html = (new Utility_Pro_Search_Form)->get_form();

    $label = strpos($html, '<label');
    $input = strpos($html, '<input');
    $button = strpos($html, '<button');

    expect($label)->toBeInt()
        ->and($input)->toBeGreaterThan($label)
        ->and($button)->toBeGreaterThan($input);
});

it('builds the id from the searchform prefix and a more-entropy uniqid', function (): void {
    preg_match('/for="([^"]+)"/', (new Utility_Pro_Search_Form)->get_form(), $id);

    expect($id[1])->toMatch('/^searchform-[0-9a-f]{14}\.[0-9]+$/');
});

it('returns one string by key, or every string for all and for an unknown key', function (): void {
    $form = new class(['label' => 'Find']) extends Utility_Pro_Search_Form
    {
        public function read(string $key = 'all'): string|array
        {
            return $this->strings($key);
        }
    };

    expect($form->read('label'))->toBe('Find')
        ->and($form->read())->toHaveKeys(['label', 'placeholder', 'button', 'button_label'])
        ->and($form->read('all'))->toBeArray()
        ->and($form->read('missing'))->toBeArray()->toHaveKey('label');
});
