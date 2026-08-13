<?php
/**
 * Popup block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/popup.
 */
class Test_Popup extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/popup' ) ) {
			$this->markTestSkipped( 'suitemart/popup is not registered here.' );
		}
	}

	/**
	 * Renders the block through do_blocks(), so directive processing runs.
	 *
	 * @param array<string, mixed> $attrs Block attributes.
	 * @param string               $inner Inner blocks markup.
	 * @return string Rendered markup.
	 */
	private function render( array $attrs = array(), string $inner = '<!-- wp:paragraph --><p>Join the list.</p><!-- /wp:paragraph -->' ): string {
		return do_blocks(
			sprintf(
				'<!-- wp:suitemart/popup %1$s -->%2$s<!-- /wp:suitemart/popup -->',
				wp_json_encode( (object) $attrs ),
				$inner
			)
		);
	}

	/**
	 * It is a dialog, and it is served closed.
	 *
	 * `<dialog>` is what makes the block a modal — focus trapping, inert
	 * background, Escape and the top layer all come from it — and a served
	 * `open` attribute would make it a non-modal dialog sitting in the flow.
	 */
	public function test_it_is_a_closed_dialog(): void {
		$this->require_block();

		$html = $this->render();

		$this->assertStringContainsString( '<dialog', $html );
		$this->assertDoesNotMatchRegularExpression( '/<dialog[^>]*\sopen[=\s>]/', $html );
	}

	/**
	 * The dialog is named, and can be named something useful.
	 */
	public function test_the_dialog_is_named(): void {
		$this->require_block();

		$this->assertStringContainsString( 'aria-label="Notice"', $this->render() );
		$this->assertStringContainsString(
			'aria-label="Newsletter signup"',
			$this->render( array( 'label' => 'Newsletter signup' ) )
		);
	}

	/**
	 * The trigger and its number reach the browser.
	 */
	public function test_triggers_reach_the_browser(): void {
		$this->require_block();

		$delayed = $this->render(
			array(
				'trigger' => 'delay',
				'delay'   => 12,
			)
		);

		$this->assertStringContainsString( '"trigger":"delay"', $delayed );
		$this->assertStringContainsString( '"delay":12000', $delayed );

		$scrolled = $this->render(
			array(
				'trigger'   => 'scroll',
				'threshold' => 1200,
			)
		);

		$this->assertStringContainsString( '"trigger":"scroll"', $scrolled );
		$this->assertStringContainsString( '"threshold":1200', $scrolled );

		$this->assertStringContainsString(
			'"trigger":"exit"',
			$this->render( array( 'trigger' => 'exit' ) )
		);
	}

	/**
	 * Nonsense falls back rather than reaching the browser.
	 */
	public function test_attributes_are_validated(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'trigger'  => 'telepathy',
				'delay'    => 9999,
				'maxWidth' => 4,
			)
		);

		$this->assertStringContainsString( '"trigger":"delay"', $html );
		$this->assertStringContainsString( '"delay":120000', $html );
		$this->assertStringContainsString( '--sm-popup-max-width:240px', $html );
	}

	/**
	 * Showing once needs a key, and says so by leaving the key empty.
	 *
	 * The editor writes the key. A block hand-written in a pattern without one
	 * must not end up claiming a memory it has no way to keep.
	 */
	public function test_showing_once_needs_a_key(): void {
		$this->require_block();

		$this->assertStringContainsString( '"key":""', $this->render() );
		$this->assertStringContainsString(
			'"key":"promo1"',
			$this->render(
				array(
					'showOnce' => true,
					'onceKey'  => 'promo1',
				)
			)
		);
		$this->assertStringContainsString(
			'"key":""',
			$this->render(
				array(
					'showOnce' => false,
					'onceKey'  => 'promo1',
				)
			)
		);
	}

	/**
	 * The key is a key, whatever was saved in the attribute.
	 */
	public function test_the_key_is_sanitised(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'showOnce' => true,
				'onceKey'  => '"><script>alert(1)</script>',
			)
		);

		$this->assertStringNotContainsString( '<script>', $html );
	}

	/**
	 * Labels are text, and reach the page as text.
	 */
	public function test_labels_are_escaped(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'label'      => '"><img onerror=alert(1)>',
				'closeLabel' => '<script>alert(2)</script>',
			)
		);

		$this->assertStringNotContainsString( '<script>', $html );
		$this->assertStringNotContainsString( '<img onerror', $html );
	}

	/**
	 * Backdrop closing is on by default and can be turned off.
	 */
	public function test_backdrop_closing_is_configurable(): void {
		$this->require_block();

		$this->assertStringContainsString( '"overlayClose":true', $this->render() );
		$this->assertStringContainsString(
			'"overlayClose":false',
			$this->render( array( 'overlayClose' => false ) )
		);
	}

	/**
	 * The close button is a named button.
	 */
	public function test_the_close_button_is_named(): void {
		$this->require_block();

		$html = $this->render();

		$this->assertStringContainsString( 'data-wp-on--click="actions.close"', $html );
		$this->assertStringContainsString( 'screen-reader-text', $html );
		$this->assertStringContainsString( 'Close', $html );
	}

	/**
	 * An empty popup renders nothing rather than an empty dialog.
	 */
	public function test_an_empty_popup_renders_nothing(): void {
		$this->require_block();

		$this->assertSame( '', trim( $this->render( array(), '' ) ) );
	}
}
