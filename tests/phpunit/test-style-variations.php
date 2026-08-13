<?php
/**
 * Style variation tests.
 *
 * Decision 13 buys multipurpose breadth with fifteen style variations, and
 * decision 6 makes that cheap: a variation replaces the value behind a palette
 * slug and nothing else, so every block follows without knowing it happened.
 * Two things can go wrong with that arrangement, and neither is visible to a
 * linter or to a rendering test.
 *
 * The first is a variation that renames or drops a slug. Every block stylesheet
 * names its colours through `var( --wp--preset--color--<slug> )`, so a missing
 * slug is not a fallback — the declaration is dropped and the element renders
 * unstyled, in exactly one variation, which nobody has open.
 *
 * The second is contrast. P3's acceptance criterion is that every variation
 * passes WCAG AA, and axe can only ever measure the one variation that happens
 * to be active. The pairs below are the ones the theme's own stylesheets
 * actually put together — read off `src/**\/*.scss` — so checking the numbers
 * here covers all fifteen at once, before a page is ever rendered.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Asserts every variation carries the full palette and passes AA on it.
 */
class Test_Style_Variations extends WP_UnitTestCase {

	/**
	 * Every slug a block stylesheet is allowed to name.
	 *
	 * @var array<int, string>
	 */
	private const SLUGS = array(
		'base',
		'contrast',
		'primary',
		'secondary',
		'accent',
		'neutral-100',
		'neutral-200',
		'neutral-300',
		'neutral-400',
		'neutral-500',
		'neutral-600',
		'neutral-700',
		'neutral-800',
		'neutral-900',
		'success',
		'warning',
		'error',
	);

	/**
	 * Foreground/background pairs the theme renders text in, at AA's 4.5:1.
	 *
	 * Nothing here is hypothetical: each pair is a colour a stylesheet sets on
	 * text over a surface another stylesheet sets behind it. Body copy and
	 * captions over the page and over a card; links over both; prices and
	 * stock warnings; and the base-coloured label a button paints on primary,
	 * on its secondary hover, and on accent.
	 *
	 * @var array<int, array{0: string, 1: string}>
	 */
	private const TEXT_PAIRS = array(
		array( 'contrast', 'base' ),
		array( 'contrast', 'neutral-100' ),
		array( 'contrast', 'neutral-200' ),
		array( 'neutral-600', 'base' ),
		array( 'neutral-600', 'neutral-100' ),
		array( 'neutral-700', 'base' ),
		array( 'neutral-700', 'neutral-100' ),
		array( 'neutral-700', 'neutral-200' ),
		array( 'primary', 'base' ),
		array( 'primary', 'neutral-100' ),
		array( 'error', 'base' ),
		array( 'error', 'neutral-100' ),
		array( 'warning', 'base' ),
		array( 'success', 'base' ),
		array( 'base', 'accent' ),
		array( 'base', 'contrast' ),
		array( 'base', 'secondary' ),
		array( 'base', 'neutral-800' ),
	);

	/**
	 * Pairs that carry no text but do identify a control, at 1.4.11's 3:1.
	 *
	 * Icon-only buttons — the size guide's close, quick view's — and the
	 * carousel pagination bullets. A bullet is a control, so the fact that it
	 * is a 8px dot does not exempt it.
	 *
	 * @var array<int, array{0: string, 1: string}>
	 */
	private const UI_PAIRS = array(
		array( 'neutral-500', 'base' ),
		array( 'neutral-500', 'neutral-100' ),
	);

	/**
	 * Reads a palette out of a theme.json-shaped file, keyed by slug.
	 *
	 * @param string $file Absolute path.
	 * @return array<string, string> Slug to hex.
	 */
	private function palette( string $file ): array {
		$json    = json_decode( (string) file_get_contents( $file ), true );
		$palette = array();

		foreach ( $json['settings']['color']['palette'] ?? array() as $entry ) {
			$palette[ (string) $entry['slug'] ] = (string) $entry['color'];
		}

		return $palette;
	}

	/**
	 * Every palette the theme ships, by the name a shop owner sees.
	 *
	 * @return array<string, array{0: string, 1: array<string, string>}>
	 */
	public function palettes(): array {
		$files = array_merge(
			array( get_template_directory() . '/theme.json' ),
			(array) glob( get_template_directory() . '/styles/*.json' )
		);

		$sets = array();

		foreach ( $files as $file ) {
			$sets[ basename( $file, '.json' ) ] = array( $file, $this->palette( $file ) );
		}

		return $sets;
	}

