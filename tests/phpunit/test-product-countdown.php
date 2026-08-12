<?php
/**
 * Product countdown block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests the product countdown block.
 */
class Test_Product_Countdown extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/product-countdown' ) ) {
			$this->markTestSkipped( 'suitemart/product-countdown is not registered here.' );
		}
	}

	/**
	 * Renders the block against a post supplied as context.
	 *
	 * @param int                  $post_id Post to render against.
	 * @param array<string, mixed> $attrs   Block attributes.
	 * @return string Rendered markup.
	 */
	private function render( int $post_id, array $attrs = array() ): string {
		$block = array(
			'blockName'    => 'suitemart/product-countdown',
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		return ( new WP_Block( $block, array( 'postId' => $post_id ) ) )->render();
	}

	/**
	 * Outside a product there is no block.
	 */
	public function test_renders_nothing_outside_a_product(): void {
		$this->require_block();

		$page = self::factory()->post->create( array( 'post_type' => 'page' ) );

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $page ) ) ) );
	}

	/**
	 * Renders nothing if not on sale.
	 */
	public function test_renders_nothing_if_not_on_sale(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '20' );
		$product->save();

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $product_id ) ) ) );
	}

	/**
	 * Renders nothing if on sale without an end date.
	 */
	public function test_renders_nothing_if_no_end_date(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '20' );
		$product->set_sale_price( '10' );
		$product->save();

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $product_id ) ) ) );
	}

	/**
	 * Renders countdown when on sale with end date.
	 */
	public function test_renders_countdown(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '20' );
		$product->set_sale_price( '10' );
		// Set sale end date to tomorrow.
		$end_timestamp = time() + DAY_IN_SECONDS;
		$product->set_date_on_sale_to( $end_timestamp );
		$product->save();

		$html = $this->render( $product_id );

		$this->assertStringContainsString( 'sm-product-countdown-wrapper', $html );
		$this->assertStringContainsString( 'sm-countdown', $html );
		$this->assertStringContainsString( 'Offer ends', wp_strip_all_tags( $html ) );
	}
}
