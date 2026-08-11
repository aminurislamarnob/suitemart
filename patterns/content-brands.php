<?php
/**
 * Title: Brand logo strip
 * Slug: suitemart/content-brands
 * Categories: suitemart/content, featured
 * Description: A muted row of brand logos that regain colour on hover.
 * Keywords: brands, logos, partners, stockists, clients
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:paragraph {"align":"center","fontSize":"sm","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em"}}} -->
<p class="has-text-align-center has-sm-font-size" style="text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'Brands we stock', 'Pattern heading', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<?php // Placeholder images are deliberately omitted: an editor picks real logos, and shipping stand-ins would only invite them to be published by mistake. ?>
<!-- wp:suitemart/brands {"columns":6,"columnsMobile":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<!-- wp:image {"sizeSlug":"medium"} -->
<figure class="wp-block-image size-medium"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"medium"} -->
<figure class="wp-block-image size-medium"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"medium"} -->
<figure class="wp-block-image size-medium"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"medium"} -->
<figure class="wp-block-image size-medium"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"medium"} -->
<figure class="wp-block-image size-medium"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"medium"} -->
<figure class="wp-block-image size-medium"><img alt=""/></figure>
<!-- /wp:image -->
<!-- /wp:suitemart/brands --></div>
<!-- /wp:group -->
