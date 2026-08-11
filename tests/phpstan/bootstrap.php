<?php
/**
 * PHPStan bootstrap.
 *
 * Declares theme constants that are defined at runtime in functions.php so that
 * static analysis can resolve them. Never loaded by WordPress.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

define( 'SUITEMART_VERSION', '0.1.0' );
define( 'SUITEMART_DIR', __DIR__ );
define( 'SUITEMART_URI', 'https://example.test/wp-content/themes/suitemart' );
define( 'SUITEMART_MIN_WP', '6.7' );
define( 'SUITEMART_MIN_PHP', '8.1' );
define( 'SUITEMART_MIN_WC', '9.0' );
