<?php
/**
 * Serialized WooCommerce block markup tests.
 *
 * The editor re-runs each block's `save` function on load and compares the
 * result against the markup it parsed. Where they disagree the block opens as
 * "Block contains unexpected or invalid content" behind an Attempt recovery
 * button, and a pattern made of those is a pattern nobody can use.
 *
 * Every commerce pattern in the theme shipped in exactly that state, because
 * four Woo product blocks save a `<div class="is-loading">` placeholder unless
 * an attribute tells them which context they render in, and another seven save
 * a wrapper element unconditionally. Neither is visible from the pattern file,
 * and PHP cannot run the JS `save` — so this file locks down the two shapes
 * that were actually wrong rather than trying to validate blocks properly.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Asserts hand-written Woo block markup matches what the blocks save.
 */
class Test_Woo_Block_Markup extends WP_UnitTestCase {

	/**
	 * Blocks whose save output is a loading placeholder until they are told
	 * which context they render in.
	 *
	 * @var array<int, string>
	 */
	private const CONTEXT_BLOCKS = array(
		'product-image',
		'product-price',
		'product-rating',
		'product-summary',
	);

	/**
	 * Blocks that always save a wrapper element, so they can never be written
	 * self-closing.
	 *
	 * @var array<int, string>
	 */
	private const WRAPPED_BLOCKS = array(
		'product-filter-active',
		'product-filter-price',
		'product-filter-price-slider',
		'product-filter-rating',
		'product-filter-status',
		'add-to-cart-with-options-grouped-product-selector',
		'add-to-cart-with-options-variation-selector',
	);

	/**
	 * Every pattern, template and template part, keyed by path.
	 *
	 * @return array<string, string>
	 */
	private function markup_files(): array {
		$paths = array_merge(
			(array) glob( SUITEMART_DIR . '/patterns/*.php' ),
			(array) glob( SUITEMART_DIR . '/templates/*.html' ),
			(array) glob( SUITEMART_DIR . '/parts/*.html' )
		);

		$files = array();

		foreach ( $paths as $path ) {
			$files[ basename( (string) $path ) ] = (string) file_get_contents( (string) $path );
		}

		return $files;
	}

	/**
	 * The four context-sensitive blocks must declare their context.
	 *
	 * Without the flag the block saves `<div class="is-loading"></div>` and the
	 * self-closing form the patterns use has nothing to match it.
	 */
	public function test_context_blocks_declare_their_context(): void {
		$offenders = array();

		foreach ( $this->markup_files() as $name => $markup ) {
			foreach ( self::CONTEXT_BLOCKS as $block ) {
				$pattern = '#<!-- wp:woocommerce/' . preg_quote( $block, '#' ) . '(?![a-z0-9-])([^>]*?)/?-->#';

				if ( ! preg_match_all( $pattern, $markup, $matches ) ) {
					continue;
				}

				foreach ( $matches[1] as $attributes ) {
					if ( false === strpos( $attributes, 'isDescendentOf' ) ) {
						$offenders[] = $name . ' → ' . $block;
					}
				}
			}
		}

		$this->assertSame(
			array(),
			array_values( array_unique( $offenders ) ),
			'These blocks save a loading placeholder until they are told where they render. '
				. 'Add "isDescendentOfQueryLoop":true inside a product-template, or '
				. '"isDescendentOfSingleProductTemplate":true on the single product template.'
		);
	}

	/**
	 * Every Product Collection query must announce itself as one.
	 *
	 * Woo gates its whole front-end query builder on
	 * `$context['query']['isProductCollectionBlock']`, and a `query` written by
	 * hand does not get it — nothing merges Woo's client-side defaults into
	 * serialized markup. Without the flag every filter in the query is dropped
	 * silently and WP_Query answers with the newest products instead: the
	 * "Featured products" grid listed four products that were not featured, and
	 * "Best sellers" listed four that had never sold. Only the front end was
	 * wrong, because the editor preview passes the flag on its own REST request,
	 * which is what made it survive review.
	 */
	public function test_product_collections_declare_themselves(): void {
		$offenders = array();

		foreach ( $this->markup_files() as $name => $markup ) {
			preg_match_all(
				'#<!-- wp:woocommerce/product-collection (\{.*?\}) -->#',
				$markup,
				$matches
			);

			foreach ( $matches[1] as $attributes ) {
				if ( false === strpos( $attributes, '"isProductCollectionBlock":true' ) ) {
					$offenders[] = $name;
				}
			}
		}

		$this->assertSame(
			array(),
			array_values( array_unique( $offenders ) ),
			'Add "isProductCollectionBlock":true to the query, or WooCommerce ignores every '
				. 'filter in it on the front end and returns the newest products instead.'
		);
	}

	/**
	 * Blocks that save a wrapper element must be written open/close.
	 */
	public function test_wrapper_blocks_are_never_self_closing(): void {
		$offenders = array();

		foreach ( $this->markup_files() as $name => $markup ) {
			foreach ( self::WRAPPED_BLOCKS as $block ) {
				$pattern = '#<!-- wp:woocommerce/' . preg_quote( $block, '#' ) . '(?![a-z0-9-])[^>]*?/-->#';

				if ( preg_match( $pattern, $markup ) ) {
					$offenders[] = $name . ' → ' . $block;
				}
			}
		}

		$this->assertSame(
			array(),
			$offenders,
			'These blocks always save a wrapper element, so the self-closing form can never validate. '
				. 'Write them as an open/close pair around the element the block saves.'
		);
	}

	/**
	 * The three wrappers that were hand-written to the wrong shape.
	 *
	 * `add-to-cart-with-options` saves nothing at all, `product-meta` saves a
	 * bare div with no inline style, and `product-filters` saves both its block
	 * class and its `wc-` alias. Each shipped wrong in more than one file.
	 */
	public function test_hand_written_wrappers_match_their_save_output(): void {
		$wrong = array(
			'<div class="wp-block-woocommerce-add-to-cart-with-options"'
				=> 'add-to-cart-with-options saves nothing — its inner blocks stand alone, with no wrapper.',
			'<div class="wp-block-woocommerce-product-meta" style='
				=> 'product-meta saves a bare div; the spacing belongs in the block attributes.',
			'<div class="wp-block-woocommerce-product-filters">'
				=> 'product-filters also saves the wc-block-product-filters class.',
		);

		foreach ( $this->markup_files() as $name => $markup ) {
			foreach ( $wrong as $needle => $message ) {
				$this->assertStringNotContainsString( $needle, $markup, $name . ': ' . $message );
			}
		}
	}
}
