<?php
/**
 * Title: What is included
 * Slug: suitemart/content-checklist
 * Categories: suitemart/content, text, services
 * Description: A tinted panel listing what comes with every order, ticked off in two columns.
 * Keywords: checklist, included, benefits, ticks, list, reassurance
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_included = array(
	_x( 'Two-year guarantee on every item', 'Pattern list item', 'suitemart' ),
	_x( 'Free returns for thirty days', 'Pattern list item', 'suitemart' ),
	_x( 'Tracked delivery, dispatched same day', 'Pattern list item', 'suitemart' ),
	_x( 'Repairs quoted before any work starts', 'Pattern list item', 'suitemart' ),
	_x( 'Packaging that goes in the recycling', 'Pattern list item', 'suitemart' ),
	_x( 'A person on the phone, not a chatbot', 'Pattern list item', 'suitemart' ),
);
?>
<!-- wp:group {"align":"wide","backgroundColor":"neutral-100","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"var:custom|radius|lg"}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide has-neutral-100-background-color has-background" style="border-radius:var(--wp--custom--radius--lg);padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--50)"><!-- wp:heading {"level":2,"fontSize":"xl"} -->
<h2 class="wp-block-heading has-xl-font-size"><?php echo esc_html_x( 'Included with every order', 'Pattern heading', 'suitemart' ); ?></h2>
<!-- /wp:heading -->

<?php
/*
 * Info boxes rather than a core list with tick characters: the icon is drawn
 * from the sprite with `currentColor` and is hidden from assistive technology,
 * so the line reads as its sentence rather than as "check mark, free returns".
 * Each item is the info box's `description`, not its `title`: a title renders as
 * a heading, and six headings in a row that are really list items is a document
 * outline nobody can navigate.
 */
?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"grid","columnCount":2,"minimumColumnWidth":"18rem"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--40)">
<?php foreach ( $sm_included as $sm_item ) : ?>
<!-- wp:suitemart/infobox {"icon":"check","iconSize":20,"description":"<?php echo esc_attr( $sm_item ); ?>","orientation":"horizontal"} /-->
<?php endforeach; ?>
</div>
<!-- /wp:group --></div>
<!-- /wp:group -->
