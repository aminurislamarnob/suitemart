<?php
/**
 * Product labels block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests the product labels block.
 */
class Test_Product_Labels extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent, which is the case without WooCommerce.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/product-labels' ) ) {
			$this->markTestSkipped( 'suitemart/product-labels is not registered here.' );
		}
	}

	/**
	 * Renders the block against a post supplied as context.
	 *
	 * @param int $post_id Post to render against.
	 * @return string Rendered markup.
	 */
	private function render( int $post_id ): string {
		$block = array(
			'blockName'    => 'suitemart/product-labels',
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		return ( new WP_Block( $block, array( 'postId' => $post_id ) ) )->render();
	}

	/**
	 * Outside a product there are no labels.
	 */
	public function test_renders_nothing_outside_a_product(): void {
		$this->require_block();

		$page = self::factory()->post->create( array( 'post_type' => 'page' ) );

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $page ) ) ) );
	}

	/**
	 * An ordinary product might just be new.
	 */
	public function test_renders_new_label(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
				'post_date'   => current_time( 'mysql' ),
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '10' );
		$product->save();

		$html = $this->render( $product_id );

		$this->assertStringContainsString( 'sm-product-labels__label--new', $html );
		$this->assertStringNotContainsString( 'sm-product-labels__label--sale', $html );
		$this->assertStringNotContainsString( 'sm-product-labels__label--out-of-stock', $html );
	}

	/**
	 * A product on sale.
	 */
	public function test_renders_sale_label(): void {
		$this->require_block();

		// Make it old so it's not 'new'.
		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
				'post_date'   => gmdate( 'Y-m-d H:i:s', time() - 30 * DAY_IN_SECONDS ),
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '10' );
		$product->set_sale_price( '5' );
		$product->save();

		$html = $this->render( $product_id );

		$this->assertStringContainsString( 'sm-product-labels__label--sale', $html );
		$this->assertStringNotContainsString( 'sm-product-labels__label--new', $html );
		$this->assertStringNotContainsString( 'sm-product-labels__label--out-of-stock', $html );
	}

	/**
	 * An out of stock product.
	 */
	public function test_renders_out_of_stock_label(): void {
		$this->require_block();

		// Make it old so it's not 'new'.
		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
				'post_date'   => gmdate( 'Y-m-d H:i:s', time() - 30 * DAY_IN_SECONDS ),
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '10' );
		$product->set_manage_stock( true );
		$product->set_stock_status( 'outofstock' );
		$product->save();

		$html = $this->render( $product_id );

		$this->assertStringContainsString( 'sm-product-labels__label--out-of-stock', $html );
		$this->assertStringNotContainsString( 'sm-product-labels__label--new', $html );
		$this->assertStringNotContainsString( 'sm-product-labels__label--sale', $html );
	}
}
