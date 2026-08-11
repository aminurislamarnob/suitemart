<?php
/**
 * Title: Product information tabs
 * Slug: suitemart/content-product-tabs
 * Categories: suitemart/content, text
 * Description: Tabbed panels for description, delivery and returns information.
 * Keywords: tabs, description, delivery, returns, faq
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:suitemart/tabs {"orientation":"horizontal","activation":"automatic"} -->
<!-- wp:suitemart/tab {"label":"<?php echo esc_attr_x( 'Description', 'Pattern tab label', 'suitemart' ); ?>","icon":"list"} -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Describe the product here. This panel accepts any blocks, so you can add images, tables or columns alongside the text.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/tab -->

<!-- wp:suitemart/tab {"label":"<?php echo esc_attr_x( 'Delivery', 'Pattern tab label', 'suitemart' ); ?>","icon":"truck"} -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Explain delivery options, costs and expected timescales.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/tab -->

<!-- wp:suitemart/tab {"label":"<?php echo esc_attr_x( 'Returns', 'Pattern tab label', 'suitemart' ); ?>","icon":"refresh-cw"} -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Set out the returns window and how a customer starts a return.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:suitemart/tab -->
<!-- /wp:suitemart/tabs -->
