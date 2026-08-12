<?php
/**
 * Title: Delivery estimate and size guide
 * Slug: suitemart/commerce-product-assurance
 * Categories: suitemart/commerce, woocommerce
 * Description: The reassurance row for a product page — when it arrives, and how to pick a size.
 * Keywords: delivery, shipping, size, guide, measurements
 * Viewport Width: 720
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! suitemart_has_woocommerce() ) {
	return;
}

?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group"><!-- wp:suitemart/estimated-delivery {"minDays":3,"maxDays":5} /-->

<!-- wp:suitemart/size-guide-button /-->

<!-- wp:suitemart/size-guide {"title":"<?php echo esc_attr_x( 'Size guide', 'Pattern modal title', 'suitemart' ); ?>"} -->
<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Measurements are taken flat, in centimetres. If you are between two sizes, we suggest the larger one.', 'Pattern text', 'suitemart' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:table -->
<figure class="wp-block-table"><table><thead><tr><th><?php echo esc_html_x( 'Size', 'Pattern table heading', 'suitemart' ); ?></th><th><?php echo esc_html_x( 'Chest', 'Pattern table heading', 'suitemart' ); ?></th><th><?php echo esc_html_x( 'Length', 'Pattern table heading', 'suitemart' ); ?></th></tr></thead><tbody><tr><td>S</td><td>48</td><td>68</td></tr><tr><td>M</td><td>51</td><td>71</td></tr><tr><td>L</td><td>54</td><td>74</td></tr><tr><td>XL</td><td>57</td><td>77</td></tr></tbody></table></figure>
<!-- /wp:table -->
<!-- /wp:suitemart/size-guide --></div>
<!-- /wp:group -->
