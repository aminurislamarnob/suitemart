<?php
/**
 * Estimated delivery block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests the estimated delivery block.
 */
class Test_Estimated_Delivery extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/estimated-delivery' ) ) {
			$this->markTestSkipped( 'suitemart/estimated-delivery is not registered here.' );
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
			'blockName'    => 'suitemart/estimated-delivery',
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		return ( new WP_Block( $block, array( 'postId' => $post_id ) ) )->render();
	}

	/**
	 * Outside a product there is no delivery block.
	 */
	public function test_renders_nothing_outside_a_product(): void {
		$this->require_block();

		$page = self::factory()->post->create( array( 'post_type' => 'page' ) );

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $page ) ) ) );
	}

	/**
	 * Renders nothing for virtual products.
	 */
	public function test_renders_nothing_for_virtual_products(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);
		$product    = wc_get_product( $product_id );
		$product->set_regular_price( '10' );
		$product->set_virtual( true );
		$product->save();

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $product_id ) ) ) );
	}

	/**
	 * Renders the estimated delivery string.
	 */
	public function test_renders_delivery_string(): void {
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

		$html = $this->render(
			$product_id,
			array(
				'minDays' => 1,
				'maxDays' => 2,
			)
		);

		$this->assertStringContainsString( 'Estimated delivery:', wp_strip_all_tags( $html ) );
	}
}
