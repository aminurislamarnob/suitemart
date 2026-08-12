<?php
/**
 * Compare button server rendering and its shared limit.
 *
 * The limit is the part worth testing in PHP: it is read by the button, by the
 * table, and by the browser through the Interactivity store, so a mismatch
 * between them would show up as a table that silently drops a column.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Renders the compare button and checks the comparison limit.
 */
class Test_Compare extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent, which is the case without WooCommerce.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/compare-button' ) ) {
			$this->markTestSkipped( 'suitemart/compare-button is not registered here.' );
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
			'blockName'    => 'suitemart/compare-button',
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		return ( new WP_Block( $block, array( 'postId' => $post_id ) ) )->render();
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
			'The compare button has no accessible name, so a screen reader announces it only as "button".'
		);
	}

	/**
	 * The server cannot read the browser, so it must claim nothing.
	 */
	public function test_renders_in_the_unadded_state(): void {
		$this->require_block();

		$html = $this->render( $this->create_product() );

		$this->assertStringContainsString( 'aria-pressed="false"', $html );

		preg_match( '/class="([^"]*)"/', $html, $classes );

		$this->assertStringNotContainsString(
			'is-added',
			$classes[1] ?? '',
			'A cached copy of this page would show one visitor’s comparison to everybody.'
		);
	}

	/**
	 * Outside a product there is nothing to compare.
	 */
	public function test_renders_nothing_outside_a_product(): void {
		$this->require_block();

		$page = self::factory()->post->create( array( 'post_type' => 'page' ) );

		$this->assertSame( '', trim( wp_strip_all_tags( $this->render( $page ) ) ) );
	}

	/**
	 * The limit the browser enforces must be the one PHP believes in.
	 */
	public function test_the_limit_reaches_the_browser(): void {
		$this->require_block();

		$html = $this->render( $this->create_product() );

		// The state is printed once per page in a script tag by the
		// Interactivity API, so the rendered block is processed the way the
		// front end processes it.
		$processed = wp_interactivity_process_directives( $html );

		$this->assertNotEmpty( $processed );

		$state = wp_interactivity_state( 'suitemart/compare' );

		$this->assertSame(
			suitemart_compare_limit(),
			$state['limit'] ?? null,
			'The browser would cap the comparison list at a different number than the table renders.'
		);
	}

	/**
	 * The limit is filterable, but not to a value that breaks the table.
	 */
	public function test_the_limit_is_clamped(): void {
		$too_many = static fn (): int => 99;
		add_filter( 'suitemart_compare_limit', $too_many );
		$this->assertSame( 6, suitemart_compare_limit(), 'An unbounded limit renders a table nobody can read.' );
		remove_filter( 'suitemart_compare_limit', $too_many );

		$too_few = static fn (): int => 1;
		add_filter( 'suitemart_compare_limit', $too_few );
		$this->assertSame( 2, suitemart_compare_limit(), 'Comparing one product against nothing is not a comparison.' );
		remove_filter( 'suitemart_compare_limit', $too_few );

		$this->assertSame( 4, suitemart_compare_limit() );
	}

	/**
	 * The table must ship no visitor data, because a cache will keep it.
	 */
	public function test_the_table_renders_no_products(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/compare-table' ) ) {
			$this->markTestSkipped( 'suitemart/compare-table is not registered here.' );
		}

		$product = $this->create_product();

		$html = render_block(
			array(
				'blockName'    => 'suitemart/compare-table',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		$this->assertStringNotContainsString(
			(string) $product,
			$html,
			'The table rendered product data the server has no way of knowing the visitor asked for.'
		);

		// The empty state is real markup rather than a binding, so it is there
		// for a reader whose JavaScript never arrives.
		$this->assertStringContainsString(
			'sm-compare-table__empty',
			$html,
			'Without JavaScript the block renders nothing at all, not even an explanation.'
		);

		// The table itself starts hidden; showing an empty table would read as
		// "your comparison is empty" before the list has even been read.
		$this->assertMatchesRegularExpression(
			'/sm-compare-table__scroll[^>]*hidden/',
			$html,
			'An empty table is served before the browser has read the list.'
		);
	}
}
