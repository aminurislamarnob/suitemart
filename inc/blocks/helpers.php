<?php
/**
 * Shared helpers for block render templates.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Records that an icon was used, and reports whether any icon has been.
 *
 * Pages that render no icons should not pay for the sprite, so the sprite is
 * printed in the footer only when something asked for an icon earlier in the
 * request. `<use>` references resolve against the sprite wherever it appears in
 * the document, so printing late is safe.
 *
 * @param string|null $name Icon name to record, or null to query.
 * @return bool True when at least one icon has been rendered this request.
 */
function suitemart_icon_registry( ?string $name = null ): bool {
	static $used = false;

	if ( null !== $name ) {
		$used = true;
	}

	return $used;
}

/**
 * Prints the icon sprite into the footer when any icon was rendered.
 *
 * The sprite is inlined rather than referenced as an external file because
 * cross-document `<use href="file.svg#id">` does not resolve when the sprite is
 * served from another origin — the normal setup once a CDN is in front of the site.
 *
 * @return void
 */
function suitemart_print_icon_sprite(): void {
	if ( ! suitemart_icon_registry() ) {
		return;
	}

	$path = SUITEMART_DIR . '/assets/icons/sprite.svg';

	if ( ! file_exists( $path ) ) {
		return;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme asset, not a remote request.
	$sprite = file_get_contents( $path );

	if ( false === $sprite ) {
		return;
	}

	echo wp_kses(
		sprintf( '<div class="sm-icon-sprite" aria-hidden="true" hidden>%s</div>', $sprite ),
		suitemart_svg_allowed_html()
	);
}
add_action( 'wp_footer', 'suitemart_print_icon_sprite', 5 );

/**
 * The subset of SVG markup Suitemart allows through wp_kses().
 *
 * @return array<string, array<string, bool>>
 */
function suitemart_svg_allowed_html(): array {
	$attrs = array(
		'xmlns'           => true,
		'viewbox'         => true,
		'width'           => true,
		'height'          => true,
		'fill'            => true,
		'stroke'          => true,
		'stroke-width'    => true,
		'stroke-linecap'  => true,
		'stroke-linejoin' => true,
		'class'           => true,
		'id'              => true,
		'aria-hidden'     => true,
		'aria-label'      => true,
		'role'            => true,
		'focusable'       => true,
		'hidden'          => true,
		'href'            => true,
		'xlink:href'      => true,
		'd'               => true,
		'points'          => true,
		'x'               => true,
		'y'               => true,
		'x1'              => true,
		'y1'              => true,
		'x2'              => true,
		'y2'              => true,
		'cx'              => true,
		'cy'              => true,
		'r'               => true,
		'rx'              => true,
		'ry'              => true,
		'transform'       => true,
	);

	return array(
		'div'      => array(
			'class'       => true,
			'aria-hidden' => true,
			'hidden'      => true,
		),
		'svg'      => $attrs,
		'symbol'   => $attrs,
		'use'      => $attrs,
		'title'    => array( 'id' => true ),
		'g'        => $attrs,
		'path'     => $attrs,
		'circle'   => $attrs,
		'rect'     => $attrs,
		'line'     => $attrs,
		'polyline' => $attrs,
		'polygon'  => $attrs,
		'ellipse'  => $attrs,
	);
}

/**
 * Renders an icon from the sprite.
 *
 * Icons inherit `currentColor` and scale with font-size by default, so callers
 * style them by styling their container.
 *
 * @param string               $name Icon id without the `sm-icon-` prefix, e.g. 'cart'.
 * @param array<string, mixed> $args {
 *     Optional arguments.
 *
 *     @type int    $size  Pixel size for width/height. Default 24.
 *     @type string $label Accessible name. When empty the icon is decorative and
 *                         hidden from assistive technology — which is correct
 *                         whenever adjacent text already names the control.
 *     @type string $class Extra class names.
 * }
 * @return string Escaped SVG markup.
 */
function suitemart_get_icon( string $name, array $args = array() ): string {
	suitemart_icon_registry( $name );

	$size  = isset( $args['size'] ) ? absint( $args['size'] ) : 24;
	$label = isset( $args['label'] ) ? (string) $args['label'] : '';
	$class = isset( $args['class'] ) ? (string) $args['class'] : '';

	// Share marks are filled shapes rather than strokes, and take the opposite
	// presentation from the Lucide set they share the sprite with.
	$variant = str_starts_with( $name, 'share-' ) ? ' sm-icon--social' : '';

	$classes = trim( 'sm-icon sm-icon--' . sanitize_html_class( $name ) . $variant . ' ' . $class );

	$a11y = '' !== $label
		? sprintf( 'role="img" aria-label="%s"', esc_attr( $label ) )
		: 'aria-hidden="true" focusable="false"';

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" %3$s><use href="#sm-icon-%4$s"></use></svg>',
		esc_attr( $classes ),
		$size,
		$a11y, // Composed above from escaped parts.
		esc_attr( sanitize_key( $name ) )
	);
}