	/**
	 * WCAG relative luminance of an `#rrggbb` colour.
	 *
	 * @param string $hex Colour.
	 * @return float Luminance, 0–1.
	 */
	private function luminance( string $hex ): float {
		list( $r, $g, $b ) = array_map( 'hexdec', str_split( ltrim( $hex, '#' ), 2 ) );

		$channel = static function ( float $value ): float {
			$value /= 255;

			return $value <= 0.03928 ? $value / 12.92 : pow( ( $value + 0.055 ) / 1.055, 2.4 );
		};

		return 0.2126 * $channel( (float) $r ) + 0.7152 * $channel( (float) $g ) + 0.0722 * $channel( (float) $b );
	}

	/**
	 * WCAG contrast ratio. Symmetric, so a pair only needs checking once.
	 *
	 * @param string $a First colour.
	 * @param string $b Second colour.
	 * @return float Ratio, 1–21.
	 */
	private function contrast( string $a, string $b ): float {
		$la = $this->luminance( $a );
		$lb = $this->luminance( $b );

		return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
	}

	/**
	 * Fifteen variations exist, plus the defaults they override.
	 */
	public function test_the_theme_ships_fifteen_variations(): void {
		$this->assertCount( 15, (array) glob( get_template_directory() . '/styles/*.json' ) );
	}

	/**
	 * Every palette declares every slug, spelled the way blocks spell it.
	 *
	 * @dataProvider palettes
	 *
	 * @param string                $file    Path, for the failure message.
	 * @param array<string, string> $palette Slug to hex.
	 */
	public function test_the_palette_is_complete( string $file, array $palette ): void {
		$this->assertSame(
			self::SLUGS,
			array_keys( $palette ),
			basename( $file ) . ' must declare every slug, in order, and invent none.'
		);

		foreach ( $palette as $slug => $color ) {
			$this->assertMatchesRegularExpression(
				'/^#[0-9a-f]{6}$/',
				$color,
				sprintf( '%s: %s must be a six-digit lowercase hex.', basename( $file ), $slug )
			);
		}
	}

	/**
	 * Text passes AA and controls pass 1.4.11, in every variation.
	 *
	 * @dataProvider palettes
	 *
	 * @param string                $file    Path, for the failure message.
	 * @param array<string, string> $palette Slug to hex.
	 */
	public function test_the_palette_passes_wcag_aa( string $file, array $palette ): void {
		$checks = array(
			array( self::TEXT_PAIRS, 4.5, 'text' ),
			array( self::UI_PAIRS, 3.0, 'control' ),
		);

		foreach ( $checks as list( $pairs, $minimum, $kind ) ) {
			foreach ( $pairs as list( $foreground, $background ) ) {
				$ratio = $this->contrast( $palette[ $foreground ], $palette[ $background ] );

				$this->assertGreaterThanOrEqual(
					$minimum,
					$ratio,
					sprintf(
						'%s: %s (%s) on %s (%s) is %.2f:1, below the %.1f:1 a %s needs.',
						basename( $file ),
						$foreground,
						$palette[ $foreground ],
						$background,
						$palette[ $background ],
						$ratio,
						$minimum,
						$kind
					)
				);
			}
		}
	}

	/**
	 * A variation restyles; it never redefines the vocabulary.
	 *
	 * Slugs are checked above. This covers the rest of the settings: a
	 * variation that added a font size, renamed a spacing step or introduced a
	 * custom property would leave every block that uses it broken under the
	 * default and under the other fourteen.
	 *
	 * @dataProvider variation_files
	 *
	 * @param string $file Absolute path to a variation.
	 */
	public function test_a_variation_only_overrides_what_it_may( string $file ): void {
		$json     = json_decode( (string) file_get_contents( $file ), true );
		$settings = $json['settings'] ?? array();

		$this->assertSame(
			array( 'color', 'custom' ),
			array_keys( $settings ),
			basename( $file ) . ' may set colour and custom values, and nothing else.'
		);

		$this->assertSame(
			array( 'palette' ),
			array_keys( $settings['color'] ),
			basename( $file ) . ' may replace the palette, not the gradients or the switches.'
		);

		$this->assertSame(
			array( 'color', 'radius' ),
			array_keys( $settings['custom'] ),
			basename( $file ) . ' may restate the scrim, overlay and radii; transitions and z-indexes are structural.'
		);

		$this->assertSame(
			array( 'sm', 'md', 'lg', 'pill' ),
			array_keys( $settings['custom']['radius'] ),
			basename( $file ) . ' must declare every radius step.'
		);

		$this->assertNotEmpty( $json['title'] ?? '', basename( $file ) . ' needs a title to appear under.' );
		$this->assertNotEmpty( $json['description'] ?? '', basename( $file ) . ' needs a description.' );
	}

	/**
	 * Every file under `styles/`.
	 *
	 * @return array<string, array{0: string}>
	 */
	public function variation_files(): array {
		$sets = array();

		foreach ( (array) glob( get_template_directory() . '/styles/*.json' ) as $file ) {
			$sets[ basename( $file, '.json' ) ] = array( $file );
		}

		return $sets;
	}
}
