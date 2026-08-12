<?php
/**
 * Title: Live viewer count
 * Slug: suitemart/commerce-visitor-count
 * Categories: suitemart/commerce, woocommerce
 * Description: Shows how many people are viewing a product. Simulates a figure unless you connect a real source — see the suitemart_visitor_count filter.
 * Keywords: visitors, viewers, social proof, live
 * Viewport Width: 720
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! suitemart_has_woocommerce() ) {
	return;
}

/*
 * Deliberately its own pattern rather than part of the urgency stack. Without a
 * data source behind `suitemart_visitor_count` this block states a number
 * nobody measured, and several jurisdictions treat invented social proof as a
 * deceptive practice — so adding it should be a decision, not a side effect of
 * inserting something else.
 */

?>
<!-- wp:suitemart/visitor-counter {"minVisitors":20,"maxVisitors":50} /-->
