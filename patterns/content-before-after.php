<?php
/**
 * Title: Before and after
 * Slug: suitemart/content-before-after
 * Categories: suitemart/content, gallery
 * Description: Two photographs of the same thing with a handle that wipes between them, under a short explanation.
 * Keywords: before, after, comparison, restoration, transformation
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * The two photographs are left for an editor to supply, as in the brands and
 * lookbook patterns. This one has a further requirement worth stating where it
 * will be read: the pair must be shot from the same place at the same size, or
 * the wipe reveals a jump rather than a change.
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'See the difference', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><?php echo esc_html_x( 'Drag the handle, or focus it and use the arrow keys.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:suitemart/compare-images {"beforeLabel":"<?php echo esc_attr_x( 'Before', 'Pattern text', 'suitemart' ); ?>","afterLabel":"<?php echo esc_attr_x( 'After', 'Pattern text', 'suitemart' ); ?>","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} /--></div>
<!-- /wp:group -->
