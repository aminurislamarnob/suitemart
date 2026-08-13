<?php
/**
 * Title: Search the catalogue
 * Slug: suitemart/commerce-search-panel
 * Categories: suitemart/commerce, woocommerce
 * Description: A wide product search with suggested categories underneath it.
 * Keywords: search, find, catalogue, products, suggestions
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! suitemart_has_woocommerce() ) {
	return;
}

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|40"}},"backgroundColor":"neutral-100","layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group alignfull has-neutral-100-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size"><?php echo esc_html_x( 'Looking for something specific?', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<?php
/*
 * `postType` is pinned to `product` rather than left at `any`, because on a
 * shop page a results list that mixes blog posts in with products reads as a
 * fault. The results panel is keyboard-navigable and announces its own count.
 */
?>
<!-- wp:suitemart/ajax-search {"postType":"product","resultLimit":6,"showImages":true,"placeholder":"<?php echo esc_attr_x( 'Search products…', 'Pattern search placeholder', 'suitemart' ); ?>","buttonText":"<?php echo esc_attr_x( 'Search', 'Pattern button text', 'suitemart' ); ?>"} /-->

<!-- wp:paragraph {"align":"center","textColor":"neutral-700","fontSize":"sm"} -->
<p class="has-text-align-center has-neutral-700-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Popular right now:', 'Pattern text', 'suitemart' ); ?> <a href="/shop"><?php echo esc_html_x( 'new arrivals', 'Pattern link text', 'suitemart' ); ?></a>, <a href="/shop"><?php echo esc_html_x( 'sale', 'Pattern link text', 'suitemart' ); ?></a>, <a href="/shop"><?php echo esc_html_x( 'gift cards', 'Pattern link text', 'suitemart' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
