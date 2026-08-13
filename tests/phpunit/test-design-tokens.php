<?php
/**
 * Design token tests.
 *
 * Decision 6 puts every colour, size and radius in `theme.json` and forbids a
 * CSS generation engine, so a block's stylesheet reaches those values through
 * `var( --wp--preset--* )` and `var( --wp--custom--* )`. Nothing in the build
 * checks that the name on the left of that `var()` exists: an invented token
 * resolves to nothing, the declaration is dropped, and the block renders
 * unstyled while every linter stays green. Worse, writing a fallback —
 * `var( --wp--preset--color--tertiary, #b45309 )` — hides the mistake
 * completely *and* stops a style variation from restyling the block, which is
 * the whole property decision 6 exists to protect.
 *
 * That shipped: six blocks reached `main` carrying nineteen references to
 * tokens that were never declared, including a product badge whose background
 * resolved to nothing and left white text on a white card.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Asserts every custom property a stylesheet references is one theme.json emits.
 */
class Test_Design_Tokens extends WP_UnitTestCase {

	/**
	 * Every `--wp--*` custom property `theme.json` emits.
	 *
	 * @return array<int, string>
	 */
	private function declared_tokens(): array {
		$settings = ( new WP_Theme_JSON_Resolver() )::get_theme_data()->get_settings();

		$tokens = array();

		/*
		 * The emitted property is the *kebab-cased* slug, not the slug: core
		 * splits a digit from the letter after it, so the size declared as
		 * `3xl` arrives as `--wp--preset--font-size--3-xl`. Spelling it the
		 * obvious way declares nothing, and did — every h1 and h2 in the theme
		 * rendered at body size for weeks because `theme.json` asked for
		 * `--3xl`, and so did the `has-3xl-font-size` class in eleven files.
		 * Use core's own function so the two can never disagree again.
		 */
		$name = static fn ( string $slug ): string => _wp_to_kebab_case( $slug );

		foreach ( $settings['color']['palette']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--color--' . $name( $item['slug'] );
		}

		foreach ( $settings['color']['gradients']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--gradient--' . $name( $item['slug'] );
		}

		foreach ( $settings['typography']['fontSizes']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--font-size--' . $name( $item['slug'] );
		}

		foreach ( $settings['typography']['fontFamilies']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--font-family--' . $name( $item['slug'] );
		}

		foreach ( $settings['shadow']['presets']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--shadow--' . $name( $item['slug'] );
		}

		foreach ( $settings['spacing']['spacingSizes']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--spacing--' . $name( $item['slug'] );
		}

		// The scale is declared by step count rather than by slug, and core
		// numbers the emitted properties 20, 30, 40 … from there.
		$steps = (int) ( $settings['spacing']['spacingScale']['steps'] ?? 0 );

		for ( $i = 0; $i < $steps; $i++ ) {
			$tokens[] = '--wp--preset--spacing--' . ( 20 + ( $i * 10 ) );
		}

		return array_merge(
			$tokens,
			$this->flatten_custom( $settings['custom'] ?? array(), '--wp--custom' )
		);
	}

	/**
	 * Walks `settings.custom`, which core emits as kebab-cased nested names.
	 *
	 * @param array<string, mixed> $values Nested custom settings.
	 * @param string               $prefix Property name built so far.
	 * @return array<int, string>
	 */
	private function flatten_custom( array $values, string $prefix ): array {
		$tokens = array();

		foreach ( $values as $key => $value ) {
			$name = strtolower( (string) preg_replace( '/(?<!^)[A-Z]/', '-$0', (string) $key ) );

			if ( is_array( $value ) && ! isset( $value[0] ) ) {
				$tokens = array_merge( $tokens, $this->flatten_custom( $value, $prefix . '--' . $name ) );
				continue;
			}

			$tokens[] = $prefix . '--' . $name;
		}

		return $tokens;
	}

	/**
	 * Every Sass file in `src/`.
	 *
	 * GLOB_BRACE is undefined on the musl libc the container uses, so the
	 * directories are globbed separately.
	 *
	 * @return array<int, string>
	 */
	private function stylesheets(): array {
		return array_merge(
			(array) glob( SUITEMART_DIR . '/src/*.scss' ),
			(array) glob( SUITEMART_DIR . '/src/*/*.scss' )
		);
	}

	/**
	 * No stylesheet may reference a token theme.json does not declare.
	 */
	public function test_every_referenced_token_is_declared(): void {
		$declared = array_flip( $this->declared_tokens() );
		$unknown  = array();

		foreach ( $this->stylesheets() as $path ) {
			$lines = (array) file( $path );

			foreach ( $lines as $number => $line ) {
				preg_match_all( '/--wp--(?:preset|custom)--[a-z0-9-]+/', (string) $line, $matches );

				foreach ( $matches[0] as $token ) {
					if ( isset( $declared[ $token ] ) ) {
						continue;
					}

					$unknown[] = sprintf(
						'%s:%d references %s',
						str_replace( SUITEMART_DIR . '/', '', $path ),
						$number + 1,
						$token
					);
				}
			}
		}

		$this->assertSame(
			array(),
			$unknown,
			"These stylesheets reference custom properties theme.json never emits, so the declarations are silently dropped:\n" . implode( "\n", $unknown )
		);
	}

