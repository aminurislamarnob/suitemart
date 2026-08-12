<?php
/**
 * Title: Product seen from every side
 * Slug: suitemart/content-spin-view
 * Categories: suitemart/content, gallery
 * Description: A turntable sequence beside a short explanation, for products where the back matters as much as the front.
 * Keywords: 360, spin, rotate, turntable, product
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * The frames are left for an editor to choose. This pattern needs a whole
 * sequence rather than one photograph, so it is worth saying plainly where it
 * will be read: shoot the object on a turntable against an unchanging
 * background, twenty-four to thirty-six frames, and add them in order.
 */
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"},"blockGap":{"left":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%"><!-- wp:suitemart/view-360 /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Every angle, before you buy', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Spin it with a drag, or use the arrows. The joinery at the back is the part most photographs leave out.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
