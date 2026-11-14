<?php

/**
 * Pins every hook the theme registers when WordPress loads it, against real WordPress and real Genesis.
 *
 * The Unit suite proves the callbacks do the right thing and that the names are registered, with Genesis and
 * WordPress faked. It cannot prove the host ever fires a name or that a callback survives the real hook, so
 * this suite loads the theme as a Genesis child theme and asserts the registrations and a Genesis-fired filter.
 */

declare(strict_types=1);

final class HookRegistrationTest extends WP_UnitTestCase
{
    /**
     * Every registration functions.php and includes/ make, as [kind, hook, callback, priority].
     *
     * @return array<string, array{string, string, string, int}>
     */
    public static function registrations(): array
    {
        $rows = [
            ['action', 'genesis_setup', 'utility_pro_setup', 15],
            ['filter', 'genesis_footer_creds_text', 'utility_pro_footer_creds', 10],
            ['filter', 'genesis_footer_creds_text', 'sp_footer_creds_filter', 10],
            ['filter', 'genesis_author_box_gravatar_size', 'utility_pro_author_box_gravatar_size', 10],
            ['action', 'wp_enqueue_scripts', 'utility_pro_enqueue_assets', 10],
            ['action', 'wp_enqueue_scripts', 'utility_pro_enqueue_fonts', 10],
            ['filter', 'genesis_attr_nav-footer', 'utility_pro_footer_nav_schema', 10],
            ['filter', 'wp_nav_menu_args', 'utility_pro_footer_menu_args', 10],
            ['filter', 'widget_text', 'do_shortcode', 10],
            ['filter', 'theme_page_templates', 'utility_pro_remove_genesis_page_templates', 10],
            ['filter', 'genesis_attr_nav-footer', 'utility_pro_add_nav_secondary_id', 10],
            ['filter', 'genesis_skip_links_output', 'utility_pro_add_nav_secondary_skip_link', 10],
            ['action', 'genesis_before_header', 'utility_pro_add_bar', 10],
            ['filter', 'the_content', 'utility_pro_featured_image', 10],
            ['action', 'genesis_before_footer', 'utility_pro_do_footer_nav', 10],
            ['action', 'genesis_after_endwhile', 'utility_pro_post_pagination', 10],
            ['filter', 'get_search_form', 'utility_pro_get_search_form', 25],
            ['action', 'tgmpa_register', 'utility_pro_register_required_plugins', 10],
            ['action', 'admin_menu', 'utility_pro_move_license_page_menu_item', 12],
        ];

        $cases = [];

        foreach ($rows as $row) {
            $cases["{$row[1]} -> {$row[2]}"] = $row;
        }

        return $cases;
    }

    /**
     * @dataProvider registrations
     */
    public function test_the_theme_registers_the_callback_at_its_priority(string $kind, string $hook, string $callback, int $priority): void
    {
        $found = $kind === 'action' ? has_action($hook, $callback) : has_filter($hook, $callback);

        $this->assertSame($priority, $found, "{$hook} -> {$callback}");
    }

    public function test_the_child_theme_is_active_over_genesis(): void
    {
        $this->assertSame('baptisthealthuamsgme', get_stylesheet());
        $this->assertSame('genesis', get_template());
        $this->assertTrue(function_exists('genesis_markup'), 'Genesis did not load.');
    }

    public function test_genesis_fires_the_footer_navigation_filter_the_theme_hooks(): void
    {
        $markup = genesis_markup([
            'open' => '<nav %s>',
            'close' => '</nav>',
            'context' => 'nav-footer',
            'content' => '',
            'echo' => false,
        ]);

        $this->assertStringContainsString('id="genesis-nav-footer"', (string) $markup);
        $this->assertStringContainsString('itemscope', (string) $markup);
        $this->assertStringContainsString('itemtype="https://schema.org/SiteNavigationElement"', (string) $markup);
    }

    public function test_the_footer_menu_args_filter_runs_on_a_real_menu_render(): void
    {
        $args = apply_filters('wp_nav_menu_args', ['theme_location' => 'footer', 'depth' => 0, 'echo' => false]);

        $this->assertIsArray($args);
        $this->assertSame(1, $args['depth']);
    }
}
