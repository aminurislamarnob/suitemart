<?php
/**
 * Seeds the variable product the gallery's variation spec runs against.
 *
 * Two colours, each carrying one of the product's two gallery images, so a
 * selection has a visible and checkable effect: choosing the second colour must
 * move the gallery to the second slide.
 *
 * Run through `wp eval-file`, which executes it inside eval() — where
 * `declare( strict_types=1 )` is a parse error. That is why this file, like
 * tools/seed-demo-products.php, does not declare it.
 *
 * Prints the product id, which tests/e2e/global-setup.js reads.
 *
 * @package Suitemart
 */

$sm_slug     = 'suitemart-variable-product';
$sm_existing = get_page_by_path( $sm_slug, OBJECT, 'product' );
$sm_product  = $sm_existing ? new WC_Product_Variable( $sm_existing->ID ) : new WC_Product_Variable();

$sm_product->set_name( 'Suitemart variable product' );
$sm_product->set_slug( $sm_slug );
$sm_product->set_status( 'publish' );

$sm_attribute = new WC_Product_Attribute();
$sm_attribute->set_name( 'Color' );
$sm_attribute->set_options( array( 'Blue', 'Orange' ) );
$sm_attribute->set_visible( true );
$sm_attribute->set_variation( true );

$sm_product->set_attributes( array( $sm_attribute ) );
$sm_product_id = $sm_product->save();

// The two fixture images the simple product already imported.
$sm_images = get_posts(
	array(
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'numberposts'    => 2,
		'orderby'        => 'ID',
		'order'          => 'ASC',
		'fields'         => 'ids',
	)
);

if ( count( $sm_images ) < 2 ) {
	WP_CLI::error( 'The variable product fixture needs two images; the simple product should have imported them first.' );
}

$sm_product->set_image_id( $sm_images[0] );
$sm_product->set_gallery_image_ids( array( $sm_images[1] ) );
$sm_product->save();

// Rebuild the variations from scratch, so re-running cannot accumulate them.
foreach ( $sm_product->get_children() as $sm_child_id ) {
	wp_delete_post( $sm_child_id, true );
}

$sm_map = array(
	'Blue'   => $sm_images[0],
	'Orange' => $sm_images[1],
);

foreach ( $sm_map as $sm_value => $sm_image_id ) {
	$sm_variation = new WC_Product_Variation();
	$sm_variation->set_parent_id( $sm_product_id );
	$sm_variation->set_attributes( array( 'color' => $sm_value ) );
	$sm_variation->set_regular_price( '25' );
	$sm_variation->set_image_id( $sm_image_id );
	$sm_variation->save();
}

WC_Product_Variable::sync( $sm_product_id );

WP_CLI::line( (string) $sm_product_id );
