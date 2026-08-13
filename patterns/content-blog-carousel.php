<?php
/**
 * Title: Latest posts carousel
 * Slug: suitemart/content-blog-carousel
 * Categories: suitemart/content, posts
 * Description: A heading and a carousel of the most recent posts, three at a time.
 * Keywords: blog, posts, news, carousel, journal
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:heading {"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'From the journal', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<?php
/*
 * The card titles are h3 because the heading above them is an h2. If you move
 * this pattern under a different heading, move the level with it — the outline
 * is what a screen reader navigates by.
 */
?>
<!-- wp:suitemart/post-carousel {"postsToShow":9,"headingLevel":3,"label":"<?php echo esc_attr_x( 'Latest posts', 'Pattern label', 'suitemart' ); ?>"} /-->
</div>
<!-- /wp:group -->
