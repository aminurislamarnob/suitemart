<?php
/**
 * Banner block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/banner.
 */
class Test_Banner extends WP_UnitTestCase {

	/**
	 * Renders the block through do_blocks().
	 *
	 * @param array<string, mixed> $attrs   Block attributes.
	 * @param string               $content Inner blocks markup.
	 * @return string Rendered markup.
	 */
	private function render( array $attrs = array(), string $content = '<!-- wp:paragraph --><p>Shop now</p><!-- /wp:paragraph -->' ): string {
		return do_blocks(
			sprintf(
				'<!-- wp:suitemart/banner %s -->%s<!-- /wp:suitemart/banner -->',
				wp_json_encode( (object) $attrs ),
				$content
			)
		);
	}

	/**
	 * With neither media nor content there is nothing to draw.
	 */
	public function test_an_empty_banner_renders_nothing(): void {
		$this->assertSame( '', trim( $this->render( array(), '' ) ) );
	}

	/**
	 * A banner with no image gets a ground its text can be read against.
	 *
	 * The content of a banner is written for a photograph: light text over a
	 * scrim. With no image the scrim covers nothing, leaving a pale grey — and
	 * white text on it, both in the inserter preview and on the page, for as
	 * long as it takes someone to notice and choose a picture.
	 */
	public function test_a_banner_without_media_marks_itself(): void {
		$this->assertStringContainsString( 'sm-banner--no-media', $this->render() );
	}

	/**
	 * With an image there is no placeholder, because the image is the ground.
	 */
	public function test_a_banner_with_media_has_no_placeholder(): void {
		$html = $this->render( array( 'mediaUrl' => 'https://example.com/banner.jpg' ) );

		$this->assertStringNotContainsString( 'sm-banner--no-media', $html );
		$this->assertStringContainsString( 'sm-banner__image', $html );
	}

	/**
	 * Layout attributes reach the markup, and nonsense does not.
	 */
	public function test_layout_is_validated(): void {
		$this->assertStringContainsString(
			'--sm-banner-ratio:16/9',
			$this->render( array( 'aspectRatio' => '16/9' ) )
		);
		$this->assertStringContainsString(
			'--sm-banner-ratio:3/2',
			$this->render( array( 'aspectRatio' => 'url(javascript:alert(1))' ) )
		);
		$this->assertStringContainsString(
			'sm-banner--center-left',
			$this->render( array( 'contentPosition' => 'center-left' ) )
		);
		$this->assertStringContainsString(
			'sm-banner--bottom-left',
			$this->render( array( 'contentPosition' => 'nowhere' ) )
		);
	}

	/**
	 * A linked banner stretches one link over itself and names it.
	 */
	public function test_a_linked_banner_is_reachable(): void {
		$html = $this->render(
			array(
				'url'      => '/shop',
				'mediaUrl' => 'https://example.com/banner.jpg',
				'mediaAlt' => 'Womenswear',
			)
		);

		$this->assertStringContainsString( 'sm-banner__link', $html );
		$this->assertStringContainsString( 'Womenswear', $html );

		// The image must not repeat the link's own name to a screen reader.
		$this->assertStringContainsString( 'alt=""', $html );
	}
}
