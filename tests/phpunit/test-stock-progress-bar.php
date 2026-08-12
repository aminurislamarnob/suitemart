<?php
/**
 * Stock progress bar block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests the stock progress bar block.
 */
class Test_Stock_Progress_Bar extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent, which is the case without WooCommerce.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/stock-progress-bar' ) ) {
			$this->markTestSkipped( 'suitemart/stock-progress-bar is not registered here.' );
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
			'blockName'    => 'suitemart/stock-progress-bar',
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		return ( new WP_Block( $block, array( 'postId' => $post_id ) ) )->render();
	}

	/**
	 * Outside a product there is no progress bar.
	 */
	public function test_renders_nothing_outside_a_product(): void {
		$this->require_block();

		$page = self::factory()->post->create( array( 'post_type' => 'page' ) );

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $page ) ) ) );
	}

	/**
	 * Renders nothing if stock is not managed.
	 */
	public function test_renders_nothing_if_stock_not_managed(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '10' );
		$product->set_manage_stock( false );
		$product->save();

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $product_id ) ) ) );
	}

	/**
	 * Renders progress bar correctly for managed stock.
	 */
	public function test_renders_stock_progress_bar(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '10' );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( 15 );
		$product->set_low_stock_amount( 5 );
		$product->save();

		// Add 5 sales so initial stock is calculated as 20.
		update_post_meta( $product_id, 'total_sales', 5 );

		$html = $this->render( $product_id );

		$this->assertStringContainsString( 'Only 15 left', $html );
		$this->assertStringContainsString( 'width: 75%', $html );
		$this->assertStringNotContainsString( 'is-low-stock', $html );
	}

	/**
	 * Renders low stock class when stock is low.
	 */
	public function test_renders_low_stock_class(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '10' );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( 3 );
		$product->set_low_stock_amount( 5 );
		$product->save();

		$html = $this->render( $product_id );

		$this->assertStringContainsString( 'Only 3 left', $html );
		$this->assertStringContainsString( 'is-low-stock', $html );
	}
}
