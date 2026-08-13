<?php
/**
 * Title: Video feature
 * Slug: suitemart/content-video-feature
 * Categories: suitemart/content, media
 * Description: A wide placeholder for a film, with the copy that sets it up alongside.
 * Keywords: video, film, media, embed, watch, story
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"align":"wide","verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"38%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:38%"><!-- wp:heading {"level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'Two minutes in the workshop', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-700"} -->
<p class="has-neutral-700-color has-text-color"><?php echo esc_html_x( 'Watch a piece go from rough stock to finished. It is the clearest answer we have to why things take four weeks.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<?php
/*
 * A cover standing in for the film rather than a core/embed: an embed with no
 * URL saves as an empty placeholder that shows nothing at all on the front end,
 * so the pattern would insert as a gap. Swap this whole cover for an Embed or
 * Video block once there is something to play.
 */
?>
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:cover {"overlayColor":"neutral-200","isUserOverlayColor":true,"minHeight":420,"style":{"border":{"radius":"var:custom|radius|lg"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover" style="border-radius:var(--wp--custom--radius--lg);min-height:420px"><span aria-hidden="true" class="wp-block-cover__background has-neutral-200-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","textColor":"neutral-700"} -->
<p class="has-text-align-center has-neutral-700-color has-text-color"><?php echo esc_html_x( 'Replace this with an Embed or Video block.', 'Pattern placeholder text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