	/**
	 * A `var()` fallback hides a wrong token name and defeats style variations.
	 */
	public function test_preset_references_carry_no_fallback_value(): void {
		$offenders = array();

		foreach ( $this->stylesheets() as $path ) {
			$lines = (array) file( $path );

			foreach ( $lines as $number => $line ) {
				preg_match_all(
					'/var\(\s*(--wp--(?:preset|custom)--[a-z0-9-]+)\s*,/',
					(string) $line,
					$matches
				);

				foreach ( $matches[1] as $token ) {
					$offenders[] = sprintf(
						'%s:%d gives %s a fallback',
						str_replace( SUITEMART_DIR . '/', '', $path ),
						$number + 1,
						$token
					);
				}
			}
		}

		$this->assertSame(
			array(),
			$offenders,
			"A fallback masks a misspelled token and stops style variations restyling the block:\n" . implode( "\n", $offenders )
		);
	}

	/**
	 * The `var()` references in theme.json itself must resolve as well.
	 *
	 * Nothing above looks at `theme.json`, and it is written in the same
	 * property names as the stylesheets are — so it can misspell one in
	 * exactly the same way, with the same silence. It did: `elements.h1` asked
	 * for `--wp--preset--font-size--3xl`, which is not a property that exists,
	 * and every h1 in the theme fell back to the root font size.
	 */
	public function test_theme_json_references_only_declared_tokens(): void {
		$declared = array_flip( $this->declared_tokens() );

		$files = array_merge(
			array( SUITEMART_DIR . '/theme.json' ),
			(array) glob( SUITEMART_DIR . '/styles/*.json' )
		);

		$unknown = array();

		foreach ( $files as $path ) {
			preg_match_all(
				'/--wp--(?:preset|custom)--[a-z0-9-]+/',
				(string) file_get_contents( $path ),
				$matches
			);

			foreach ( array_unique( $matches[0] ) as $token ) {
				if ( isset( $declared[ $token ] ) ) {
					continue;
				}

				$unknown[] = sprintf( '%s references %s', basename( $path ), $token );
			}
		}

		$this->assertSame(
			array(),
			$unknown,
			"These theme.json files reference custom properties nothing emits:\n" . implode( "\n", $unknown )
		);
	}

	/**
	 * Preset classes written by hand must name a preset that exists.
	 *
	 * Patterns, templates and parts are hand-written block markup, so their
	 * `has-…-font-size` and `has-…-color` classes are typed rather than
	 * generated — and a class core would never emit styles nothing. This is
	 * the other half of the `3xl` / `3-xl` bug: eleven files asked for
	 * `has-3xl-font-size` and got body-sized headings.
	 */
	public function test_preset_classes_name_real_presets(): void {
		$settings = ( new WP_Theme_JSON_Resolver() )::get_theme_data()->get_settings();

		$known = array();

		foreach ( $settings['typography']['fontSizes']['theme'] ?? array() as $item ) {
			$known[ 'has-' . _wp_to_kebab_case( $item['slug'] ) . '-font-size' ] = true;
		}

		foreach ( $settings['color']['palette']['theme'] ?? array() as $item ) {
			$known[ 'has-' . _wp_to_kebab_case( $item['slug'] ) . '-color' ]            = true;
			$known[ 'has-' . _wp_to_kebab_case( $item['slug'] ) . '-background-color' ] = true;
			$known[ 'has-' . _wp_to_kebab_case( $item['slug'] ) . '-border-color' ]     = true;
		}

		$files = array_merge(
			(array) glob( SUITEMART_DIR . '/patterns/*.php' ),
			(array) glob( SUITEMART_DIR . '/templates/*.html' ),
			(array) glob( SUITEMART_DIR . '/parts/*.html' )
		);

		$unknown = array();

		foreach ( $files as $path ) {
			preg_match_all(
				'/has-[a-z0-9-]+-(?:font-size|background-color|color)\b/',
				(string) file_get_contents( $path ),
				$matches
			);

			foreach ( array_unique( $matches[0] ) as $class ) {
				// Core's own markers, which announce that *some* value is set
				// rather than naming a preset.
				$markers = array(
					'has-text-color',
					'has-background-color',
					'has-link-color',
					'has-icon-color',
					'has-border-color',
					'has-custom-font-size',
				);

				if ( in_array( $class, $markers, true ) ) {
					continue;
				}

				if ( isset( $known[ $class ] ) ) {
					continue;
				}

				$unknown[] = sprintf( '%s uses %s', basename( $path ), $class );
			}
		}

		$this->assertSame(
			array(),
			$unknown,
			"These files use preset classes that match no preset, so they style nothing:\n" . implode( "\n", $unknown )
		);
	}
}
