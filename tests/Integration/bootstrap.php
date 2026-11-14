<?php

/**
 * Integration bootstrap for a Genesis child theme.
 *
 * Boots WordPress through wp-phpunit with this checkout as the active theme and
 * the real Genesis parent theme beside it in the same themes directory (the suite
 * asserts against Genesis' own hooks, so a stand-in would only restate the theme's
 * registrations). Like the Unit suite's, it loads this directory's own Composer
 * setup, never the root's.
 */

declare(strict_types=1);

if (! is_readable(__DIR__.'/vendor/autoload.php')) {
    fwrite(
        STDERR,
        PHP_EOL."Refusing to run: the Integration suite's own Composer setup is not installed.".PHP_EOL
        .'Run `composer setup:integration` (or `composer install` in tests/Integration) first.'.PHP_EOL.PHP_EOL
    );

    exit(1);
}

require_once __DIR__.'/vendor/autoload.php';

$wordpress = rtrim((string) (getenv('WP_TESTS_ABSPATH') ?: dirname(__DIR__, 5)), '/').'/';

putenv('WP_TESTS_ABSPATH='.$wordpress);

if (! is_readable($wordpress.'wp-settings.php')) {
    fwrite(
        STDERR,
        PHP_EOL."Refusing to run: no WordPress at {$wordpress}.".PHP_EOL
        .'Check this theme out inside a WordPress install, or set WP_TESTS_ABSPATH to one.'.PHP_EOL.PHP_EOL
    );

    exit(1);
}

$themeDirectory = dirname(__DIR__, 2);
$themeRoot = dirname($themeDirectory);

if (! is_readable($themeRoot.'/genesis/style.css')) {
    fwrite(
        STDERR,
        PHP_EOL."Refusing to run: the Genesis parent theme is not at {$themeRoot}/genesis.".PHP_EOL
        .'The suite asserts against real Genesis; install it beside this theme.'.PHP_EOL.PHP_EOL
    );

    exit(1);
}

$testDatabase = (string) (getenv('WP_TESTS_DB_NAME') ?: 'baptisthealthuamsgme_tests');
$liveConfig = $wordpress.'wp-config.php';

if (is_readable($liveConfig)) {
    $matched = preg_match(
        '/define\(\s*[\'"]DB_NAME[\'"]\s*,\s*[\'"]([^\'"]*)[\'"]/',
        (string) file_get_contents($liveConfig),
        $liveDatabase
    );

    if ($matched === 1 && $liveDatabase[1] === $testDatabase) {
        fwrite(
            STDERR,
            PHP_EOL."Refusing to run: WP_TESTS_DB_NAME is '{$testDatabase}', the database the".PHP_EOL
            ."WordPress install at {$wordpress} uses. The test installer drops every table it".PHP_EOL
            .'finds. Point WP_TESTS_DB_NAME at a database you can afford to lose.'.PHP_EOL.PHP_EOL
        );

        exit(1);
    }
}

define('WP_TESTS_CONFIG_FILE_PATH', __DIR__.'/wp-tests-config.php');

/*
 * The theme registers its license page and plugin installer only in the admin, so the whole run is an
 * admin request. That is the only way the load-time registrations in includes/suggested-plugins.php and
 * includes/theme-license.php exist to be asserted.
 */
define('WP_ADMIN', true);

$wpPhpunit = (string) getenv('WP_PHPUNIT__DIR');

require_once $wpPhpunit.'/includes/functions.php';

tests_add_filter('muplugins_loaded', static function () use ($themeDirectory, $themeRoot): void {
    $theme = basename($themeDirectory);

    register_theme_directory($themeRoot);
    add_filter('theme_root', static fn (): string => $themeRoot);
    add_filter('pre_option_template', static fn (): string => 'genesis');
    add_filter('pre_option_stylesheet', static fn (): string => $theme);
});

require $wpPhpunit.'/includes/bootstrap.php';

// cspell:ignore ABSPATH fwrite getenv muplugins preg putenv rtrim stylesheet
