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

		foreach ( $settings['color']['palette']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--color--' . $item['slug'];
		}

		foreach ( $settings['color']['gradients']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--gradient--' . $item['slug'];
		}

		foreach ( $settings['typography']['fontSizes']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--font-size--' . $item['slug'];
		}

		foreach ( $settings['typography']['fontFamilies']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--font-family--' . $item['slug'];
		}

		foreach ( $settings['shadow']['presets']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--shadow--' . $item['slug'];
		}

		foreach ( $settings['spacing']['spacingSizes']['theme'] ?? array() as $item ) {
			$tokens[] = '--wp--preset--spacing--' . $item['slug'];
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
}
