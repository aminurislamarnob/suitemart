<?php
/**
 * Image hotspots block tests.
 *
 * The interesting assertions here are the two that no amount of "does it
 * render" checking would catch: that server-side directive processing leaves
 * `aria-expanded` in place, and that two hotspot images on one page do not
 * share ids. Both are failures the block would otherwise ship with, looking
 * entirely correct in a browser until a screen reader or a second instance is
 * involved.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/hotspots and suitemart/hotspot.
 */
class Test_Hotspots extends WP_UnitTestCase {

	/**
	 * A one-pixel image URL. Nothing fetches it; only the markup is inspected.
	 */
	private const IMAGE = 'https://example.org/room.jpg';

	/**
	 * Skips when the blocks are absent.
	 */
	private function require_blocks(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/hotspots' ) ) {
			$this->markTestSkipped( 'suitemart/hotspots is not registered here.' );
		}
	}

	/**
	 * Builds hotspots block markup with the given markers.
	 *
	 * `do_blocks()` rather than WP_Block::render(), because directive
	 * processing runs on the `render_block` filter — rendering the block object
	 * directly would skip the very step these tests exist to check.
	 *
	 * @param array<int, array<string, mixed>> $pins    Marker attributes.
	 * @param array<string, mixed>             $parent_attrs Parent attributes.
	 * @return string Rendered markup.
	 */
	private function render( array $pins, array $parent_attrs = array() ): string {
		$parent_attrs = array_merge( array( 'mediaUrl' => self::IMAGE ), $parent_attrs );

		$markers = '';

		foreach ( $pins as $pin ) {
			$markers .= sprintf(
				'<!-- wp:suitemart/hotspot %s --><!-- wp:paragraph --><p>Note.</p><!-- /wp:paragraph --><!-- /wp:suitemart/hotspot -->',
				wp_json_encode( (object) $pin )
			);
		}

		return do_blocks(
			sprintf(
				'<!-- wp:suitemart/hotspots %s -->%s<!-- /wp:suitemart/hotspots -->',
				wp_json_encode( (object) $parent_attrs ),
				$markers
			)
		);
	}

	/**
	 * Without an image there is nothing to pin markers to.
	 */
	public function test_no_image_renders_nothing(): void {
		$this->require_blocks();

		$html = do_blocks( '<!-- wp:suitemart/hotspots /-->' );

		$this->assertStringNotContainsString( 'sm-hotspots__frame', $html );
	}

	/**
	 * A marker with no panel content is not a marker worth rendering.
	 */
	public function test_empty_marker_renders_nothing(): void {
		$this->require_blocks();

		$html = do_blocks(
			sprintf(
				'<!-- wp:suitemart/hotspots {"mediaUrl":"%s"} --><!-- wp:suitemart/hotspot /--><!-- /wp:suitemart/hotspots -->',
				self::IMAGE
			)
		);

		$this->assertStringContainsString( 'sm-hotspots__frame', $html );
		$this->assertStringNotContainsString( 'sm-hotspots__marker', $html );
	}

	/**
	 * Server-side directive processing must not strip the disclosure state.
	 *
	 * This is the trap in AGENTS.md §5 made into a test. Binding to a `state`
	 * getter that only exists in JavaScript resolves to null on the server,
	 * which deletes `aria-expanded` from the button and leaves a control that
	 * announces nothing about what it does. Binding to seeded context, as this
	 * block does, survives.
	 */
	public function test_disclosure_state_survives_directive_processing(): void {
		$this->require_blocks();

		$html = $this->render( array( array( 'label' => 'The lamp' ) ) );

		$this->assertStringContainsString( 'aria-expanded="false"', $html );
		$this->assertStringContainsString( 'data-wp-bind--aria-expanded="context.isOpen"', $html );

		// And the panel is closed in the served HTML rather than flashing open
		// until the view module loads.
		$this->assertMatchesRegularExpression( '/class="sm-hotspots__panel"[^>]*\shidden/', $html );
	}

	/**
	 * Two images on one page must not share marker ids.
	 */
	public function test_ids_are_unique_across_instances(): void {
		$this->require_blocks();

		$html = $this->render( array( array( 'label' => 'One' ) ) )
			. $this->render( array( array( 'label' => 'Two' ) ) );

		preg_match_all( '/id="(sm-hotspot-[^"]+)"/', $html, $matches );

		$ids = $matches[1];

		$this->assertCount( 4, $ids, 'Expected a button id and a panel id per marker.' );
		$this->assertSame( $ids, array_unique( $ids ), 'Marker ids repeated across instances.' );
	}

	/**
	 * Each button points at its own panel, not at a sibling's.
	 */
	public function test_button_controls_its_own_panel(): void {
		$this->require_blocks();

		$html = $this->render(
			array(
				array( 'label' => 'One' ),
				array( 'label' => 'Two' ),
			)
		);

		preg_match_all( '/aria-controls="([^"]+)"/', $html, $controls );
		preg_match_all( '/class="sm-hotspots__panel"[^>]*id="([^"]+)"/', $html, $panels );

		$this->assertCount( 2, $controls[1] );
		$this->assertSame( $controls[1], $panels[1] );
	}

	/**
	 * Positions reach the markup, and impossible ones are clamped rather than
	 * placing a marker outside the picture.
	 */
	public function test_position_is_clamped(): void {
		$this->require_blocks();

		$html = $this->render(
			array(
				array(
					'x' => 30,
					'y' => 40,
				),
				array(
					'x' => 999,
					'y' => -12,
				),
			)
		);

		$this->assertStringContainsString( '--sm-hotspot-x:30%;--sm-hotspot-y:40%', $html );
		$this->assertStringContainsString( '--sm-hotspot-x:100%;--sm-hotspot-y:0%', $html );
	}

	/**
	 * A marker with no label still has an accessible name.
	 */
	public function test_unlabelled_marker_still_has_a_name(): void {
		$this->require_blocks();

		$html = $this->render( array( array() ) );

		$this->assertStringContainsString( 'Show details', $html );
	}

	/**
	 * A label is text, and reaches the page as text.
	 */
	public function test_label_is_escaped(): void {
		$this->require_blocks();

		$html = $this->render( array( array( 'label' => '<script>alert(1)</script>' ) ) );

		$this->assertStringNotContainsString( '<script>alert(1)</script>', $html );
	}

	/**
	 * An unknown placement falls back rather than reaching the class list.
	 */
	public function test_placement_is_validated(): void {
		$this->require_blocks();

		$html = $this->render( array( array( 'placement' => 'diagonally' ) ) );

		$this->assertStringNotContainsString( 'diagonally', $html );
		$this->assertStringContainsString( 'sm-hotspots__point--top', $html );
	}
}
