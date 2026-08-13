<?php
/**
 * Title: Hero, three category tiles
 * Slug: suitemart/hero-category-tiles
 * Categories: suitemart/hero, suitemart/commerce, featured
 * Description: Three linked tiles that send visitors straight into a category, in place of a headline.
 * Keywords: hero, categories, tiles, banner, grid
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%">
<?php
/*
 * Each tile is a banner with no image yet: the merchant picks one in the
 * inspector, and the inner blocks stay where they are. The tall tile carries
 * the h1 because it is the first heading on the page.
 */
?>
<!-- wp:suitemart/banner {"url":"/shop","aspectRatio":"3/4","contentPosition":"bottom-left","hoverEffect":"zoom","overlayOpacity":35,"textColor":"base","style":{"border":{"radius":"var:custom|radius|lg"}}} -->
<!-- wp:heading {"level":1,"textColor":"base","fontSize":"2xl"} -->
<h1 class="wp-block-heading has-base-color has-text-color has-2-xl-font-size"><?php echo esc_html_x( 'Womenswear', 'Pattern tile heading', 'suitemart' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"sm"} -->
<p class="has-base-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'New in this week', 'Pattern tile text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/banner --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"50%","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:suitemart/banner {"url":"/shop","aspectRatio":"16/9","contentPosition":"center-left","hoverEffect":"zoom","overlayOpacity":35,"textColor":"base","style":{"border":{"radius":"var:custom|radius|lg"}}} -->
<!-- wp:heading {"level":2,"textColor":"base","fontSize":"xl"} -->
<h2 class="wp-block-heading has-base-color has-text-color has-xl-font-size"><?php echo esc_html_x( 'Menswear', 'Pattern tile heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->
<!-- /wp:suitemart/banner -->

<!-- wp:suitemart/banner {"url":"/shop","aspectRatio":"16/9","contentPosition":"center-left","hoverEffect":"zoom","overlayOpacity":35,"textColor":"base","style":{"border":{"radius":"var:custom|radius|lg"}}} -->
<!-- wp:heading {"level":2,"textColor":"base","fontSize":"xl"} -->
<h2 class="wp-block-heading has-base-color has-text-color has-xl-font-size"><?php echo esc_html_x( 'Home and living', 'Pattern tile heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->
<!-- /wp:suitemart/banner --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
