<?php
/**
 * Block render tests.
 *
 * Every Suitemart block renders dynamically (decision 7), which means a bad
 * attribute reaches PHP rather than being caught at save time. Block attributes
 * come from post content, so after a hand edit, a failed migration or a pattern
 * written by someone else they can be any type at all.
 *
 * These tests assert the only two properties that really matter for a shipped
 * theme: a render callback never fatals, and it never emits unescaped input.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Renders every registered block against hostile attributes.
 */
class Test_Block_Render extends WP_UnitTestCase {

	/**
	 * Attribute sets every block is rendered with.
	 *
	 * `malformed` deliberately supplies the wrong PHP type for each attribute,
	 * plus two payloads that must never survive to the output.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function attribute_cases(): array {
		return array(
			'defaults'  => array(),
			'empty'     => array(
				'mobileBreakpoint' => '',
				'submenuTrigger'   => '',
				'ariaLabel'        => '',
				'label'            => '',
				'url'              => '',
				'panelWidth'       => '',
				'align'            => '',
				'badge'            => '',
			),
			'malformed' => array(
				'mobileBreakpoint' => array( 'unexpected array' ),
				'submenuTrigger'   => 99,
				'ariaLabel'        => null,
				'label'            => '<script>alert(1)</script>',
				'url'              => 'javascript:alert(1)',
				'hasPanel'         => 'yes',
				'badge'            => 12345,
				'panelWidth'       => 'not-a-width',
				'align'            => false,
				'openIcon'         => '../../etc/passwd',
			),
		);
	}

	/**
	 * Returns every registered Suitemart block name.
	 *
	 * @return array<int, array<int, string>>
	 */
	public function data_suitemart_blocks(): array {
		$names = array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() );
		$ours  = array_filter(
			$names,
			static fn ( string $name ): bool => str_starts_with( $name, 'suitemart/' )
		);

		$this->assertNotEmpty( $ours, 'No Suitemart blocks are registered; the theme did not load.' );

		return array_map( static fn ( string $name ): array => array( $name ), array_values( $ours ) );
	}

	/**
	 * A block must render without fatals for any attribute values.
	 *
	 * @dataProvider data_suitemart_blocks
	 *
	 * @param string $block_name Registered block name.
	 */
	public function test_renders_without_fatal( string $block_name ): void {
		foreach ( $this->attribute_cases() as $case => $attributes ) {
			$html = render_block(
				array(
					'blockName'    => $block_name,
					'attrs'        => $attributes,
					'innerBlocks'  => array(),
					'innerHTML'    => '<p>Inner content</p>',
					'innerContent' => array( '<p>Inner content</p>' ),
				)
			);

			$this->assertIsString(
				$html,
				sprintf( '%s did not return a string for the "%s" attribute set.', $block_name, $case )
			);
		}
	}

	/**
	 * A block must escape everything it takes from its attributes.
	 *
	 * @dataProvider data_suitemart_blocks
	 *
	 * @param string $block_name Registered block name.
	 */
	public function test_escapes_hostile_attributes( string $block_name ): void {
		$html = render_block(
			array(
				'blockName'    => $block_name,
				'attrs'        => $this->attribute_cases()['malformed'],
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		$this->assertStringNotContainsStringIgnoringCase(
			'<script',
			$html,
			sprintf( '%s emitted an unescaped script tag from its attributes.', $block_name )
		);

		$this->assertStringNotContainsStringIgnoringCase(
			'javascript:',
			$html,
			sprintf( '%s emitted a javascript: URL from its attributes.', $block_name )
		);
	}

	/**
	 * Blocks that bind text or labels must still render them without JavaScript.
	 *
	 * Interactivity directives are processed on the server as well as in the
	 * browser, and an expression the server cannot resolve does not leave the
	 * markup alone — it blanks the element or strips the attribute. A getter
	 * defined only in view.js is exactly such an expression, which shipped
	 * empty counters and an unlabelled pause button before this test existed.
	 *
	 * Binding from per-block context instead fixes both, and this asserts it
	 * stays fixed.
	 *
	 * @dataProvider data_bound_output
	 *
	 * @param string               $block_name Registered block name.
	 * @param array<string, mixed> $attributes Attributes producing visible output.
	 * @param string               $pattern    Regex the rendered markup must match.
	 * @param string               $why        What breaks when it does not.
	 * @param string               $inner      Inner block markup, for container blocks
	 *                                         that render nothing when empty.
	 */
	public function test_bound_output_survives_without_javascript(
		string $block_name,
		array $attributes,
		string $pattern,
		string $why,
		string $inner = ''
	): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( $block_name ) ) {
			$this->markTestSkipped( sprintf( '%s is not registered here.', $block_name ) );
		}

		$html = render_block(
			array(
				'blockName'    => $block_name,
				'attrs'        => $attributes,
				'innerBlocks'  => array(),
				'innerHTML'    => $inner,
				'innerContent' => '' === $inner ? array() : array( $inner ),
			)
		);

		$this->assertMatchesRegularExpression( $pattern, $html, $why );
	}

	/**
	 * Blocks whose visible output comes from a directive binding.
	 *
	 * @return array<string, array{0: string, 1: array<string, mixed>, 2: string, 3: string, 4?: string}>
	 */
	public function data_bound_output(): array {
		return array(
			'counter value'      => array(
				'suitemart/counter',
				array( 'end' => 4200 ),
				'/sm-counter__number[^>]*>\s*4,?200\s*</',
				'The counter rendered no number, so it is blank until JavaScript runs.',
			),
			'countdown digits'   => array(
				'suitemart/countdown',
				array( 'endDate' => '2099-01-01T00:00:00' ),
				'/sm-countdown__value[^>]*>\s*\d+\s*</',
				'The countdown rendered no digits, so it is blank until JavaScript runs.',
			),
			'marquee pause name' => array(
				'suitemart/marquee',
				array(),
				'/<button[^>]*sm-marquee__toggle[^>]*aria-label="[^"]+"|aria-label="[^"]+"[^>]*sm-marquee__toggle/',
				'The pause button has no accessible name, so a screen reader announces it only as "button".',
				'<p>Free delivery</p>',
			),
		);
	}

	/**
	 * Commerce blocks must not register when WooCommerce is unavailable.
	 */
	public function test_commerce_blocks_require_woocommerce(): void {
		if ( suitemart_has_woocommerce() ) {
			$this->markTestSkipped( 'WooCommerce is active in this environment.' );
		}

		$registry = WP_Block_Type_Registry::get_instance();

		foreach ( suitemart_woocommerce_block_slugs() as $slug ) {
			$this->assertFalse(
				$registry->is_registered( 'suitemart/' . $slug ),
				sprintf( 'suitemart/%s registered without WooCommerce and will fatal on render.', $slug )
			);
		}
	}
}
