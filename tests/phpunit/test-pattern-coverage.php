<?php
/**
 * Pattern coverage tests.
 *
 * A block nobody can find is a block nobody uses. Suitemart ships 52 of them,
 * and the inserter's block list is an alphabetical wall of names with no
 * indication of what any of them produce — so the pattern is how a block is
 * actually discovered, and the standing rule is that every block ships with at
 * least one.
 *
 * It is a rule that decays silently: adding a block is a commit of its own, the
 * pattern is the last step, and nothing before this file noticed when it was
 * skipped. Five blocks had reached `main` with no pattern at all — including
 * both halves of the wishlist and both halves of compare, four features a buyer
 * would have to already know existed to switch on.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Asserts every block is reachable from at least one pattern.
 */
class Test_Pattern_Coverage extends WP_UnitTestCase {

	/**
	 * Every block name declared under `src/`.
	 *
	 * Read from source rather than from the registry: the registry holds core
	 * and WooCommerce blocks too, and a block whose build is missing would
	 * quietly drop out of the check exactly when it most needs making.
	 *
	 * @return array<int, string>
	 */
	private function block_names(): array {
		$names = array();

		foreach ( (array) glob( SUITEMART_DIR . '/src/*/block.json' ) as $path ) {
			$metadata = json_decode( (string) file_get_contents( (string) $path ), true );

			if ( is_array( $metadata ) && ! empty( $metadata['name'] ) ) {
				$names[] = (string) $metadata['name'];
			}
		}

		sort( $names );

		return $names;
	}

	/**
	 * Every block must appear in at least one pattern.
	 */
	public function test_every_block_appears_in_a_pattern(): void {
		$patterns = '';

		foreach ( (array) glob( SUITEMART_DIR . '/patterns/*.php' ) as $path ) {
			$patterns .= (string) file_get_contents( (string) $path );
		}

		$uncovered = array();

		foreach ( $this->block_names() as $name ) {
			/*
			 * The trailing guard matters: a plain substring search for
			 * `suitemart/size-guide` also matches `suitemart/size-guide-button`,
			 * so a child block with a pattern would vouch for a parent without
			 * one. Block names are lowercase, digits and hyphens, so anything
			 * else ends the name.
			 */
			$pattern = '#' . preg_quote( $name, '#' ) . '(?![a-z0-9-])#';

			if ( ! preg_match( $pattern, $patterns ) ) {
				$uncovered[] = $name;
			}
		}

		$this->assertSame(
			array(),
			$uncovered,
			"These blocks appear in no pattern, so the only way to find them is to already know their name:\n" . implode( "\n", $uncovered )
		);
	}

	/**
	 * A pattern category with no patterns in it shows as an empty tab.
	 *
	 * Categories are declared in `inc/patterns.php` by hand, and the pattern
	 * files reference them by hand too, so the two drift: a renamed category
	 * leaves its old name orphaned in a header, and the pattern lands in the
	 * uncategorised pile rather than failing loudly.
	 */
	public function test_patterns_name_registered_categories(): void {
		suitemart_register_pattern_categories();

		$registered = WP_Block_Pattern_Categories_Registry::get_instance()->get_all_registered();
		$known      = array_column( $registered, 'name' );

		$unknown = array();

		foreach ( (array) glob( SUITEMART_DIR . '/patterns/*.php' ) as $path ) {
			$headers = get_file_data( (string) $path, array( 'categories' => 'Categories' ) );

			foreach ( explode( ',', (string) $headers['categories'] ) as $category ) {
				$category = trim( $category );

				// Only Suitemart's own categories are ours to guarantee; the
				// core ones a pattern also lists — `banner`, `woocommerce` —
				// are registered by WordPress and Woo, not here.
				if ( '' === $category || ! str_starts_with( $category, 'suitemart/' ) ) {
					continue;
				}

				if ( ! in_array( $category, $known, true ) ) {
					$unknown[] = sprintf( '%s lists %s', basename( (string) $path ), $category );
				}
			}
		}

		$this->assertSame(
			array(),
			$unknown,
			"These patterns name a category nothing registers, so they land uncategorised:\n" . implode( "\n", $unknown )
		);
	}

	/**
	 * A single-side border must carry its colour on that side.
	 *
	 * `"borderColor":"neutral-200"` puts `has-border-color` on the element, and
	 * core's rule for that class is `border-style: solid` — on all four sides.
	 * Pair it with `"border":{"top":{"width":"1px"}}` and only the top width is
	 * set; the other three fall back to `medium`, so a design asking for one hair
	 * rule renders a 3px box. It is invisible in the markup, invisible to phpcs,
	 * and six patterns shipped with it.
	 *
	 * The form that works puts colour, width and style inside the side:
	 * `"border":{"top":{"color":"var:preset|color|neutral-200","width":"1px","style":"solid"}}`.
	 */
	public function test_side_borders_do_not_use_a_block_wide_border_colour(): void {
		$offenders = array();

		foreach ( (array) glob( SUITEMART_DIR . '/patterns/*.php' ) as $path ) {
			$source = (string) file_get_contents( (string) $path );

			/*
			 * Matched on the serialised attributes rather than the rendered
			 * class, because the attributes are what an editor round-trip keeps.
			 * A `"border"` object naming a side, followed by `"borderColor"`
			 * before the next block comment, is the broken combination.
			 */
			if ( preg_match( '#"border":\{"(?:top|right|bottom|left)"[^\n]*?"borderColor":#', $source ) ) {
				$offenders[] = basename( (string) $path );
			}
		}

		$this->assertSame(
			array(),
			$offenders,
			"These patterns set a per-side border width alongside a block-wide borderColor, which draws all four sides:\n" . implode( "\n", $offenders )
		);
	}
}
