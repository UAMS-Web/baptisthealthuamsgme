# Integration suite

Boots WordPress through `wp-phpunit` with this theme active over the real Genesis parent theme, and asserts the hooks the theme registers and that Genesis fires them. It is PHPUnit 9.6 in its own Composer setup, because WordPress' test suite supports PHPUnit 9 only and the root runs Pest on PHPUnit 13.

It runs locally. CI runs the Unit suite only (`composer test:unit`): Genesis is a commercial theme with no public download, and the shared chain has no MySQL job. Whether CI can run it is the follow-up measured on `UAMS-Web/baptisthealthuamsgme#66`.

## Run it

1. Check this theme out in `wp-content/themes/` of a WordPress install that also has Genesis at `wp-content/themes/genesis`.
2. Create an empty database the suite may wipe: `mysql -u root -e 'CREATE DATABASE baptisthealthuamsgme_tests'`. The bootstrap refuses to run when the name matches the install's own `DB_NAME`.
3. From the repository root, once: `composer setup:integration`.
4. `composer test:integration`.

Override the defaults with `WP_TESTS_ABSPATH`, `WP_TESTS_DB_NAME`, `WP_TESTS_DB_USER`, `WP_TESTS_DB_PASSWORD` and `WP_TESTS_DB_HOST`.
