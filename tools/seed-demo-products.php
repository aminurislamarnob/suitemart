<?php
/**
 * Seeds a development store with demo products.
 *
 * WooCommerce ships no products, so a fresh wp-env has nothing for the theme's
 * commerce blocks to render: the wishlist, the comparison table, the product
 * grid and the shop templates all look correct and empty. This creates a
 * catalogue big enough to see them behave — several categories, sale prices,
 * out-of-stock and low-stock items, and products with and without reviews.
 *
 * Run it through WP-CLI:
 *
 *     npx wp-env run cli --env-cwd=wp-content/themes/suitemart \
 *         wp eval-file tools/seed-demo-products.php
 *
 * It is idempotent: products are matched by SKU, so running it twice updates
 * rather than duplicates, and images already in the media library are reused.
 *
 * Photographs come from Unsplash under the Unsplash Licence, which permits
 * commercial use without attribution. They are development fixtures only and
 * are never committed or shipped: decision 10 requires the theme's own imagery
 * to be original, and several of these photographs show real products whose
 * trade dress belongs to their makers.
 *
 * @package Suitemart
 */

/*
 * No `declare( strict_types=1 )` here, unlike every other file in the theme:
 * `wp eval-file` runs the file through eval(), and a strict_types declaration
 * is only legal as the very first statement of a script. The parameter and
 * return types below are still declared and still enforced coercively.
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce is not active, so there is nothing to seed.' );
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * Builds a full-size Unsplash URL for a photo id.
 *
 * The size is requested explicitly so every product image has the same
 * dimensions — a catalogue of mixed aspect ratios makes a grid look broken for
 * reasons that have nothing to do with the theme.
 *
 * @param string $photo Unsplash photo id.
 * @return string
 */
function suitemart_seed_image_url( string $photo ): string {
	return sprintf(
		'https://images.unsplash.com/%s?w=1200&h=1200&fit=crop&q=80&fm=jpg',
		rawurlencode( $photo )
	);
}

/**
 * Imports an image into the media library, reusing it if already there.
 *
 * @param string $photo Unsplash photo id.
 * @param string $title Attachment title, used as the alt text too.
 * @return int Attachment id, or 0 on failure.
 */
function suitemart_seed_attachment( string $photo, string $title ): int {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_suitemart_seed_photo', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Development seeding only.
			'meta_value'     => $photo, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Development seeding only.
		)
	);

	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	$tmp = download_url( suitemart_seed_image_url( $photo ), 60 );

	if ( is_wp_error( $tmp ) ) {
		WP_CLI::warning( sprintf( 'Could not download %s: %s', $photo, $tmp->get_error_message() ) );

		return 0;
	}

	$id = media_handle_sideload(
		array(
			'name'     => $photo . '.jpg',
			'tmp_name' => $tmp,
		),
		0,
		$title
	);

	if ( is_wp_error( $id ) ) {
		// media_handle_sideload cleans up after itself on success only.
		if ( file_exists( $tmp ) ) {
			wp_delete_file( $tmp );
		}

		WP_CLI::warning( sprintf( 'Could not import %s: %s', $photo, $id->get_error_message() ) );

		return 0;
	}

	update_post_meta( $id, '_suitemart_seed_photo', $photo );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );

	return (int) $id;
}

/**
 * Ensures a product category exists.
 *
 * @param string $name Category name.
 * @return int Term id, or 0 on failure.
 */
function suitemart_seed_category( string $name ): int {
	$term = term_exists( $name, 'product_cat' );

	if ( ! $term ) {
		$term = wp_insert_term( $name, 'product_cat' );
	}

	return is_wp_error( $term ) ? 0 : (int) $term['term_id'];
}

/**
 * The catalogue.
 *
 * Names are invented rather than taken from the photographs' subjects: several
 * show real products, and a demo store should not put a manufacturer's name on
 * a listing it did not make.
 *
 * @return array<int, array<string, mixed>>
 */
