<?php
/**
 * Title: Pull quote band
 * Slug: suitemart/content-pullquote
 * Categories: suitemart/content, text, testimonials
 * Description: One sentence given a full-width tinted band, with an attribution beneath it.
 * Keywords: quote, pullquote, statement, press, review
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"full","backgroundColor":"neutral-100","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-neutral-100-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"align":"center","textColor":"primary","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontWeight":"600"}},"fontSize":"sm"} -->
<p class="has-text-align-center has-primary-color has-text-color has-sm-font-size" style="font-weight:600;text-transform:uppercase;letter-spacing:0.08em"><?php echo esc_html_x( 'In the press', 'Pattern eyebrow text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<?php
/*
 * core/pullquote, not core/quote. Core's theme stylesheet draws a left rule and
 * left padding on every blockquote, and the `is-style-plain` that is supposed
 * to remove it has no CSS outside the editor — a class nobody registers styles
 * nothing, which is the same trap `is-style-none` set in the header parts. The
 * pullquote centres by default and its rules above and below suit the band.
 *
 * No `fontSize` here on purpose: the size set on the pullquote is inherited by
 * the `<cite>` as well, so an eye-catching quote drags its attribution up with
 * it. `theme.json` already sets this block to `xl`, and the citation lands at a
 * readable fraction of that.
 */
?>
<!-- wp:pullquote {"textAlign":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<figure class="wp-block-pullquote has-text-align-center" style="margin-top:var(--wp--preset--spacing--50)"><blockquote><p><?php echo esc_html_x( 'The only shop I know that will still repair something it sold you a decade ago.', 'Pattern quote', 'suitemart' ); ?></p><cite><?php echo esc_html_x( 'Replace with the publication and date', 'Pattern attribution', 'suitemart' ); ?></cite></blockquote></figure>
<!-- /wp:pullquote --></div>
<!-- /wp:group -->
