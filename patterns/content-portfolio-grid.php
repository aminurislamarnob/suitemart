<?php
/**
 * Title: Filterable portfolio
 * Slug: suitemart/content-portfolio-grid
 * Categories: suitemart/content, portfolio
 * Description: A heading and a grid of projects with category filters that work without reloading the page.
 * Keywords: portfolio, projects, work, grid, filter
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
<h2 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'Selected work', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<?php
/*
 * Card titles are h3 because the heading above them is an h2. Move the level
 * with the pattern if you put it under a different one.
 */
?>
<!-- wp:suitemart/portfolio-grid {"postsToShow":12,"columns":3,"headingLevel":3,"label":"<?php echo esc_attr_x( 'Selected work', 'Pattern label', 'suitemart' ); ?>"} /-->
</div>
<!-- /wp:group -->
