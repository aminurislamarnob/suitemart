<?php
/**
 * Tests for the fbt-products block.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

namespace Suitemart\Tests\Blocks;

use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * Tests the Frequently Bought Together block.
 */
class Test_Fbt_Products extends WP_UnitTestCase {

	/**
	 * Product ID under test.
	 *
	 * @var int
	 */
	private $product_id;

	/**
	 * Array of cross-sell product IDs.
	 *
	 * @var int[]
	 */
	private $cross_sell_ids = array();

	/**
	 * Set up test products.
	 */
	public function set_up(): void {
		parent::set_up();

		$main = new \WC_Product_Simple();
		$main->set_name( 'Main Product' );
		$main->set_regular_price( '20' );
		$main->set_status( 'publish' );
		$main->save();
		$this->product_id = $main->get_id();

		for ( $i = 0; $i < 3; $i++ ) {
			$cross = new \WC_Product_Simple();
			$cross->set_name( 'Cross Sell ' . $i );
			$cross->set_regular_price( '10' );
			$cross->set_status( 'publish' );
			$cross->save();
			$this->cross_sell_ids[] = $cross->get_id();
		}

		$main->set_cross_sell_ids( $this->cross_sell_ids );
		$main->save();
	}

	/**
	 * Ensures the block registers successfully.
	 */
	public function test_registers(): void {
		$registry = WP_Block_Type_Registry::get_instance();
		$this->assertTrue( $registry->is_registered( 'suitemart/fbt-products' ) );
	}

	/**
	 * Tests rendering outside a product context.
	 */
	public function test_renders_empty_outside_product(): void {
		$markup = render_block( array( 'blockName' => 'suitemart/fbt-products' ) );
		$this->assertSame( '', trim( (string) $markup ) );
	}

	/**
	 * Tests rendering when the product has no cross-sells.
	 */
	public function test_renders_empty_with_no_cross_sells(): void {
		$lonely_product = new \WC_Product_Simple();
		$lonely_product->set_name( 'Lonely' );
		$lonely_product->set_status( 'publish' );
		$lonely_product->save();

		$markup = render_block(
			array(
				'blockName' => 'suitemart/fbt-products',
			)
		);

		// With a post global.
		global $post;
		$post = get_post( $lonely_product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $post );

		$markup = render_block(
			array(
				'blockName' => 'suitemart/fbt-products',
			)
		);

		wp_reset_postdata();
		$this->assertSame( '', trim( (string) $markup ) );
	}

	/**
	 * Tests successful render with cross-sells.
	 */
	public function test_renders_with_cross_sells(): void {
		$block = array(
			'blockName'    => 'suitemart/fbt-products',
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		$markup = ( new \WP_Block( $block, array( 'postId' => $this->product_id ) ) )->render();

		$this->assertStringContainsString( 'sm-fbt-products', $markup );
		$this->assertStringContainsString( 'Main Product', $markup );
		$this->assertStringContainsString( 'Cross Sell 0', $markup );
		$this->assertStringContainsString( 'Cross Sell 1', $markup );
		// It should only show up to 2 cross-sells, so the third one is skipped.
		$this->assertStringNotContainsString( 'Cross Sell 2', $markup );
	}
}
