<?php
/**
 * Add to cart button server rendering.
 *
 * Two things are worth asserting in PHP, and both are about what the button
 * says rather than what it looks like. A product that cannot be added in one
 * request must render as a link to the product rather than as a button that
 * would fail on click; and the control must carry an accessible name in the
 * served markup, because every binding on it is also evaluated on the server
 * and an unresolved one strips the attribute outright.
 *
 * The nonce assertion is the third: one rendered into markup is served stale
 * from behind a full-page cache and every add then 403s.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Renders the add to cart button against real products.
 */
class Test_Add_To_Cart_Button extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent, which is the case without WooCommerce.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/add-to-cart-button' ) ) {
			$this->markTestSkipped( 'suitemart/add-to-cart-button is not registered here.' );
		}
	}

	/**
	 * Renders the block against a product supplied as context.
	 *
	 * @param int                  $post_id Product to render against.
	 * @param array<string, mixed> $attrs   Block attributes.
	 * @return string Rendered markup.
	 */
	private function render( int $post_id, array $attrs = array() ): string {
		$block = array(
			'blockName'    => 'suitemart/add-to-cart-button',
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		return ( new WP_Block( $block, array( 'postId' => $post_id ) ) )->render();
	}

	/**
	 * Creates a simple, purchasable, in-stock product.
	 *
	 * @return int Product id.
	 */
	private function create_simple_product(): int {
		$product = new WC_Product_Simple();
		$product->set_name( 'Addable' );
		$product->set_regular_price( '10.00' );
		$product->set_stock_status( 'instock' );

		return $product->save();
	}

	/**
	 * A product that can be added in one request renders a button.
	 */
	public function test_simple_product_renders_a_button(): void {
		$this->require_block();

		$markup = $this->render( $this->create_simple_product() );

		$this->assertStringContainsString( '<button', $markup, 'A purchasable product must render a button.' );
		$this->assertStringContainsString( 'data-wp-on--click="actions.add"', $markup );
	}

	/**
	 * An out of stock product renders a link to the product instead.
	 *
	 * A button that cannot do anything is worse than a link that says where it
	 * goes, and Woo's own wording is used so it reads the same as everywhere
	 * else in the shop.
	 */
	public function test_unpurchasable_product_renders_a_link(): void {
		$this->require_block();

		$product = new WC_Product_Simple();
		$product->set_name( 'Sold out' );
		$product->set_regular_price( '10.00' );
		$product->set_stock_status( 'outofstock' );
		$id = $product->save();

		$markup = $this->render( $id );

		$this->assertStringContainsString( '<a', $markup, 'An unpurchasable product must render a link.' );
		$this->assertStringNotContainsString( '<button', $markup );
	}

	/**
	 * The control is named however it is rendered.
	 *
	 * Icon-only is the appearance the product card uses, so the name lives in
	 * `aria-label` and nowhere else. It must survive server-side directive
	 * processing as a literal, not only as a binding.
	 */
	public function test_icon_only_button_carries_an_accessible_name(): void {
		$this->require_block();

		$markup = $this->render(
			$this->create_simple_product(),
			array( 'appearance' => 'icon' )
		);

		$this->assertMatchesRegularExpression(
			'/aria-label="[^"]+"/',
			$markup,
			'An icon-only control with no accessible name is unusable.'
		);
	}

	/**
	 * An icon reaches the markup.
	 *
	 * `suitemart_get_icon()` returns markup rather than printing it, and four
	 * blocks have already shipped calling it bare.
	 */
	public function test_an_icon_is_rendered(): void {
		$this->require_block();

		$this->assertStringContainsString(
			'<svg',
			$this->render( $this->create_simple_product() ),
			'The button must render its icon.'
		);
	}

	/**
	 * No nonce is ever rendered into the markup.
	 */
	public function test_no_nonce_is_rendered(): void {
		$this->require_block();

		$this->assertStringNotContainsString(
			'wp_rest',
			$this->render( $this->create_simple_product() ),
			'A nonce in cacheable markup is served stale and every add then 403s.'
		);
	}

	/**
	 * `render.php` never mints a nonce in the first place.
	 */
	public function test_render_does_not_create_a_nonce(): void {
		$path = get_template_directory() . '/src/add-to-cart-button/render.php';

		$this->assertFileExists( $path );
		$this->assertStringNotContainsString(
			'wp_create_nonce',
			(string) file_get_contents( $path )
		);
	}

	/**
	 * Outside a product there is nothing to add, so nothing is rendered.
	 */
	public function test_non_product_renders_nothing(): void {
		$this->require_block();

		$page = self::factory()->post->create( array( 'post_type' => 'page' ) );

		$this->assertSame( '', trim( $this->render( $page ) ) );
	}
}
