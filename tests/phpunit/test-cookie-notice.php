<?php
/**
 * Cookie notice block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/cookie-notice.
 */
class Test_Cookie_Notice extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/cookie-notice' ) ) {
			$this->markTestSkipped( 'suitemart/cookie-notice is not registered here.' );
		}
	}

	/**
	 * Renders the block through do_blocks(), so directive processing runs.
	 *
	 * @param array<string, mixed> $attrs Block attributes.
	 * @param string               $inner Inner blocks markup.
	 * @return string Rendered markup.
	 */
	private function render( array $attrs = array(), string $inner = '<!-- wp:paragraph --><p>We use cookies.</p><!-- /wp:paragraph -->' ): string {
		return do_blocks(
			sprintf(
				'<!-- wp:suitemart/cookie-notice %1$s -->%2$s<!-- /wp:suitemart/cookie-notice -->',
				wp_json_encode( (object) $attrs ),
				$inner
			)
		);
	}

	/**
	 * The notice is served hidden.
	 *
	 * The server cannot know whether this visitor has already answered — the
	 * answer lives in localStorage so that pages stay cacheable — so the notice
	 * has to start closed and be opened by the browser once it finds nothing
	 * stored. Served visible, it would flash at everyone who had dismissed it.
	 */
	public function test_the_notice_is_served_hidden(): void {
		$this->require_block();

		$html = $this->render();

		$this->assertMatchesRegularExpression( '/<div[^>]*\shidden[=\s>]/', $html );
		$this->assertStringContainsString( 'data-wp-bind--hidden="!context.isOpen"', $html );
		$this->assertStringContainsString( '"isOpen":false', $html );
		$this->assertStringContainsString( 'data-wp-init="callbacks.decideVisibility"', $html );
	}

	/**
	 * Both buttons are present, and neither is pre-selected.
	 *
	 * Declining has to be as easy as accepting. That is mostly a CSS promise,
	 * but the markup half of it is checkable: two real buttons, same class stem,
	 * decline first in source order so it is also first in the tab order.
	 */
	public function test_both_choices_are_offered(): void {
		$this->require_block();

		$html = $this->render();

		$this->assertStringContainsString( 'data-wp-on--click="actions.decline"', $html );
		$this->assertStringContainsString( 'data-wp-on--click="actions.accept"', $html );

		$decline = strpos( $html, 'sm-cookie-notice__button--decline' );
		$accept  = strpos( $html, 'sm-cookie-notice__button--accept' );

		$this->assertIsInt( $decline );
		$this->assertIsInt( $accept );
		$this->assertLessThan( $accept, $decline );
	}

	/**
	 * The region is named for screen readers.
	 */
	public function test_the_region_is_named(): void {
		$this->require_block();

		$this->assertStringContainsString( 'role="region"', $this->render() );
		$this->assertStringContainsString( 'aria-label="Cookie notice"', $this->render() );
		$this->assertStringContainsString(
			'aria-label="Your privacy"',
			$this->render( array( 'regionLabel' => 'Your privacy' ) )
		);
	}

	/**
	 * Labels are text, and reach the page as text.
	 */
	public function test_labels_are_escaped(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'acceptLabel'  => '<script>alert(1)</script>',
				'declineLabel' => '"><script>alert(2)</script>',
				'regionLabel'  => '"><img onerror=alert(3)>',
			)
		);

		$this->assertStringNotContainsString( '<script>', $html );
		$this->assertStringNotContainsString( '<img onerror', $html );
	}

	/**
	 * Blank labels fall back rather than rendering an unnamed button.
	 */
	public function test_blank_labels_fall_back(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'acceptLabel'  => '   ',
				'declineLabel' => '',
			)
		);

		$squashed = (string) preg_replace( '/\s+/', '', $html );

		$this->assertStringContainsString( '>Accept<', $squashed );
		$this->assertStringContainsString( '>Decline<', $squashed );
	}

	/**
	 * An unknown position falls back rather than reaching the class list.
	 */
	public function test_position_is_validated(): void {
		$this->require_block();

		$html = $this->render( array( 'position' => 'top' ) );

		$this->assertStringNotContainsString( 'sm-cookie-notice--top', $html );
		$this->assertStringContainsString( 'sm-cookie-notice--bottom', $html );
	}

	/**
	 * With no message, the block renders nothing at all.
	 *
	 * A consent notice with no text is not a smaller notice, it is a pair of
	 * buttons asking the visitor to agree to nothing.
	 */
	public function test_an_empty_notice_renders_nothing(): void {
		$this->require_block();

		$this->assertSame( '', trim( $this->render( array(), '' ) ) );
	}
}
