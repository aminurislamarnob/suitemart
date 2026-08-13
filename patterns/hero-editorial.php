<?php
/**
 * Title: Hero, editorial with a moving strip
 * Slug: suitemart/hero-editorial
 * Categories: suitemart/hero, banner
 * Description: A large statement headline over a strip of selling points that scrolls sideways.
 * Keywords: hero, editorial, marquee, typography, statement
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|60"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","contentSize":"860px","justifyContent":"left"}} -->
<div class="wp-block-group alignwide"><!-- wp:heading {"level":1,"fontSize":"3xl","style":{"typography":{"lineHeight":"1.05"}}} -->
<h1 class="wp-block-heading has-3-xl-font-size" style="line-height:1.05"><?php echo esc_html_x( 'A shop with a point of view, not a catalogue with a search box', 'Pattern headline', 'suitemart' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"neutral-700","fontSize":"lg"} -->
<p class="has-neutral-700-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'Everything here was chosen by someone who uses it. Nothing is listed because a distributor had spare stock.', 'Pattern supporting text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/shop"><?php echo esc_html_x( 'Start browsing', 'Pattern button text', 'suitemart' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<?php
/*
 * The strip pauses on hover and stops entirely under `prefers-reduced-motion`,
 * which is the block's job rather than the pattern's — but it is why a moving
 * element is defensible directly under the first heading on the page.
 */
?>
<!-- wp:suitemart/marquee {"align":"full","speed":40,"pauseOnHover":true,"ariaLabel":"<?php echo esc_attr_x( 'What we promise', 'Pattern label', 'suitemart' ); ?>","backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"fontSize":"lg"} -->
<!-- wp:suitemart/marquee-item -->
<p><?php echo esc_html_x( 'Free delivery over £50', 'Pattern marquee item', 'suitemart' ); ?></p>
<!-- /wp:suitemart/marquee-item -->

<!-- wp:suitemart/marquee-item -->
<p><?php echo esc_html_x( 'Thirty days to change your mind', 'Pattern marquee item', 'suitemart' ); ?></p>
<!-- /wp:suitemart/marquee-item -->

<!-- wp:suitemart/marquee-item -->
<p><?php echo esc_html_x( 'Repairs for as long as you own it', 'Pattern marquee item', 'suitemart' ); ?></p>
<!-- /wp:suitemart/marquee-item -->

<!-- wp:suitemart/marquee-item -->
<p><?php echo esc_html_x( 'Every maker named', 'Pattern marquee item', 'suitemart' ); ?></p>
<!-- /wp:suitemart/marquee-item -->
<!-- /wp:suitemart/marquee --></div>
<!-- /wp:group -->
