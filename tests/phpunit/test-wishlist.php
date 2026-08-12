<?php
/**
 * Wishlist button server rendering.
 *
 * The wishlist lives in the visitor's browser, so this block is the clearest
 * case of a rule the theme has already been bitten by three times: Interactivity
 * directives are evaluated on the server as well as in the browser, and an
 * expression the server cannot resolve does not leave the previous value in
 * place — it erases the text or strips the attribute outright.
 *
 * A button whose `aria-label` binding failed is announced as "button". These
 * tests assert the pre-JavaScript render is correct on its own terms.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Renders the wishlist button in and out of a product context.
 */
class Test_Wishlist extends WP_UnitTestCase {

	/**
	 * Renders the block with a post supplied as context.
	 *
	 * @param int                  $post_id    Post to render against.
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return string Rendered markup.
	 */
	private function render( int $post_id, array $attributes = array() ): string {
		$block = array(
			'blockName'    => 'suitemart/wishlist-button',
			'attrs'        => $attributes,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		// The block reads `postId` from context, which on a real page comes from
		// the query loop or the singular template.
		return ( new WP_Block( $block, array( 'postId' => $post_id ) ) )->render();
	}

	/**
	 * Skips when the block is absent, which is the case without WooCommerce.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/wishlist-button' ) ) {
			$this->markTestSkipped( 'suitemart/wishlist-button is not registered here.' );
		}
	}

	/**
	 * Creates a published product.
	 *
	 * @return int Post id.
	 */
	private function create_product(): int {
		return self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_title'  => 'Test product',
				'post_status' => 'publish',
			)
		);
	}

	/**
	 * The button must carry an accessible name before any JavaScript runs.
	 */
	public function test_renders_with_a_resolved_accessible_name(): void {
		$this->require_block();

		$html = $this->render( $this->create_product() );

		$this->assertMatchesRegularExpression(
			'/<button[^>]*aria-label="[^"]+"/',
			$html,
			'The wishlist button has no accessible name, so a screen reader announces it only as "button".'
		);
	}

	/**
	 * The server cannot read the browser, so it must claim nothing.
	 */
	public function test_renders_in_the_unsaved_state(): void {
		$this->require_block();

		$html = $this->render( $this->create_product() );

		$this->assertStringContainsString(
			'aria-pressed="false"',
			$html,
			'The server rendered a saved state it has no way of knowing, so the button will be wrong for every visitor whose wishlist is empty.'
		);

		// The `is-saved` string also appears in the `data-wp-class--is-saved`
		// directive, so the class attribute is checked rather than the markup.
		preg_match( '/class="([^"]*)"/', $html, $classes );

		$this->assertStringNotContainsString(
			'is-saved',
			$classes[1] ?? '',
			'A cached copy of this page would show one visitor’s wishlist to everybody.'
		);
	}

	/**
	 * The label element must contain text, not an empty binding.
	 */
	public function test_the_label_is_not_erased_by_its_binding(): void {
		$this->require_block();

		$html = $this->render( $this->create_product(), array( 'appearance' => 'icon-label' ) );

		$this->assertMatchesRegularExpression(
			'/sm-wishlist-button__label[^>]*>\s*\S/',
			$html,
			'The label rendered empty, so the button is a bare icon until JavaScript runs.'
		);
	}

	/**
	 * Outside a product there is nothing to save, so nothing should render.
	 */
	public function test_renders_nothing_outside_a_product(): void {
		$this->require_block();

		$page = self::factory()->post->create( array( 'post_type' => 'page' ) );

		$this->assertSame(
			'',
			trim( wp_strip_all_tags( $this->render( $page ) ) ),
			'The button rendered on a page, where it would save an id that is not a product.'
		);
	}

	/**
	 * The product id has to reach the browser or the button cannot act.
	 */
	public function test_the_product_id_is_carried_in_context(): void {
		$this->require_block();

		$product = $this->create_product();
		$html    = $this->render( $product );

		// wp_interactivity_data_wp_context() single-quotes the attribute, so the
		// JSON inside it is not entity-encoded.
		$this->assertStringContainsString(
			'"productId":' . $product,
			$html,
			'The button has no product id, so clicking it would store nothing.'
		);
	}

	/**
	 * The grid must ship no visitor data, because a cache will keep it.
	 */
	public function test_the_grid_renders_no_products(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/wishlist-grid' ) ) {
			$this->markTestSkipped( 'suitemart/wishlist-grid is not registered here.' );
		}

		$product = $this->create_product();

		$html = render_block(
			array(
				'blockName'    => 'suitemart/wishlist-grid',
				'attrs'        => array( 'columns' => 3 ),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		$this->assertStringNotContainsString(
			(string) $product,
			$html,
			'The grid rendered product data the server has no way of knowing the visitor saved.'
		);

		// The column count has to be a class: `repeat( var( --n ), 1fr )` is
		// invalid CSS and drops the whole declaration, which looks like a
		// layout bug rather than the CSS error it is.
		$this->assertStringContainsString(
			'sm-wishlist-grid--cols-3',
			$html,
			'The column setting never reaches the stylesheet.'
		);

		$this->assertStringContainsString(
			'sm-wishlist-grid__empty',
			$html,
			'Without JavaScript the block renders nothing at all, not even an explanation.'
		);
	}

	/**
	 * An out-of-range column count must not reach the class name.
	 */
	public function test_the_column_count_is_clamped(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/wishlist-grid' ) ) {
			$this->markTestSkipped( 'suitemart/wishlist-grid is not registered here.' );
		}

		$html = render_block(
			array(
				'blockName'    => 'suitemart/wishlist-grid',
				'attrs'        => array( 'columns' => 40 ),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		// Only classes up to six are generated, so anything past that would be
		// a class with no rule behind it.
		$this->assertStringContainsString( 'sm-wishlist-grid--cols-6', $html );
	}
}
