<?php
/**
 * Title: Frequently asked questions
 * Slug: suitemart/content-faq
 * Categories: suitemart/content, text
 * Description: A stack of expandable questions, one open to show what a reader gets.
 * Keywords: faq, questions, answers, accordion, help, support
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * core/details rather than a Suitemart accordion: the browser owns the open
 * state, the summary is already a button to a screen reader, and find-in-page
 * reaches text inside a closed panel. Decision 4 says core for primitives.
 */
$sm_faqs = array(
	array(
		'q' => _x( 'How long does delivery take?', 'Pattern question', 'suitemart' ),
		'a' => _x( 'Orders placed before 3pm are dispatched the same working day and usually arrive within three days. You will get a tracking link as soon as the parcel leaves us.', 'Pattern answer', 'suitemart' ),
	),
	array(
		'q' => _x( 'Can I return something that does not fit?', 'Pattern question', 'suitemart' ),
		'a' => _x( 'Yes — anything unworn can go back within thirty days. Start the return from your account page and we will email you a label.', 'Pattern answer', 'suitemart' ),
	),
	array(
		'q' => _x( 'Do you ship outside the country?', 'Pattern question', 'suitemart' ),
		'a' => _x( 'We ship to twelve countries. Duties are calculated at checkout so nothing is charged on arrival.', 'Pattern answer', 'suitemart' ),
	),
	array(
		'q' => _x( 'What payment methods can I use?', 'Pattern question', 'suitemart' ),
		'a' => _x( 'Every method WooCommerce supports, including the wallets your phone already has set up.', 'Pattern answer', 'suitemart' ),
	),
	array(
		'q' => _x( 'Is my order covered by a guarantee?', 'Pattern question', 'suitemart' ),
		'a' => _x( 'Two years against manufacturing faults, on top of your statutory rights. Send us a photograph and we will sort it out.', 'Pattern answer', 'suitemart' ),
	),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Questions we get asked', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)">
<?php foreach ( $sm_faqs as $sm_index => $sm_faq ) : ?>
<!-- wp:details <?php echo 0 === $sm_index ? '{"showContent":true,' : '{'; ?>"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"border":{"width":"1px","radius":"var:custom|radius|md"}},"borderColor":"neutral-200"} -->
<details class="wp-block-details has-border-color has-neutral-200-border-color" style="border-width:1px;border-radius:var(--wp--custom--radius--md);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"<?php echo 0 === $sm_index ? ' open' : ''; ?>><summary><?php echo esc_html( $sm_faq['q'] ); ?></summary><!-- wp:paragraph {"textColor":"neutral-700"} -->
<p class="has-neutral-700-color has-text-color"><?php echo esc_html( $sm_faq['a'] ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<?php endforeach; ?>
</div>
<!-- /wp:group --></div>
<!-- /wp:group -->
