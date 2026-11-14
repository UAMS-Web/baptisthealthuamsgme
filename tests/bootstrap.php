<?php

/**
 * Unit bootstrap for a theme.
 *
 * The plugin template's tests/bootstrap.php with one difference: it loads
 * functions.php, which a theme cannot avoid, instead of an includes/ directory
 * of definitions only. The Integration suite does not come through here; it has
 * its own Composer setup and bootstrap under tests/Integration.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

require_once __DIR__.'/../vendor/antecedent/patchwork/Patchwork.php';

if (! defined('ABSPATH')) {
    define('ABSPATH', sys_get_temp_dir().'/baptisthealthuamsgme-tests-no-wordpress/');
}

/*
 * functions.php is not loaded here. load_theme() in tests/Support/load-theme.php loads it inside the first
 * test that runs, so Pest attributes its file-scope lines to a test and a mutation run can cover them.
 */

// cspell:ignore ABSPATH stylesheet
