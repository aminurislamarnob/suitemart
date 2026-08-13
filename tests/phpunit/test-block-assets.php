<?php
/**
 * Block asset tests.
 *
 * A `block.json` names its assets by filename — `"style": "file:./style-index.css"`
 * — and `register_block_type()` believes it. If the build never emitted that
 * file, nothing complains: the handle is registered against a URL that 404s,
 * WordPress prints no stylesheet link, and the block renders with whatever the
 * user agent gives it. On a white page an unstyled `<button>` looks close
 * enough to a designed one to survive review.
 *
 * That is what happened. Three blocks — quick view, the product gallery and
 * frequently-bought-together — shipped with a `style` handle and no stylesheet,
 * because `@wordpress/scripts` only compiles a Sass file that something
 * imports, and their `index.js` imported nothing. Every command in AGENTS §6
 * passed the whole time. It surfaced when a dark style variation was applied
 * and the quick view button stayed light grey with black text.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Asserts every asset a block declares was actually built.
 */
class Test_Block_Assets extends WP_UnitTestCase {

	/**
	 * Every built block, by name.
	 *
	 * @return array<string, array{0: string}>
	 */
	public function blocks(): array {
		$sets = array();

		foreach ( (array) glob( get_template_directory() . '/build/*/block.json' ) as $file ) {
			$sets[ basename( dirname( $file ) ) ] = array( $file );
		}

		return $sets;
	}

	/**
	 * Every `file:./…` reference in a `block.json` resolves to a real file.
	 *
	 * @dataProvider blocks
	 *
	 * @param string $file Absolute path to a built `block.json`.
	 */
	public function test_declared_assets_exist( string $file ): void {
		$metadata = json_decode( (string) file_get_contents( $file ), true );
		$dir      = dirname( $file );
		$block    = basename( $dir );

		$fields = array(
			'editorScript',
			'script',
			'viewScript',
			'viewScriptModule',
			'editorStyle',
			'style',
			'viewStyle',
			'render',
		);

		$checked = 0;

		foreach ( $fields as $field ) {
			foreach ( (array) ( $metadata[ $field ] ?? array() ) as $value ) {
				if ( ! is_string( $value ) || ! str_starts_with( $value, 'file:' ) ) {
					continue;
				}

				++$checked;

				$this->assertFileExists(
					$dir . '/' . ltrim( substr( $value, strlen( 'file:' ) ), './' ),
					sprintf( '%s declares %s: %s, which the build never produced.', $block, $field, $value )
				);
			}
		}

		$this->assertGreaterThan( 0, $checked, $block . ' declares no assets at all, which cannot be right.' );
	}

	/**
	 * A block with a stylesheet in `src/` declares it, and vice versa.
	 *
	 * The check above catches a declaration with no file behind it. This one
	 * catches the mirror image — a `style.scss` sitting in `src/` that no
	 * `block.json` ever asks for, which is just as invisible.
	 *
	 * @dataProvider blocks
	 *
	 * @param string $file Absolute path to a built `block.json`.
	 */
	public function test_source_stylesheets_are_declared( string $file ): void {
		$metadata = json_decode( (string) file_get_contents( $file ), true );
		$block    = basename( dirname( $file ) );
		$source   = get_template_directory() . '/src/' . $block;

		$pairs = array(
			'style.scss'  => 'style',
			'editor.scss' => 'editorStyle',
		);

		$present  = array();
		$declared = array();

		foreach ( $pairs as $sheet => $field ) {
			if ( file_exists( $source . '/' . $sheet ) ) {
				$present[] = $field;
			}

			if ( isset( $metadata[ $field ] ) ) {
				$declared[] = $field;
			}
		}

		$this->assertSame(
			$present,
			$declared,
			sprintf(
				'%s: the stylesheets in src/ and the ones block.json declares must be the same set. In src/: %s. Declared: %s.',
				$block,
				$present ? implode( ', ', $present ) : 'none',
				$declared ? implode( ', ', $declared ) : 'none'
			)
		);
	}
}
