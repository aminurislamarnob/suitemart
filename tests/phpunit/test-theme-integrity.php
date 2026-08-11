<?php
/**
 * Theme integrity tests.
 *
 * These catch the structural mistakes that are invisible in review but break the
 * Site Editor: a template referencing a part that does not exist, a theme.json
 * declaration with no matching file, or a style variation that renames a palette
 * slug the blocks depend on.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Verifies templates, parts, theme.json and style variations agree with each other.
 */
class Test_Theme_Integrity extends WP_UnitTestCase {

	/**
	 * Reads and decodes the theme's theme.json.
	 *
	 * @return array<string, mixed>
	 */
	private function theme_json(): array {
		$raw = file_get_contents( SUITEMART_DIR . '/theme.json' );
		$this->assertIsString( $raw, 'theme.json is unreadable.' );

		$data = json_decode( $raw, true );
		$this->assertIsArray( $data, 'theme.json is not valid JSON: ' . json_last_error_msg() );

		return $data;
	}

	/**
	 * Every template part declared in theme.json must exist on disk.
	 */
	public function test_declared_template_parts_exist(): void {
		foreach ( $this->theme_json()['templateParts'] ?? array() as $part ) {
			$this->assertFileExists(
				SUITEMART_DIR . '/parts/' . $part['name'] . '.html',
				sprintf( 'theme.json declares the "%s" part but no file exists.', $part['name'] )
			);
		}
	}

	/**
	 * Every custom template declared in theme.json must exist on disk.
	 */
	public function test_declared_custom_templates_exist(): void {
		foreach ( $this->theme_json()['customTemplates'] ?? array() as $template ) {
			$this->assertFileExists(
				SUITEMART_DIR . '/templates/' . $template['name'] . '.html',
				sprintf( 'theme.json declares the "%s" template but no file exists.', $template['name'] )
			);
		}
	}

	/**
	 * Every template part a template references must exist.
	 *
	 * A missing part renders as an empty region with no error, so this is the
	 * kind of break that ships unnoticed.
	 */
	public function test_referenced_template_parts_exist(): void {
		$files = glob( SUITEMART_DIR . '/templates/*.html' );
		$files = is_array( $files ) ? $files : array();
		$this->assertNotEmpty( $files, 'The theme has no templates.' );

		foreach ( $files as $file ) {
			$content = (string) file_get_contents( $file );

			preg_match_all( '/wp:template-part\s*({[^}]*})/', $content, $matches );

			foreach ( $matches[1] as $json ) {
				$attrs = json_decode( $json, true );

				if ( ! is_array( $attrs ) || ! isset( $attrs['slug'] ) ) {
					continue;
				}

				$this->assertFileExists(
					SUITEMART_DIR . '/parts/' . $attrs['slug'] . '.html',
					sprintf(
						'%s references the "%s" part, which does not exist.',
						basename( $file ),
						$attrs['slug']
					)
				);
			}
		}
	}

	/**
	 * Templates and parts must contain no stray HTML outside block comments.
	 *
	 * Stray markup survives parsing but cannot be edited in the Site Editor, and
	 * it silently disappears the first time a user saves the template.
	 */
	public function test_templates_contain_only_blocks(): void {
		// GLOB_BRACE is not defined on musl libc, which is what the Alpine-based
		// test container uses. Two globs are portable; brace expansion is not.
		$templates = glob( SUITEMART_DIR . '/templates/*.html' );
		$parts     = glob( SUITEMART_DIR . '/parts/*.html' );

		$files = array_merge(
			is_array( $templates ) ? $templates : array(),
			is_array( $parts ) ? $parts : array()
		);

		$this->assertNotEmpty( $files, 'The theme has no templates or parts.' );

		foreach ( $files as $file ) {
			$this->assertSame(
				array(),
				$this->find_stray_html( parse_blocks( (string) file_get_contents( $file ) ) ),
				sprintf( '%s contains HTML outside of a block.', basename( $file ) )
			);
		}
	}

