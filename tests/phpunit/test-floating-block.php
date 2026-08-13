<?php
/**
 * Floating block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/floating-block.
 */
class Test_Floating_Block extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/floating-block' ) ) {
			$this->markTestSkipped( 'suitemart/floating-block is not registered here.' );
		}
	}

	/**
	 * Renders the block through do_blocks(), so directive processing runs.
	 *
	 * @param array<string, mixed> $attrs Block attributes.
	 * @param string               $inner Inner blocks markup.
	 * @return string Rendered markup.
	 */
	private function render( array $attrs = array(), string $inner = '<!-- wp:paragraph --><p>Ten percent off.</p><!-- /wp:paragraph -->' ): string {
		return do_blocks(
			sprintf(
				'<!-- wp:suitemart/floating-block %1$s -->%2$s<!-- /wp:suitemart/floating-block -->',
				wp_json_encode( (object) $attrs ),
				$inner
			)
		);
	}

	/**
	 * With nothing to check first, the panel is served open.
	 *
	 * Which is the whole reason the default trigger is "immediate": served
	 * open, the panel and its contents are there with JavaScript switched off.
	 */
	public function test_an_immediate_panel_is_served_open(): void {
		$this->require_block();

		$html = $this->render();

		$this->assertDoesNotMatchRegularExpression( '/<div[^>]*\shidden[=\s>]/', $html );
		$this->assertStringContainsString( 'Ten percent off.', $html );
		$this->assertStringContainsString( '"isOpen":true', $html );
	}

	/**
	 * A panel with a trigger is served closed.
	 */
	public function test_a_triggered_panel_is_served_closed(): void {
		$this->require_block();

		foreach ( array( 'scroll', 'delay' ) as $trigger ) {
			$html = $this->render( array( 'trigger' => $trigger ) );

			$this->assertMatchesRegularExpression( '/<div[^>]*\shidden[=\s>]/', $html, $trigger );
			$this->assertStringContainsString( '"isOpen":false', $html, $trigger );
		}
	}

	/**
	 * So is a panel that might have been dismissed already.
	 *
	 * The dismissal lives in localStorage, so the server cannot see it. Served
	 * open, the panel would flash on every load at someone who had closed it.
	 */
	public function test_a_remembering_panel_is_served_closed(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'remember'    => true,
				'rememberKey' => 'promo1',
			)
		);

		$this->assertMatchesRegularExpression( '/<div[^>]*\shidden[=\s>]/', $html );
		$this->assertStringContainsString( '"key":"promo1"', $html );
	}

	/**
	 * Without a key there is no memory, so there is nothing to check.
	 *
	 * The editor writes the key when the toggle goes on. A block saved before
	 * that, or hand-written in a pattern without one, must not end up served
	 * hidden with no way for the browser to decide it should open.
	 */
	public function test_remembering_without_a_key_is_ignored(): void {
		$this->require_block();

		$html = $this->render( array( 'remember' => true ) );

		$this->assertDoesNotMatchRegularExpression( '/<div[^>]*\shidden[=\s>]/', $html );
		$this->assertStringContainsString( '"key":""', $html );
	}

	/**
	 * A panel that cannot be closed has nothing to remember either.
	 */
	public function test_memory_needs_a_close_button(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'dismissible' => false,
				'remember'    => true,
				'rememberKey' => 'promo1',
			)
		);

		$this->assertStringNotContainsString( 'sm-floating-block__dismiss', $html );
		$this->assertStringContainsString( '"key":""', $html );
		$this->assertDoesNotMatchRegularExpression( '/<div[^>]*\shidden[=\s>]/', $html );
	}

	/**
	 * The close button is a named button.
	 */
	public function test_the_close_button_is_named(): void {
		$this->require_block();

		$html = $this->render();

		$this->assertStringContainsString( 'data-wp-on--click="actions.dismiss"', $html );
		$this->assertStringContainsString( 'screen-reader-text', $html );
		$this->assertStringContainsString( 'Close', $html );
		$this->assertStringContainsString( 'Dismiss offer', $this->render( array( 'dismissLabel' => 'Dismiss offer' ) ) );
	}

	/**
	 * The delay reaches the browser in milliseconds, and is clamped.
	 */
	public function test_delay_is_converted_and_clamped(): void {
		$this->require_block();

		$this->assertStringContainsString(
			'"delay":8000',
			$this->render(
				array(
					'trigger' => 'delay',
					'delay'   => 8,
				)
			)
		);
		$this->assertStringContainsString(
			'"delay":120000',
			$this->render(
				array(
					'trigger' => 'delay',
					'delay'   => 9999,
				)
			)
		);
		$this->assertStringContainsString(
			'"delay":0',
			$this->render(
				array(
					'trigger' => 'delay',
					'delay'   => -3,
				)
			)
		);
	}

	/**
	 * Position and width reach the markup, and nonsense does not.
	 */
	public function test_position_and_width_are_validated(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'position' => 'bottom-start',
				'maxWidth' => 280,
			)
		);

		$this->assertStringContainsString( 'sm-floating-block--bottom-start', $html );
		$this->assertStringContainsString( '--sm-floating-max-width:280px', $html );

		$junk = $this->render(
			array(
				'position' => 'nowhere',
				'maxWidth' => 99999,
			)
		);

		$this->assertStringNotContainsString( 'sm-floating-block--nowhere', $junk );
		$this->assertStringContainsString( 'sm-floating-block--bottom-end', $junk );
		$this->assertStringContainsString( '--sm-floating-max-width:1200px', $junk );
	}

	/**
	 * The key is a key, whatever was saved in the attribute.
	 */
	public function test_the_key_is_sanitised(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'remember'    => true,
				'rememberKey' => '"><script>alert(1)</script>',
			)
		);

		$this->assertStringNotContainsString( '<script>', $html );
	}

	/**
	 * The mobile opt-out reaches the class list only when asked for.
	 */
	public function test_hiding_on_mobile_is_opt_in(): void {
		$this->require_block();

		$this->assertStringNotContainsString( 'is-hidden-on-mobile', $this->render() );
		$this->assertStringContainsString(
			'is-hidden-on-mobile',
			$this->render( array( 'hideOnMobile' => true ) )
		);
	}

	/**
	 * An empty panel renders nothing rather than an empty box in the corner.
	 */
	public function test_an_empty_panel_renders_nothing(): void {
		$this->require_block();

		$this->assertSame( '', trim( $this->render( array(), '' ) ) );
	}
}
