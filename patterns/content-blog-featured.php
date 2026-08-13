<?php
/**
 * Title: Blog, one lead story
 * Slug: suitemart/content-blog-featured
 * Categories: suitemart/content, posts, query
 * Description: The newest post given a full-width card, with the next three listed beside it.
 * Keywords: blog, posts, featured, news, editorial, query
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} -->
<h2 class="wp-block-heading" style="margin-bottom:var(--wp--preset--spacing--50)"><?php echo esc_html_x( 'Reading', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"width":"58%"} -->
<div class="wp-block-column" style="flex-basis:58%"><!-- wp:query {"queryId":0,"query":{"perPage":1,"pages":1,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":"var:custom|radius|md"}}} /-->

<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}},"fontSize":"xl"} /-->

<!-- wp:post-excerpt {"excerptLength":40} /-->

<!-- wp:post-date {"textColor":"neutral-600","fontSize":"sm"} /-->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"textColor":"neutral-600"} -->
<p class="has-neutral-600-color has-text-color"><?php echo esc_html_x( 'Your newest post will lead this section.', 'Pattern empty state text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:column -->

<?php
/*
 * The second query offsets by one so the lead story is not repeated beside
 * itself. Change `perPage` on the first and this `offset` has to follow.
 */
?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:query {"queryId":0,"query":{"perPage":3,"pages":1,"offset":1,"postType":"post","order":"desc","orderBy":"date","inherit":false},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"default"}} -->
<!-- wp:group {"style":{"spacing":{"padding":{"bottom":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|10"},"border":{"bottom":{"color":"var:preset|color|neutral-200","width":"1px","style":"solid"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="border-bottom-color:var(--wp--preset--color--neutral-200);border-bottom-style:solid;border-bottom-width:1px;padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:post-title {"isLink":true,"fontSize":"md"} /-->

<!-- wp:post-date {"textColor":"neutral-600","fontSize":"sm"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"textColor":"neutral-600","fontSize":"sm"} -->
<p class="has-neutral-600-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Older posts will be listed here.', 'Pattern empty state text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
