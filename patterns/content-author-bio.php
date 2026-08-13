<?php
/**
 * Title: Author bio card
 * Slug: suitemart/content-author-bio
 * Categories: suitemart/content, about, posts
 * Description: A short biography card for the end of a post, with links to share it.
 * Keywords: author, bio, byline, about, post, writer
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|40"},"border":{"width":"1px","radius":"var:custom|radius|md"}},"borderColor":"neutral-200","layout":{"type":"default"}} -->
<div class="wp-block-group has-border-color has-neutral-200-border-color" style="border-width:1px;border-radius:var(--wp--custom--radius--md);padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
<?php
/*
 * The team-member block rather than core/post-author: this card is meant for a
 * page as often as a post, and a post-author block on a page renders whoever
 * happens to own the page. Swap it for post-author where the byline should
 * follow the post.
 */
?>
<!-- wp:suitemart/team-member {"name":"<?php echo esc_attr_x( 'Replace with the author', 'Pattern name', 'suitemart' ); ?>","role":"<?php echo esc_attr_x( 'Workshop manager', 'Pattern role', 'suitemart' ); ?>","bio":"<?php echo esc_attr_x( 'Writes here about materials, repairs and why some things are worth mending. Fifteen years on the bench before taking over the workshop.', 'Pattern biography', 'suitemart' ); ?>","imageShape":"circle","alignment":"start"} /-->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"},"border":{"top":{"color":"var:preset|color|neutral-200","width":"1px","style":"solid"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--neutral-200);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"textColor":"neutral-600","fontSize":"sm"} -->
<p class="has-neutral-600-color has-text-color has-sm-font-size"><?php echo esc_html_x( 'Found this useful?', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:suitemart/social-share {"networks":["facebook","x","linkedin","email","copy"],"shape":"circle","iconSize":18} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
