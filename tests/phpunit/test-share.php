<?php
/**
 * Share Links block tests.
 *
 * The block's network list exists twice — in PHP, which builds the URLs, and in
 * JavaScript, which draws the editor's picker. A network added to one and not
 * the other is either unofferable or unrenderable, and neither shows up as an
 * error, so the two are asserted to match here.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Verifies share URLs are built and encoded correctly.
 */
class Test_Share extends WP_UnitTestCase {

	/**
	 * Network keys the editor offers.
	 *
	 * @return array<int, string>
	 */
	private function js_networks(): array {
		$js = (string) file_get_contents( SUITEMART_DIR . '/src/social-share/networks.js' );

		preg_match_all( "/\{\s*id:\s*'([a-z0-9-]+)'/", $js, $matches );

		$names = $matches[1];
		sort( $names );

		return $names;
	}

	/**
	 * The PHP and JavaScript network lists must hold the same keys.
	 */
	public function test_network_lists_match(): void {
		$php = array_keys( suitemart_share_networks() );
		sort( $php );

		$this->assertNotEmpty( $php, 'No share networks are defined in PHP.' );
		$this->assertSame(
			$php,
			$this->js_networks(),
			'suitemart_share_networks() and src/social-share/networks.js disagree.'
		);
	}

	/**
	 * Every network except the clipboard must produce a URL.
	 */
	public function test_every_network_builds_a_url(): void {
		foreach ( array_keys( suitemart_share_networks() ) as $network ) {
			$url = suitemart_share_url( $network, 'https://example.test/post/', 'Title' );

			if ( 'copy' === $network ) {
				// Copying is done in the browser and has no endpoint.
				$this->assertSame( '', $url );
				continue;
			}

			$this->assertNotSame( '', $url, sprintf( '%s produced no share URL.', $network ) );
			$this->assertMatchesRegularExpression(
				'#^(https://|mailto:)#',
				$url,
				sprintf( '%s produced a URL with an unexpected scheme.', $network )
			);
		}
	}

	/**
	 * Titles and URLs must be encoded before they reach a query string.
	 *
	 * A title containing `&` or `#` would otherwise end the parameter early and
	 * corrupt everything after it — including, on some networks, the URL being
	 * shared. `esc_url()` at the output stage does not catch this.
	 */
	public function test_parameters_are_encoded(): void {
		$url = suitemart_share_url(
			'x',
			'https://example.test/a b/?x=1&y=2',
			'Tea & Coffee #sale'
		);

		$this->assertStringNotContainsString( ' ', $url, 'An unencoded space reached the URL.' );
		$this->assertStringNotContainsString( '#sale', $url, 'An unencoded fragment reached the URL.' );
		$this->assertStringContainsString( 'Tea%20%26%20Coffee', $url );

		// The shared URL's own separators must survive as data, not structure.
		$this->assertStringContainsString( 'x%3D1%26y%3D2', $url );
	}

	/**
	 * An unknown network must yield nothing rather than a malformed URL.
	 */
	public function test_unknown_network_yields_nothing(): void {
		$this->assertSame(
			'',
			suitemart_share_url( 'not-a-network', 'https://example.test/', 'Title' )
		);
	}

	/**
	 * Rendered links must be safe to open in a new tab.
	 */
	public function test_rendered_links_are_safe(): void {
		$html = render_block(
			array(
				'blockName'    => 'suitemart/social-share',
				'attrs'        => array( 'networks' => array( 'facebook', 'email' ) ),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		$this->assertStringContainsString( 'rel="noopener noreferrer nofollow"', $html );

		// Icon-only is the default, so the name has to come from the label text
		// rather than from the icon.
		$this->assertStringContainsString( 'sm-share__label--hidden', $html );
		$this->assertStringContainsString( 'Share on Facebook', $html );
	}

	/**
	 * Only requested networks are rendered, and unknown keys are dropped.
	 */
	public function test_unknown_requested_networks_are_ignored(): void {
		$html = render_block(
			array(
				'blockName'    => 'suitemart/social-share',
				'attrs'        => array( 'networks' => array( 'facebook', '<script>', 'nope' ) ),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		$this->assertStringContainsString( 'sm-share__link--facebook', $html );
		$this->assertStringNotContainsString( '<script', $html );
		$this->assertSame( 1, substr_count( $html, 'sm-share__item' ) );
	}
}