/**
 * The share targets the Share Links block supports.
 *
 * Kept in PHP because render.php builds the URLs — only the server knows the
 * permalink and title of the post being rendered. `src/social-share/networks.js`
 * mirrors the list for the editor's picker, and
 * `Test_Share::test_network_lists_match` asserts the two agree.
 *
 * @return array<string, array{label: string, icon: string}>
 */
function suitemart_share_networks(): array {
	return array(
		'facebook'  => array(
			'label' => __( 'Facebook', 'suitemart' ),
			'icon'  => 'share-facebook',
		),
		'x'         => array(
			'label' => __( 'X', 'suitemart' ),
			'icon'  => 'share-x',
		),
		'linkedin'  => array(
			'label' => __( 'LinkedIn', 'suitemart' ),
			'icon'  => 'share-linkedin',
		),
		'pinterest' => array(
			'label' => __( 'Pinterest', 'suitemart' ),
			'icon'  => 'share-pinterest',
		),
		'whatsapp'  => array(
			'label' => __( 'WhatsApp', 'suitemart' ),
			'icon'  => 'share-whatsapp',
		),
		'telegram'  => array(
			'label' => __( 'Telegram', 'suitemart' ),
			'icon'  => 'share-telegram',
		),
		'reddit'    => array(
			'label' => __( 'Reddit', 'suitemart' ),
			'icon'  => 'share-reddit',
		),
		'email'     => array(
			'label' => __( 'Email', 'suitemart' ),
			'icon'  => 'share-email',
		),
		'copy'      => array(
			'label' => __( 'Copy link', 'suitemart' ),
			'icon'  => 'link',
		),
	);
}

/**
 * Builds the public share URL for a network.
 *
 * Every value is passed through `rawurlencode()` before it reaches a query
 * string. `esc_url()` alone would not be enough: a title containing `&` or `#`
 * would otherwise terminate the parameter and corrupt the rest of the URL.
 *
 * @param string $network Network key from suitemart_share_networks().
 * @param string $url     Canonical URL of the page being shared.
 * @param string $title   Title of the page being shared.
 * @return string Share endpoint, or an empty string for an unknown network.
 */
function suitemart_share_url( string $network, string $url, string $title ): string {
	$encoded_url   = rawurlencode( $url );
	$encoded_title = rawurlencode( $title );

	switch ( $network ) {
		case 'facebook':
			return 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
		case 'x':
			return 'https://x.com/intent/post?url=' . $encoded_url . '&text=' . $encoded_title;
		case 'linkedin':
			return 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url;
		case 'pinterest':
			return 'https://pinterest.com/pin/create/button/?url=' . $encoded_url . '&description=' . $encoded_title;
		case 'whatsapp':
			return 'https://api.whatsapp.com/send?text=' . $encoded_title . '%20' . $encoded_url;
		case 'telegram':
			return 'https://t.me/share/url?url=' . $encoded_url . '&text=' . $encoded_title;
		case 'reddit':
			return 'https://www.reddit.com/submit?url=' . $encoded_url . '&title=' . $encoded_title;
		case 'email':
			return 'mailto:?subject=' . $encoded_title . '&body=' . $encoded_url;
		default:
			return '';
	}
}

/**
 * Normalises a block attribute to a bounded integer.
 *
 * Block attributes arrive from post content and can be anything after a manual
 * edit or a failed migration; render templates must never trust them.
 *
 * @param mixed $value    Raw attribute value.
 * @param int   $fallback Value to use when the attribute is unusable.
 * @param int   $min      Lower bound.
 * @param int   $max      Upper bound.
 * @return int
 */
function suitemart_clamp_int( mixed $value, int $fallback, int $min, int $max ): int {
	if ( ! is_scalar( $value ) || ! is_numeric( $value ) ) {
		return $fallback;
	}

	return max( $min, min( $max, (int) $value ) );
}

/**
 * Normalises a block attribute to one of an allowed set of strings.
 *
 * @param mixed              $value    Raw attribute value.
 * @param array<int, string> $allowed  Permitted values.
 * @param string             $fallback Value to use when the attribute is not permitted.
 * @return string
 */
function suitemart_enum( mixed $value, array $allowed, string $fallback ): string {
	return is_string( $value ) && in_array( $value, $allowed, true ) ? $value : $fallback;
}