	/**
	 * Collects non-empty content that does not belong to any block.
	 *
	 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
	 * @return array<int, string>
	 */
	private function find_stray_html( array $blocks ): array {
		$stray = array();

		foreach ( $blocks as $block ) {
			if ( null === $block['blockName'] && '' !== trim( (string) $block['innerHTML'] ) ) {
				$stray[] = trim( (string) $block['innerHTML'] );
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$stray = array_merge( $stray, $this->find_stray_html( $block['innerBlocks'] ) );
			}
		}

		return $stray;
	}

	/**
	 * Style variations may change palette colours but never palette slugs.
	 *
	 * Blocks and patterns reference colours by slug. A variation that renames one
	 * does not fail loudly — it silently drops the colour everywhere it was used,
	 * which is why this is asserted rather than trusted.
	 */
	public function test_style_variations_preserve_palette_slugs(): void {
		$base_palette = $this->theme_json()['settings']['color']['palette'] ?? array();
		$base_slugs   = wp_list_pluck( $base_palette, 'slug' );
		sort( $base_slugs );

		$this->assertNotEmpty( $base_slugs, 'theme.json defines no colour palette.' );

		$variations = glob( SUITEMART_DIR . '/styles/*.json' );

		foreach ( is_array( $variations ) ? $variations : array() as $file ) {
			$variation = json_decode( (string) file_get_contents( $file ), true );

			$this->assertIsArray(
				$variation,
				sprintf( '%s is not valid JSON.', basename( $file ) )
			);

			$palette = $variation['settings']['color']['palette'] ?? null;

			if ( null === $palette ) {
				continue;
			}

			$slugs = wp_list_pluck( $palette, 'slug' );
			sort( $slugs );

			$this->assertSame(
				$base_slugs,
				$slugs,
				sprintf( '%s changes the palette slugs; variations may only change colour values.', basename( $file ) )
			);
		}
	}

	/**
	 * A template part must not repeat the landmark its reference already supplies.
	 *
	 * `wp:template-part {"tagName":"header"}` wraps the part in a `<header>`. If
	 * the part's own root group also sets `tagName`, the page ends up with two
	 * nested banner landmarks, which screen readers announce twice. Nothing about
	 * the rendered page looks wrong, so this is asserted rather than eyeballed.
	 */
	public function test_parts_do_not_duplicate_landmarks(): void {
		$parts = glob( SUITEMART_DIR . '/parts/*.html' );

		foreach ( is_array( $parts ) ? $parts : array() as $file ) {
			$blocks = parse_blocks( (string) file_get_contents( $file ) );
			$root   = null;

			foreach ( $blocks as $block ) {
				if ( null !== $block['blockName'] ) {
					$root = $block;
					break;
				}
			}

			if ( null === $root ) {
				continue;
			}

			$this->assertNotContains(
				$root['attrs']['tagName'] ?? 'div',
				array( 'header', 'footer', 'main', 'aside', 'nav' ),
				sprintf(
					'%s sets a landmark tagName on its root block; the template-part reference already provides one.',
					basename( $file )
				)
			);
		}
	}

	/**
	 * The portfolio post type and its taxonomy must be registered.
	 */
	public function test_portfolio_post_type_is_registered(): void {
		$this->assertTrue( post_type_exists( 'portfolio' ) );
		$this->assertTrue( taxonomy_exists( 'project-cat' ) );

		$post_type = get_post_type_object( 'portfolio' );
		$this->assertNotNull( $post_type );
		$this->assertTrue(
			$post_type->show_in_rest,
			'Portfolio must be REST-enabled so its content is editable in the block editor and exportable.'
		);
	}

	/**
	 * Every pattern file must declare the headers WordPress needs to register it.
	 */
	public function test_patterns_declare_required_headers(): void {
		$patterns = glob( SUITEMART_DIR . '/patterns/*.php' );

		foreach ( is_array( $patterns ) ? $patterns : array() as $file ) {
			$headers = get_file_data(
				$file,
				array(
					'title' => 'Title',
					'slug'  => 'Slug',
				)
			);

			$this->assertNotEmpty(
				$headers['title'],
				sprintf( '%s has no Title header and will not register.', basename( $file ) )
			);
			$this->assertNotEmpty(
				$headers['slug'],
				sprintf( '%s has no Slug header and will not register.', basename( $file ) )
			);
		}
	}
}
