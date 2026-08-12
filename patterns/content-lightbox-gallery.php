<?php
/**
 * Title: Gallery with lightbox
 * Slug: suitemart/content-lightbox-gallery
 * Categories: suitemart/content, gallery
 * Description: A gallery whose images open full screen, with arrows to move between them.
 * Keywords: gallery, lightbox, zoom, photos, full screen
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * `linkTo: media` is the part that matters and the part that is easy to lose:
 * the lightbox only acts on images that link to their own file, so a gallery
 * set to link nowhere sits inside the block doing nothing. Images are left for
 * an editor to choose, as in the other gallery patterns.
 */
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'In the workshop', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:suitemart/lightbox {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<!-- wp:gallery {"columns":3,"linkTo":"media"} -->
<figure class="wp-block-gallery has-nested-images columns-3 is-cropped"><!-- wp:image {"linkDestination":"media"} -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"linkDestination":"media"} -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"linkDestination":"media"} -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"linkDestination":"media"} -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"linkDestination":"media"} -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"linkDestination":"media"} -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image --></figure>
<!-- /wp:gallery -->
<!-- /wp:suitemart/lightbox --></div>
<!-- /wp:group -->
