<?php
/**
 * Map block tests.
 *
 * The Map block turns a block attribute into an `iframe src`, which is a route
 * to framing arbitrary content on the site's own pages — a fake login form, a
 * payment prompt — and anyone who can edit a post can set that attribute. The
 * host allowlist is therefore a security boundary, not a convenience, and these
 * tests exist to keep it one.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Verifies only Google Maps URLs can ever be framed.
 */
class Test_Map extends WP_UnitTestCase {

	/**
	 * URLs that must be refused.
	 *
	 * @return array<string, array<int, string>>
	 */
	public function data_rejected_urls(): array {
		return array(
			'another origin'       => array( 'https://evil.test/maps/embed?pb=1' ),
			'lookalike host'       => array( 'https://google.com.evil.test/maps/embed?pb=1' ),
			'subdomain lookalike'  => array( 'https://notgoogle.com/maps/embed' ),
			'google but not a map' => array( 'https://www.google.com/search?q=test' ),
			'plain http'           => array( 'http://www.google.com/maps/embed?pb=1' ),
			'javascript scheme'    => array( 'javascript:alert(1)' ),
			'data scheme'          => array( 'data:text/html,<script>alert(1)</script>' ),
			'protocol relative'    => array( '//www.google.com/maps/embed?pb=1' ),
			'empty'                => array( '' ),
			'not a url'            => array( 'maps.google.com/maps/embed' ),
			'userinfo smuggling'   => array( 'https://www.google.com@evil.test/maps/embed' ),
		);
	}

	/**
	 * Anything that is not a Google Maps embed must be refused.
	 *
	 * @dataProvider data_rejected_urls
	 *
	 * @param string $url Candidate URL.
	 */
	public function test_rejects_untrusted_urls( string $url ): void {
		$this->assertSame(
			'',
			suitemart_map_validate_embed_url( $url ),
			sprintf( '%s was accepted as a map URL.', $url )
		);
	}

	/**
	 * URLs that must be accepted.
	 *
	 * @return array<string, array<int, string>>
	 */
	public function data_accepted_urls(): array {
		return array(
			'share embed'  => array( 'https://www.google.com/maps/embed?pb=!1m18!1m12' ),
			'embed api'    => array( 'https://www.google.com/maps/embed/v1/place?key=abc&q=London' ),
			'output=embed' => array( 'https://maps.google.com/maps?q=London&output=embed' ),
		);
	}

	/**
	 * Genuine Google Maps embeds must survive validation unchanged.
	 *
	 * @dataProvider data_accepted_urls
	 *
	 * @param string $url Candidate URL.
	 */
	public function test_accepts_google_embeds( string $url ): void {
		$this->assertSame( $url, suitemart_map_validate_embed_url( $url ) );
	}

	/**
	 * A rejected URL must produce no iframe at all.
	 */
	public function test_rejected_url_renders_nothing(): void {
		$html = render_block(
			array(
				'blockName'    => 'suitemart/map',
				'attrs'        => array(
					'source'   => 'embed',
					'embedUrl' => 'https://evil.test/maps/embed',
				),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		$this->assertStringNotContainsString( 'evil.test', $html );
		$this->assertStringNotContainsString( '<iframe', $html );
	}

	/**
	 * An address builds the documented Embed API URL when a key is supplied.
	 */
	public function test_address_with_key_uses_the_embed_api(): void {
		$url = suitemart_map_url(
			array(
				'source'  => 'address',
				'address' => 'Brighton, UK',
				'apiKey'  => 'test-key',
				'zoom'    => 12,
			)
		);

		$this->assertStringStartsWith( 'https://www.google.com/maps/embed/v1/place', $url );
		$this->assertStringContainsString( 'key=test-key', $url );
		$this->assertStringContainsString( 'zoom=12', $url );
		$this->assertStringContainsString( 'Brighton', $url );
	}

	/**
	 * Without a key it falls back to the keyless address form.
	 */
	public function test_address_without_key_falls_back(): void {
		$url = suitemart_map_url(
			array(
				'source'  => 'address',
				'address' => 'Brighton, UK',
			)
		);

		$this->assertStringContainsString( 'output=embed', $url );
		$this->assertStringContainsString( 'maps.google.com', $url );
	}

	/**
	 * An address is encoded before it reaches the query string.
	 */
	public function test_address_is_encoded(): void {
		$url = suitemart_map_url(
			array(
				'source'  => 'address',
				'address' => 'Bar & Grill #2, Brighton',
			)
		);

		$this->assertStringNotContainsString( '#2', $url, 'An unencoded fragment reached the URL.' );
		$this->assertStringNotContainsString( ' ', $url, 'An unencoded space reached the URL.' );
	}

	/**
	 * An empty address produces nothing rather than a map of nowhere.
	 */
	public function test_empty_address_renders_nothing(): void {
		$this->assertSame(
			'',
			suitemart_map_url(
				array(
					'source'  => 'address',
					'address' => '   ',
				)
			)
		);
	}

	/**
	 * The iframe must carry a title, since that is all a screen reader gets.
	 */
	public function test_iframe_is_titled(): void {
		$html = render_block(
			array(
				'blockName'    => 'suitemart/map',
				'attrs'        => array(
					'source'  => 'address',
					'address' => 'Brighton',
					'title'   => 'Map of the Brighton shop',
				),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		$this->assertStringContainsString( 'title="Map of the Brighton shop"', $html );
	}

	/**
	 * With consent required, nothing is requested from Google on page load.
	 */
	public function test_consent_mode_withholds_the_url(): void {
		$html = render_block(
			array(
				'blockName'    => 'suitemart/map',
				'attrs'        => array(
					'source'         => 'address',
					'address'        => 'Brighton',
					'requireConsent' => true,
				),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		$this->assertStringContainsString( 'sm-map__consent-button', $html );

		// The frame must ship empty: a populated src would have already made
		// the request the consent screen exists to prevent.
		$this->assertMatchesRegularExpression(
			'/<iframe[^>]*\ssrc=""/',
			$html,
			'The iframe shipped with a src, so Google was contacted before consent.'
		);
	}

	/**
	 * Without consent required the map works with no JavaScript at all.
	 */
	public function test_default_mode_renders_a_complete_iframe(): void {
		$html = render_block(
			array(
				'blockName'    => 'suitemart/map',
				'attrs'        => array(
					'source'  => 'address',
					'address' => 'Brighton',
				),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		$this->assertMatchesRegularExpression(
			'/<iframe[^>]*\ssrc="https:\/\/maps\.google\.com[^"]+"/',
			$html,
			'The iframe has no src, so the map needs JavaScript to appear at all.'
		);
	}
}
