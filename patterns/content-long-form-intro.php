<?php
/**
 * Title: Long-form opening
 * Slug: suitemart/content-long-form-intro
 * Categories: suitemart/content, text
 * Description: A standfirst and two columns of running text, for a page that is mostly reading.
 * Keywords: article, editorial, intro, standfirst, text, columns
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading {"level":2,"fontSize":"2xl"} -->
<h2 class="wp-block-heading has-2-xl-font-size"><?php echo esc_html_x( 'Why we still make things the slow way', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<?php
/*
 * The standfirst is set at `lg` and the body at the default size, which is the
 * whole hierarchy this pattern needs. It stays at `contentSize` — 720px — on
 * purpose: a measure much wider than about 65 characters is what makes a page
 * of text tiring to read, so this is one of the few sections that should not
 * be widened.
 */
?>
<!-- wp:paragraph {"textColor":"neutral-700","fontSize":"lg"} -->
<p class="has-neutral-700-color has-text-color has-lg-font-size"><?php echo esc_html_x( 'A batch takes four weeks, which is about three and a half weeks longer than it needs to. Here is what happens in that time, and why we have never found a way to cut it that we were willing to live with.', 'Pattern standfirst', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:separator {"className":"is-style-wide"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Replace this column with your own writing. Two paragraphs of roughly this length keep the two sides even, which is what makes the split read as deliberate rather than as an accident of length.', 'Pattern placeholder text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'On a narrow screen the columns stack, so nothing here can depend on sitting beside the other side.', 'Pattern placeholder text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php echo esc_html_x( 'The second column carries on the same thought. If you only have enough to say for one column, delete the other rather than padding it out.', 'Pattern placeholder text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'End on the thing you want remembered. A closing line does more work here than a heading would.', 'Pattern placeholder text', 'suitemart' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
