<?php
/**
 * Image comparison block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/compare-images.
 */
class Test_Compare_Images extends WP_UnitTestCase {

	private const BEFORE = 'https://example.org/before.jpg';
	private const AFTER  = 'https://example.org/after.jpg';

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/compare-images' ) ) {
			$this->markTestSkipped( 'suitemart/compare-images is not registered here.' );
		}
	}

	/**
	 * Renders the block through do_blocks(), so directive processing runs.
	 *
	 * @param array<string, mixed> $attrs Block attributes.
	 * @return string Rendered markup.
	 */
	private function render( array $attrs = array() ): string {
		$attrs = array_merge(
			array(
				'beforeUrl' => self::BEFORE,
				'afterUrl'  => self::AFTER,
			),
			$attrs
		);

		return do_blocks(
			sprintf( '<!-- wp:suitemart/compare-images %s /-->', wp_json_encode( (object) $attrs ) )
		);
	}

	/**
	 * One image is not a comparison.
	 */
	public function test_a_single_image_renders_nothing(): void {
		$this->require_block();

		$only_before = do_blocks(
			sprintf( '<!-- wp:suitemart/compare-images {"beforeUrl":"%s"} /-->', self::BEFORE )
		);
		$only_after  = do_blocks(
			sprintf( '<!-- wp:suitemart/compare-images {"afterUrl":"%s"} /-->', self::AFTER )
		);

		$this->assertStringNotContainsString( 'sm-compare-images__frame', $only_before );
		$this->assertStringNotContainsString( 'sm-compare-images__frame', $only_after );
	}

	/**
	 * The control is a real slider, not a div someone attached drag handlers to.
	 *
	 * Everything the block gets for free — arrow keys, Home and End, touch, the
	 * value announced as a percentage — comes from that one decision, so it is
	 * worth a test that would fail loudly if it were ever swapped out.
	 */
	public function test_the_control_is_a_native_range_input(): void {
		$this->require_block();

		$html = $this->render();

		$this->assertMatchesRegularExpression(
			'/<input[^>]*type="range"[^>]*class="sm-compare-images__range"/',
			$html
		);
		$this->assertMatchesRegularExpression( '/<input[^>]*\saria-label="[^"]+"/', $html );
	}

	/**
	 * The wipe position must survive server-side directive processing.
	 *
	 * A `state` getter composing this string in JavaScript resolves to null on
	 * the server, which strips the style attribute — and the block then serves
	 * both photographs stacked at full width, with the second one hiding the
	 * first entirely. Seeded context renders correctly before any script loads.
	 */
	public function test_start_position_survives_directive_processing(): void {
		$this->require_block();

		$html = $this->render( array( 'startPosition' => 30 ) );

		$this->assertStringContainsString( '--sm-compare-position:30%', $html );
		$this->assertStringContainsString( 'data-wp-bind--style="context.frameStyle"', $html );
		$this->assertStringContainsString( '"frameStyle":"--sm-compare-position:30%;"', $html );
	}

	/**
	 * An impossible starting position is clamped, not written out.
	 */
	public function test_start_position_is_clamped(): void {
		$this->require_block();

		$this->assertStringContainsString(
			'--sm-compare-position:100%',
			$this->render( array( 'startPosition' => 400 ) )
		);
		$this->assertStringContainsString(
			'--sm-compare-position:0%',
			$this->render( array( 'startPosition' => -5 ) )
		);
	}

	/**
	 * Both images carry their own alternative text.
	 */
	public function test_both_images_keep_their_alt_text(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'beforeAlt' => 'The wall as we found it',
				'afterAlt'  => 'The wall repointed',
			)
		);

		$this->assertStringContainsString( 'alt="The wall as we found it"', $html );
		$this->assertStringContainsString( 'alt="The wall repointed"', $html );
	}

	/**
	 * Captions repeat what the alt text already says, so they are hidden from
	 * assistive technology rather than announced twice.
	 */
	public function test_captions_are_decorative(): void {
		$this->require_block();

		$html = $this->render(
			array(
				'beforeLabel' => 'Before',
				'afterLabel'  => 'After',
			)
		);

		preg_match_all( '/<span class="sm-compare-images__label[^"]*"([^>]*)>/', $html, $matches );

		$this->assertCount( 2, $matches[1] );

		foreach ( $matches[1] as $attributes ) {
			$this->assertStringContainsString( 'aria-hidden="true"', $attributes );
		}
	}

	/**
	 * An unknown orientation falls back rather than reaching the class list.
	 */
	public function test_orientation_is_validated(): void {
		$this->require_block();

		$html = $this->render( array( 'orientation' => 'sideways' ) );

		$this->assertStringNotContainsString( 'sideways', $html );
		$this->assertStringContainsString( 'sm-compare-images--horizontal', $html );
	}

	/**
	 * A caption is text, and reaches the page as text.
	 */
	public function test_labels_are_escaped(): void {
		$this->require_block();

		$html = $this->render( array( 'beforeLabel' => '<script>alert(1)</script>' ) );

		$this->assertStringNotContainsString( '<script>alert(1)</script>', $html );
	}
}
