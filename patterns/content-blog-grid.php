<?php
/**
 * Title: Blog grid, three across
 * Slug: suitemart/content-blog-grid
 * Categories: suitemart/content, posts, query
 * Description: The three most recent posts as cards, with a link through to the archive.
 * Keywords: blog, posts, news, journal, grid, query
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group" style="margin-bottom:var(--wp--preset--spacing--50)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'From the journal', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"sm"} -->
<p class="has-sm-font-size"><a href="/blog"><?php echo esc_html_x( 'All posts', 'Pattern link text', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<?php
/*
 * `inherit: false` on purpose. Inheriting the main query would make this
 * section show search results on a search page and the archive on an archive;
 * a "latest three" band should say the same thing wherever it is placed.
 */
?>
<!-- wp:query {"queryId":0,"query":{"perPage":3,"pages":1,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","style":{"border":{"radius":"var:custom|radius|md"}}} /-->

<!-- wp:post-terms {"term":"category","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.06em"},"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"fontSize":"sm"} /-->

<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"lg"} /-->

<!-- wp:post-excerpt {"excerptLength":22,"showMoreOnNewLine":false,"fontSize":"sm"} /-->

<!-- wp:post-date {"textColor":"neutral-600","fontSize":"sm"} /-->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"textColor":"neutral-600"} -->
<p class="has-neutral-600-color has-text-color"><?php echo esc_html_x( 'Posts will appear here once you publish one.', 'Pattern empty state text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
