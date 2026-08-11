<?php
/**
 * Icon sprite tests.
 *
 * The editor picks icons from a hard-coded list in `src/_shared/icons.js` while
 * the front end renders them from `assets/icons/sprite.svg`. Neither knows about
 * the other, so a symbol added to one and not the other produces an icon that is
 * offered in the editor and renders as nothing on the page. This asserts they
 * hold the same set.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Verifies the sprite and the editor's icon list agree.
 */
class Test_Icons extends WP_UnitTestCase {

	/**
	 * Icon ids defined in the sprite.
	 *
	 * @return array<int, string>
	 */
	private function sprite_names(): array {
		$svg = (string) file_get_contents( SUITEMART_DIR . '/assets/icons/sprite.svg' );

		preg_match_all( '/id="sm-icon-([a-z0-9-]+)"/', $svg, $matches );

		$names = $matches[1];
		sort( $names );

		return $names;
	}

	/**
	 * Icon names listed for the editor.
	 *
	 * @return array<int, string>
	 */
	private function listed_names(): array {
		$js = (string) file_get_contents( SUITEMART_DIR . '/src/_shared/icons.js' );

		// Only the ICON_NAMES array, so the JSDoc examples below it cannot match.
		preg_match( '/export const ICON_NAMES = \[(.*?)\];/s', $js, $block );

		$this->assertNotEmpty( $block, 'ICON_NAMES was not found in src/_shared/icons.js.' );

		preg_match_all( "/'([a-z0-9-]+)'/", $block[1], $matches );

		$names = $matches[1];
		sort( $names );

		return $names;
	}

	/**
	 * The sprite and the editor list must hold the same icons.
	 */
	public function test_sprite_matches_list(): void {
		$sprite = $this->sprite_names();
		$listed = $this->listed_names();

		$this->assertNotEmpty( $sprite, 'The icon sprite defines no symbols.' );

		$this->assertSame(
			array(),
			array_values( array_diff( $sprite, $listed ) ),
			'Icons exist in the sprite but are not offered in the editor. Add them to src/_shared/icons.js.'
		);

		$this->assertSame(
			array(),
			array_values( array_diff( $listed, $sprite ) ),
			'Icons are offered in the editor but missing from the sprite, so they would render as nothing.'
		);
	}

	/**
	 * Every icon the theme's own markup asks for must exist.
	 */
	public function test_referenced_icons_exist(): void {
		$sprite = $this->sprite_names();
		$files  = glob( SUITEMART_DIR . '/src/*/render.php' );

		foreach ( is_array( $files ) ? $files : array() as $file ) {
			preg_match_all(
				"/suitemart_get_icon\(\s*'([a-z0-9-]+)'/",
				(string) file_get_contents( $file ),
				$matches
			);

			foreach ( $matches[1] as $name ) {
				$this->assertContains(
					$name,
					$sprite,
					sprintf( '%s renders the "%s" icon, which is not in the sprite.', basename( dirname( $file ) ), $name )
				);
			}
		}
	}

	/**
	 * A decorative icon must be hidden from assistive technology.
	 */
	public function test_icons_without_a_label_are_hidden(): void {
		$decorative = suitemart_get_icon( 'cart' );

		$this->assertStringContainsString( 'aria-hidden="true"', $decorative );
		$this->assertStringContainsString( 'focusable="false"', $decorative );
		$this->assertStringNotContainsString( 'role="img"', $decorative );
	}

	/**
	 * A labelled icon must expose its name instead.
	 */
	public function test_labelled_icons_are_exposed(): void {
		$labelled = suitemart_get_icon( 'cart', array( 'label' => 'Basket' ) );

		$this->assertStringContainsString( 'role="img"', $labelled );
		$this->assertStringContainsString( 'aria-label="Basket"', $labelled );
		$this->assertStringNotContainsString( 'aria-hidden', $labelled );
	}

	/**
	 * Icon names are used in a selector and an id, so they must be sanitised.
	 */
	public function test_icon_names_are_sanitised(): void {
		$hostile = suitemart_get_icon( '"><script>alert(1)</script>' );

		$this->assertStringNotContainsString( '<script', $hostile );

		// The name reaches both a class and a fragment id, so it must survive as
		// nothing but safe characters. Asserting on the extracted values rather
		// than the whole string avoids matching ordinary markup such as `"><use`.
		preg_match( '/class="([^"]*)"/', $hostile, $class );
		preg_match( '/href="#([^"]*)"/', $hostile, $href );

		$this->assertMatchesRegularExpression( '/^[a-z0-9 -]*$/', $class[1] ?? '' );
		$this->assertMatchesRegularExpression( '/^sm-icon-[a-z0-9_-]*$/', $href[1] ?? '' );
	}
}
