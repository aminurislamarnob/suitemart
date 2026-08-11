<?php
/**
 * Suitemart theme bootstrap.
 *
 * Defines theme constants, verifies the runtime meets the supported floor, and
 * loads the theme's modules. Nothing else belongs in this file.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// Support floor — decision 19. Keep in sync with style.css and readme.txt.
define( 'SUITEMART_VERSION', '0.1.0' );
define( 'SUITEMART_MIN_WP', '6.7' );
define( 'SUITEMART_MIN_PHP', '8.1' );
define( 'SUITEMART_MIN_WC', '9.0' );

define( 'SUITEMART_DIR', untrailingslashit( get_template_directory() ) );
define( 'SUITEMART_URI', untrailingslashit( get_template_directory_uri() ) );

require_once SUITEMART_DIR . '/inc/compat.php';

// If the environment is below the supported floor, show an admin notice and load
// nothing else. A half-loaded theme is worse than an inert one.
if ( ! suitemart_environment_is_supported() ) {
	add_action( 'admin_notices', 'suitemart_render_unsupported_notice' );
	return;
}

require_once SUITEMART_DIR . '/inc/setup.php';
require_once SUITEMART_DIR . '/inc/enqueue.php';
require_once SUITEMART_DIR . '/inc/blocks/helpers.php';
require_once SUITEMART_DIR . '/inc/blocks/register.php';
require_once SUITEMART_DIR . '/inc/patterns.php';
require_once SUITEMART_DIR . '/inc/post-types/portfolio.php';
require_once SUITEMART_DIR . '/inc/integrations/load.php';
