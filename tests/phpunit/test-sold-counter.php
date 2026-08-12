<?php
/**
 * Sold counter block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests the sold counter block.
 */
class Test_Sold_Counter extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/sold-counter' ) ) {
			$this->markTestSkipped( 'suitemart/sold-counter is not registered here.' );
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
			'blockName'    => 'suitemart/sold-counter',
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		return ( new WP_Block( $block, array( 'postId' => $post_id ) ) )->render();
	}

	/**
	 * Outside a product there is no counter.
	 */
	public function test_renders_nothing_outside_a_product(): void {
		$this->require_block();

		$page = self::factory()->post->create( array( 'post_type' => 'page' ) );

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $page ) ) ) );
	}

	/**
	 * Renders nothing if total sales is 0.
	 */
	public function test_renders_nothing_if_no_sales(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '10' );
		$product->save();

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $product_id ) ) ) );
	}

	/**
	 * Renders the sold count.
	 */
	public function test_renders_sold_count(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '10' );
		$product->save();

		update_post_meta( $product_id, 'total_sales', 42 );

		$html = $this->render( $product_id );

		$this->assertStringContainsString( '42 units sold', wp_strip_all_tags( $html ) );
	}
}
