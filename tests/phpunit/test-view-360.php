<?php
/**
 * 360° view block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/view-360.
 */
class Test_View_360 extends WP_UnitTestCase {

	/**
	 * Attachment ids created for the tests.
	 *
	 * @var array<int, int>
	 */
	private array $frames = array();

	/**
	 * Creates four attachments to use as frames.
	 */
	public function set_up(): void {
		parent::set_up();

		$this->frames = array();

		for ( $i = 0; $i < 4; $i++ ) {
			// The attachment factory takes the file as its first argument, not
			// as an `args` key — passing it as one silently produces an
			// attachment with no file, and wp_get_attachment_image() then
			// returns an empty string rather than failing.
			$this->frames[] = self::factory()->attachment->create_object(
				"frame-{$i}.jpg",
				0,
				array( 'post_mime_type' => 'image/jpeg' )
			);
		}
	}

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/view-360' ) ) {
			$this->markTestSkipped( 'suitemart/view-360 is not registered here.' );
		}
	}

	/**
	 * Renders the block through do_blocks(), so directive processing runs.
	 *
	 * @param array<string, mixed> $attrs Block attributes.
	 * @return string Rendered markup.
	 */
	private function render( array $attrs = array() ): string {
		$attrs = array_merge( array( 'frames' => $this->frames ), $attrs );

		return do_blocks(
			sprintf( '<!-- wp:suitemart/view-360 %s /-->', wp_json_encode( (object) $attrs ) )
		);
	}

	/**
	 * A single photograph is not a rotation.
	 */
	public function test_fewer_than_two_frames_renders_nothing(): void {
		$this->require_block();

		$this->assertStringNotContainsString( 'sm-view-360__frames', $this->render( array( 'frames' => array() ) ) );
		$this->assertStringNotContainsString(
			'sm-view-360__frames',
			$this->render( array( 'frames' => array( $this->frames[0] ) ) )
		);
	}

	/**
	 * The server picks the first frame, and only the first frame.
	 *
	 * This is the assertion the block was rewritten for. The first attempt
	 * moved a class from a watch callback, and Preact put the class back on its
	 * next render, so the viewer skipped frames. Binding `hidden` per frame
	 * against derived state — declared in PHP as well as in JavaScript — is
	 * what makes the served HTML correct on its own.
	 */
	public function test_exactly_one_frame_is_visible_before_hydration(): void {
		$this->require_block();

		$html = $this->render();

		preg_match_all( '/<img[^>]*class="[^"]*sm-view-360__frame[^"]*"[^>]*>/', $html, $matches );

		$this->assertCount( 4, $matches[0], 'Every frame should be in the markup.' );

		$visible = array_values(
			array_filter(
				$matches[0],
				// Matched as a whole attribute: every frame also carries
				// `data-wp-bind--hidden`, so a substring test would call them
				// all hidden and pass while the block was broken.
				static fn ( string $tag ): bool => 1 !== preg_match( '/\shidden[=\s>]/', $tag )
			)
		);

		$this->assertCount( 1, $visible, 'Exactly one frame should be visible.' );
		$this->assertStringContainsString( 'frame-0.jpg', $visible[0], 'The first frame should be the visible one.' );
	}

	/**
	 * Each frame knows its own position, so the binding has something to
	 * compare against.
	 */
	public function test_each_frame_carries_its_index(): void {
		$this->require_block();

		$html = $this->render();

		foreach ( range( 0, 3 ) as $position ) {
			// Entity-encoded, because this context rides on an attribute built
			// by wp_get_attachment_image() rather than by
			// wp_interactivity_data_wp_context(), which quotes with apostrophes.
			$this->assertStringContainsString(
				sprintf( 'data-wp-context="{&quot;frame&quot;:%d}"', $position ),
				$html
			);
		}
	}

	/**
	 * Reversing swaps which photograph leads.
	 */
	public function test_reverse_flips_the_sequence(): void {
		$this->require_block();

		$html = $this->render( array( 'reverse' => true ) );

		preg_match( '/<img[^>]*class="[^"]*sm-view-360__frame[^"]*"[^>]*>/', $html, $first );

		$this->assertStringContainsString( 'frame-3.jpg', $first[0] );
	}

	/**
	 * Impossible ids are dropped rather than rendered.
	 */
	public function test_invalid_frame_ids_are_dropped(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'frames' => array( $this->frames[0], 0, -7, $this->frames[1] ),
			)
		);

		// Anchored to <img> on purpose: the container's own class is
		// `sm-view-360__frames`, which a looser pattern counts as a frame.
		preg_match_all( '/<img[^>]*class="[^"]*sm-view-360__frame[^"]*"/', $html, $matches );

		$this->assertCount( 2, $matches[0] );
	}

	/**
	 * A frame list of the wrong type never reaches render.php at all.
	 *
	 * WordPress validates attributes against the block's schema and falls back
	 * to the default when they do not match, so a hand-edited list holding
	 * strings arrives here as an empty array. Worth pinning down: it means the
	 * filtering in render.php is a second line of defence, not the only one.
	 */
	public function test_a_frame_list_of_the_wrong_type_renders_nothing(): void {
		$this->require_block();

		$html = $this->render( array( 'frames' => array( 'one', 'two', 'three' ) ) );

		$this->assertStringNotContainsString( 'sm-view-360__frames', $html );
	}

	/**
	 * The stack is described once, not once per frame.
	 */
	public function test_the_stack_carries_the_description(): void {
		$this->require_block();

		$html = $this->render( array( 'label' => 'A walnut chair' ) );

		$this->assertStringContainsString( 'role="img"', $html );
		$this->assertStringContainsString( 'aria-label="A walnut chair"', $html );

		// Every frame decorative — otherwise the object is announced four times.
		preg_match_all( '/<img[^>]*class="[^"]*sm-view-360__frame[^"]*"[^>]*>/', $html, $matches );

		foreach ( $matches[0] as $tag ) {
			$this->assertStringContainsString( 'alt=""', $tag );
		}
	}

	/**
	 * Without a description the stack still has an accessible name.
	 */
	public function test_unlabelled_view_still_has_a_name(): void {
		$this->require_block();

		$this->assertStringContainsString( 'aria-label="360 degree view"', $this->render() );
	}

	/**
	 * The play toggle only appears when it has something to toggle, and its
	 * pressed state is rendered rather than left to hydration.
	 */
	public function test_the_auto_rotate_toggle_is_optional(): void {
		$this->require_block();

		$this->assertStringNotContainsString( 'sm-view-360__button--play', $this->render() );

		$html = $this->render( array( 'autoRotate' => true ) );

		$this->assertStringContainsString( 'sm-view-360__button--play', $html );
		$this->assertStringContainsString( 'aria-pressed="true"', $html );
	}

	/**
	 * Every control has an accessible name and an icon in it.
	 *
	 * Because suitemart_get_icon() returns markup rather than printing it, and
	 * four blocks have already shipped with a bare call and an empty control.
	 */
	public function test_controls_are_named_and_carry_icons(): void {
		$this->require_block();

		$html = $this->render( array( 'autoRotate' => true ) );

		preg_match_all( '/<button[^>]*class="[^"]*sm-view-360__button[^"]*"[^>]*>(.*?)<\/button>/s', $html, $matches );

		$this->assertCount( 3, $matches[1] );

		foreach ( $matches[1] as $inner ) {
			$this->assertStringContainsString( '<svg', $inner );
			$this->assertStringContainsString( 'screen-reader-text', $inner );
		}
	}
}
