<?php
/**
 * Back to top block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/back-to-top.
 */
class Test_Back_To_Top extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/back-to-top' ) ) {
			$this->markTestSkipped( 'suitemart/back-to-top is not registered here.' );
		}
	}

	/**
	 * Renders the block through do_blocks(), so directive processing runs.
	 *
	 * @param array<string, mixed> $attrs Block attributes.
	 * @return string Rendered markup.
	 */
	private function render( array $attrs = array() ): string {
		return do_blocks(
			sprintf( '<!-- wp:suitemart/back-to-top %s /-->', wp_json_encode( (object) $attrs ) )
		);
	}

	/**
	 * The button is served out of the tab order, not merely transparent.
	 *
	 * A page is served scrolled to the top, so the button is not yet wanted.
	 * Fading it out without `inert` leaves an invisible control that Tab still
	 * lands on — the kind of defect that never shows up in a screenshot.
	 */
	public function test_the_button_starts_inert(): void {
		$this->require_block();

		$html = $this->render();

		$this->assertMatchesRegularExpression( '/<button[^>]*\sinert/', $html );
		$this->assertStringContainsString( 'data-wp-bind--inert="!context.isVisible"', $html );

		/*
		 * And the wrapper is not carrying `is-visible`, which the class binding
		 * would have added had the seeded context said otherwise. Matched
		 * inside the class attribute specifically: the directive's own name
		 * contains the string, so a plain substring test can never fail.
		 */
		preg_match( '/class="([^"]*sm-back-to-top[^"]*)"/', $html, $classes );

		$this->assertNotEmpty( $classes );
		$this->assertStringNotContainsString( 'is-visible', $classes[1] );
	}

	/**
	 * The threshold reaches the browser, and nonsense is clamped.
	 */
	public function test_threshold_is_clamped(): void {
		$this->require_block();

		$this->assertStringContainsString( '"threshold":800', $this->render( array( 'threshold' => 800 ) ) );
		$this->assertStringContainsString( '"threshold":0', $this->render( array( 'threshold' => -50 ) ) );
		$this->assertStringContainsString( '"threshold":20000', $this->render( array( 'threshold' => 99999 ) ) );
	}

	/**
	 * The control is named whether or not its label is on screen.
	 */
	public function test_the_button_is_always_named(): void {
		$this->require_block();

		$icon_only = $this->render();

		$this->assertStringContainsString( 'screen-reader-text', $icon_only );
		$this->assertStringContainsString( 'Back to top', $icon_only );

		$with_label = $this->render( array( 'appearance' => 'icon-label' ) );

		$this->assertStringContainsString( 'sm-back-to-top__label', $with_label );
		$this->assertStringNotContainsString( 'screen-reader-text', $with_label );
	}

	/**
	 * The icon reaches the markup.
	 *
	 * Because suitemart_get_icon() returns markup rather than printing it, and
	 * an icon-only button that lost its call renders as an empty circle.
	 */
	public function test_the_icon_is_rendered(): void {
		$this->require_block();

		$this->assertStringContainsString( '<svg', $this->render() );
	}

	/**
	 * A custom label is text, and reaches the page as text.
	 */
	public function test_label_is_escaped(): void {
		$this->require_block();

		$html = $this->render( array( 'label' => '<script>alert(1)</script>' ) );

		$this->assertStringNotContainsString( '<script>alert(1)</script>', $html );
	}

	/**
	 * An unknown corner falls back rather than reaching the class list.
	 */
	public function test_position_is_validated(): void {
		$this->require_block();

		$html = $this->render( array( 'position' => 'middle' ) );

		$this->assertStringNotContainsString( 'sm-back-to-top--middle', $html );
		$this->assertStringContainsString( 'sm-back-to-top--end', $html );
	}
}
