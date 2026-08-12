<?php
/**
 * Rendered-output tests for traps that leave every linter green.
 *
 * The existing suites assert that a block renders and that its markup has the
 * right shape. Two whole classes of defect slip past that, and both of them
 * have shipped here:
 *
 * - `suitemart_get_icon()` returns markup, it does not print. Four blocks
 *   called it bare, so the icon vanished — including a modal's close button,
 *   which shipped as a visibly empty control. Nothing failed.
 * - A block that hardcodes a DOM id renders duplicate ids the moment a second
 *   instance appears, which a product grid guarantees. `aria-controls` and
 *   `aria-labelledby` then resolve to the wrong element, and one click opened
 *   every size guide on the page at once.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Asserts blocks produce the output their source implies.
 */
class Test_Block_Output extends WP_UnitTestCase {

	/**
	 * Renders a block, optionally against a post supplied as context.
	 *
	 * @param string               $name    Block name.
	 * @param array<string, mixed> $attrs   Block attributes.
	 * @param array<string, mixed> $context Block context.
	 * @param string               $inner   Inner blocks markup.
	 * @return string Rendered markup.
	 */
	private function render( string $name, array $attrs = array(), array $context = array(), string $inner = '' ): string {
		// Cast to an object so empty attributes serialise as `{}`; `[]` is not
		// valid block-comment JSON and the comment parses as plain HTML.
		$parsed = parse_blocks(
			sprintf(
				'<!-- wp:%s %s -->%s<!-- /wp:%s -->',
				$name,
				wp_json_encode( (object) $attrs ),
				$inner,
				$name
			)
		);

		return ( new WP_Block( $parsed[0], $context ) )->render();
	}

	/**
	 * A product with everything a commerce block might ask of it.
	 *
	 * @return int Product id.
	 */
	private function product(): int {
		$product_id = self::factory()->post->create( array( 'post_type' => 'product' ) );

		$product = new WC_Product_Simple( $product_id );
		$product->set_regular_price( '19.99' );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( 3 );
		$product->save();

		update_post_meta( $product_id, 'total_sales', 42 );

		return $product_id;
	}

	/**
	 * Blocks whose render template emits an icon, and the context they need.
	 *
	 * @return array<string, array{0: string, 1: bool}>
	 */
	public function icon_block_provider(): array {
		return array(
			'sold counter'       => array( 'suitemart/sold-counter', true ),
			'estimated delivery' => array( 'suitemart/estimated-delivery', true ),
			'visitor counter'    => array( 'suitemart/visitor-counter', true ),
			'size guide'         => array( 'suitemart/size-guide', false ),
		);
	}

	/**
	 * A block that calls the icon helper must actually print the result.
	 *
	 * @dataProvider icon_block_provider
	 *
	 * @param string $name         Block name.
	 * @param bool   $needs_product Whether the block requires product context.
	 */
	public function test_icon_reaches_the_markup( string $name, bool $needs_product ): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( $name ) ) {
			$this->markTestSkipped( sprintf( '%s is not registered here.', $name ) );
		}

		$context = $needs_product ? array( 'postId' => $this->product() ) : array();
		$markup  = $this->render( $name, array(), $context );

		$this->assertNotSame( '', trim( $markup ), sprintf( '%s rendered nothing at all.', $name ) );
		$this->assertStringContainsString(
			'<svg',
			$markup,
			sprintf(
				'%s calls suitemart_get_icon() but no SVG reached the markup. The helper returns its output; it has to be echoed.',
				$name
			)
		);
	}

	/**
	 * Two size guides on one page must not collide.
	 *
	 * A product grid renders one per card, so this is the ordinary case rather
	 * than an edge one.
	 */
	public function test_two_size_guides_get_distinct_ids(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/size-guide' ) ) {
			$this->markTestSkipped( 'suitemart/size-guide is not registered here.' );
		}

		$first  = $this->render( 'suitemart/size-guide', array(), array( 'postId' => $this->product() ) );
		$second = $this->render( 'suitemart/size-guide', array(), array( 'postId' => $this->product() ) );

		preg_match_all( '/id="([^"]+)"/', $first . $second, $matches );

		$ids = $matches[1];

		$this->assertNotEmpty( $ids, 'The size guide rendered no ids at all.' );
		$this->assertSame(
			$ids,
			array_unique( $ids ),
			'Two size guides produced duplicate DOM ids, so aria-controls and aria-labelledby resolve to the wrong element: ' . implode( ', ', $ids )
		);
	}

	/**
	 * The button's aria-controls must name the modal rendered beside it.
	 */
	public function test_size_guide_button_points_at_its_own_modal(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/size-guide-button' ) ) {
			$this->markTestSkipped( 'suitemart/size-guide-button is not registered here.' );
		}

		$product = $this->product();
		$context = array( 'postId' => $product );

		$button = $this->render( 'suitemart/size-guide-button', array(), $context );
		$modal  = $this->render( 'suitemart/size-guide', array(), $context );

		$this->assertSame( 1, preg_match( '/aria-controls="([^"]+)"/', $button, $target ) );
		$this->assertStringContainsString(
			sprintf( 'id="%s"', $target[1] ),
			$modal,
			'The button points at an id the modal never renders.'
		);
	}

	/**
	 * The currency prefix must be a symbol, not the entity Woo stores it as.
	 *
	 * `get_woocommerce_currency_symbol()` returns "&#36;". Seeded undecoded and
	 * written out through `data-wp-text`, which sets textContent, that renders
	 * literally as "&#36;19.99".
	 */
	public function test_fbt_seeds_a_decoded_currency_symbol(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/fbt-products' ) ) {
			$this->markTestSkipped( 'suitemart/fbt-products is not registered here.' );
		}

		$main  = $this->product();
		$cross = $this->product();

		$product = wc_get_product( $main );
		$product->set_cross_sell_ids( array( $cross ) );
		$product->save();

		$markup  = $this->render( 'suitemart/fbt-products', array(), array( 'postId' => $main ) );
		$decoded = html_entity_decode( $markup, ENT_QUOTES, 'UTF-8' );

		if ( ! str_contains( $decoded, 'currency_prefix' ) ) {
			$this->markTestSkipped( 'The block declined to render, so there is no currency to check.' );
		}

		$this->assertDoesNotMatchRegularExpression(
			'/currency_(prefix|suffix)":"[^"]*&#?[a-z0-9]+;/i',
			$decoded,
			'The seeded currency symbol is still an HTML entity, which data-wp-text will print literally.'
		);
	}

	/**
	 * No nonce may be rendered into markup a page cache will serve to everyone.
	 */
	public function test_no_block_renders_a_nonce(): void {
		$offenders = array();

		foreach ( (array) glob( SUITEMART_DIR . '/src/*/render.php' ) as $path ) {
			$source = (string) file_get_contents( $path );

			if ( str_contains( $source, 'wp_create_nonce' ) ) {
				$offenders[] = str_replace( SUITEMART_DIR . '/', '', $path );
			}
		}

		$this->assertSame(
			array(),
			$offenders,
			"A nonce rendered into the page goes stale behind a full-page cache and every request using it then fails with 403. Fetch one at interaction time instead:\n" . implode( "\n", $offenders )
		);
	}
}
