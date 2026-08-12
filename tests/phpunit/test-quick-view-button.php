<?php
/**
 * Quick view button block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

namespace Suitemart\Tests\Blocks;

use WP_UnitTestCase;

/**
 * Quick view button render test.
 */
class Test_Quick_View_Button extends WP_UnitTestCase {

	/**
	 * Test that the block renders the button and the global modal.
	 */
	public function test_render() {
		if ( ! suitemart_has_woocommerce() ) {
			$this->markTestSkipped( 'Requires WooCommerce' );
		}

		$product_id = $this->factory()->post->create(
			array(
				'post_type'  => 'product',
				'post_title' => 'Test Product',
			)
		);

		$content = do_blocks(
			'<!-- wp:suitemart/quick-view-button /-->'
		);

		// Without context, it renders nothing.
		$this->assertSame( '', $content );

		global $post;
		$post = get_post( $product_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $post );

		$content = do_blocks(
			'<!-- wp:suitemart/quick-view-button /-->'
		);

		wp_reset_postdata();

		// Renders the button.
		$this->assertStringContainsString( 'sm-quick-view-button', $content );
		$this->assertStringContainsString( 'data-wp-interactive="suitemart/quick-view"', $content );
		$this->assertStringContainsString( '"productId":' . $product_id, $content );

		// Hook should be added to wp_footer.
		$this->assertNotFalse( has_action( 'wp_footer', 'suitemart_render_quick_view_modal' ) );

		// Capture the footer output to verify the modal.
		ob_start();
		suitemart_render_quick_view_modal();
		$footer = ob_get_clean();

		$this->assertStringContainsString( 'sm-quick-view-modal', $footer );
		$this->assertStringContainsString( 'sm-quick-view-modal__dialog', $footer );
	}
}
