<?php
/**
 * Title: Share row
 * Slug: suitemart/content-share-row
 * Categories: suitemart/content, text
 * Description: A labelled row of share links, sized for the end of a post or product description.
 * Keywords: share, social, post, article, product
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"},"border":{"top":{"color":"var:preset|color|neutral-200","width":"1px","style":"solid"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--neutral-200);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:suitemart/social-share {"networks":["facebook","x","linkedin","whatsapp","email","copy"],"shape":"circle","heading":"<?php echo esc_attr_x( 'Share this', 'Pattern heading', 'suitemart' ); ?>"} /--></div>
<!-- /wp:group -->