function suitemart_seed_catalogue(): array {
	return array(
		array( 'Aurora Smart Watch', 'Electronics', 'photo-1523275335684-37898b6baf30', 199.00, 149.00, 24, 'A minimalist smart watch with a week of battery and a screen you can read outdoors.' ),
		array( 'Beacon Wireless Headphones', 'Electronics', 'photo-1505740420928-5e560c06d30e', 129.00, null, 40, 'Over-ear wireless headphones tuned for long listening rather than loudness.' ),
		array( 'Retro Instant Camera', 'Electronics', 'photo-1526170375885-4d8ecf77b99f', 119.00, 99.00, 12, 'Point, shoot, and hold the photograph a minute later. Takes standard instant film.' ),
		array( 'Nimbus Studio Headphones', 'Electronics', 'photo-1546435770-a3e426bf472b', 249.00, null, 8, 'Closed-back studio headphones with a flat response and replaceable earpads.' ),
		array( 'Cove Polarised Sunglasses', 'Accessories', 'photo-1572635196237-14b3f281503f', 89.00, 69.00, 55, 'Polarised lenses in a light acetate frame, with a hard case included.' ),
		array( 'Shadow Runner Sneakers', 'Footwear', 'photo-1491553895911-0055eca6402d', 139.00, null, 0, 'A road-running shoe with a firm midsole, built for daily distance rather than race day.' ),
		array( 'Prism Court Sneakers', 'Footwear', 'photo-1560769629-975ec94e6a86', 149.00, 119.00, 3, 'A court silhouette in six colours, with a rubber cupsole that takes a beating.' ),
		array( 'Cloudsoft Joggers', 'Apparel', 'photo-1594633312681-425c7b97ccd1', 69.00, null, 60, 'Brushed-back cotton joggers with a drawcord waist and deep side pockets.' ),
		array( 'Nordic Oak Stool', 'Home', 'photo-1503602642458-232111445657', 149.00, 129.00, 18, 'A solid oak stool that works as a side table, a plant stand, or a stool.' ),
		array( 'Meridian Chronograph', 'Accessories', 'photo-1523170335258-f5ed11844a49', 449.00, null, 6, 'An automatic chronograph on a steel bracelet, water resistant to 200 metres.' ),
		array( 'Vertex Ultrabook 14-inch', 'Electronics', 'photo-1517336714731-489689fd1ca8', 1299.00, 1149.00, 7, 'A thin 14-inch laptop with an all-day battery and a backlit keyboard.' ),
		array( 'Pulse Smartphone', 'Electronics', 'photo-1511707171634-5f897ff02aa9', 699.00, null, 15, 'A six-inch phone with a dual camera and a screen that dims to candlelight.' ),
		array( 'Everyday Phone Case Set', 'Accessories', 'photo-1556656793-08538906a9f8', 39.00, 29.00, 75, 'Three slim cases in muted colours, with a raised lip around the camera.' ),
		array( 'Lumen Screen Protector', 'Accessories', 'photo-1601784551446-20c9e07cdbdb', 19.00, null, 120, 'Tempered glass with an oleophobic coating and an alignment frame in the box.' ),
		array( 'Verdant Derby Shoes', 'Footwear', 'photo-1560343090-f0409e92791a', 219.00, 179.00, 9, 'Goodyear-welted derbies in green suede, resoleable and made to be worn in.' ),
		array( 'Petal Pendant Necklace', 'Accessories', 'photo-1611085583191-a3b181a88401', 79.00, null, 42, 'A gold-filled chain with a small freshwater pearl, 45cm with an extender.' ),
		array( 'Essential Fleece Sweatshirt', 'Apparel', 'photo-1620799140408-edc6dcb6d633', 79.00, 59.00, 0, 'A heavyweight loopback sweatshirt that keeps its shape through the winter.' ),
		array( 'Ivory Court Sneakers', 'Footwear', 'photo-1608231387042-66d1773070a5', 99.00, null, 27, 'A plain leather court shoe in off-white, with a padded collar and cotton laces.' ),
		array( 'Halo Drawing Tablet', 'Electronics', 'photo-1531297484001-80022131f5a1', 329.00, 279.00, 5, 'A pressure-sensitive drawing tablet with a battery-free pen and eight shortcut keys.' ),
		array( 'Copper Loop Headphones', 'Electronics', 'photo-1484704849700-f032a568e944', 179.00, null, 21, 'On-ear headphones in brushed copper, with a braided cable and an inline remote.' ),
	);
}

/**
 * Creates or updates every product in the catalogue.
 *
 * Wrapped in a function rather than written at the top level so its working
 * variables are locals: at file scope they would be globals, and a seeding
 * script has no business adding twenty of those to a running WordPress.
 *
 * @return void
 */
function suitemart_seed_run(): void {
	$sm_created = 0;
	$sm_updated = 0;

	foreach ( suitemart_seed_catalogue() as $sm_index => $sm_row ) {
		list( $sm_name, $sm_category, $sm_photo, $sm_price, $sm_sale, $sm_stock, $sm_summary ) = $sm_row;

		$sm_sku = sprintf( 'SM-DEMO-%02d', $sm_index + 1 );

		$sm_id      = wc_get_product_id_by_sku( $sm_sku );
		$sm_product = $sm_id ? wc_get_product( $sm_id ) : new WC_Product_Simple();

		if ( ! $sm_product instanceof WC_Product ) {
			WP_CLI::warning( sprintf( '%s exists but is not a product.', $sm_sku ) );

			continue;
		}

		$sm_product->set_name( $sm_name );
		$sm_product->set_sku( $sm_sku );
		$sm_product->set_status( 'publish' );
		$sm_product->set_catalog_visibility( 'visible' );
		$sm_product->set_short_description( $sm_summary );
		$sm_product->set_description(
			$sm_summary . ' This is demo content, written to give the theme something realistic to lay out. It is deliberately a few sentences long so that a product page shows how a real description wraps, and so the excerpt on a listing has something to truncate.'
		);

		$sm_product->set_regular_price( (string) $sm_price );
		$sm_product->set_sale_price( null === $sm_sale ? '' : (string) $sm_sale );

		// Stock is managed so the theme's stock bar and out-of-stock states have
		// real numbers behind them rather than a flag.
		$sm_product->set_manage_stock( true );
		$sm_product->set_stock_quantity( $sm_stock );
		$sm_product->set_stock_status( $sm_stock > 0 ? 'instock' : 'outofstock' );
		$sm_product->set_low_stock_amount( 5 );

		$sm_category_id = suitemart_seed_category( $sm_category );

		if ( $sm_category_id > 0 ) {
			$sm_product->set_category_ids( array( $sm_category_id ) );
		}

		$sm_attachment = suitemart_seed_attachment( $sm_photo, $sm_name );

		if ( $sm_attachment > 0 ) {
			$sm_product->set_image_id( $sm_attachment );
		}

		$sm_product->save();

		if ( $sm_id ) {
			++$sm_updated;
		} else {
			++$sm_created;
		}

		WP_CLI::log( sprintf( '%s  %s', $sm_sku, $sm_name ) );
	}

	WP_CLI::success( sprintf( '%d created, %d updated.', $sm_created, $sm_updated ) );
}

suitemart_seed_run();
