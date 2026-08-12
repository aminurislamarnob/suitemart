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
 * Hosts a Suitemart map is allowed to frame.
 *
 * An `iframe src` taken from a block attribute is a route to embedding
 * anything at all on a page — a login form, a payment prompt — under the
 * site's own domain. Anyone who can edit a post can set that attribute, which
 * on a multi-author site is a wider group than the administrators. So the host
 * is checked against this list and nothing else is ever framed.
 *
 * @return array<int, string>
 */
function suitemart_map_allowed_hosts(): array {
	return array(
		'www.google.com',
		'maps.google.com',
		'google.com',
	);
}

/**
 * Validates a pasted Google Maps embed URL.
 *
 * @param string $url Candidate URL.
 * @return string The URL when it is a Google Maps embed, otherwise an empty string.
 */
function suitemart_map_validate_embed_url( string $url ): string {
	$parts = wp_parse_url( $url );

	if ( ! is_array( $parts ) || empty( $parts['host'] ) || empty( $parts['scheme'] ) ) {
		return '';
	}

	// Framing over plain HTTP on an HTTPS page is blocked by the browser
	// anyway, and would be a downgrade besides.
	if ( 'https' !== strtolower( $parts['scheme'] ) ) {
		return '';
	}

	if ( ! in_array( strtolower( $parts['host'] ), suitemart_map_allowed_hosts(), true ) ) {
		return '';
	}

	// Both the "Share → Embed a map" iframe and the Embed API live under
	// /maps/embed; the keyless query form is /maps with output=embed.
	$path  = $parts['path'] ?? '';
	$query = $parts['query'] ?? '';

	$is_embed_path  = str_starts_with( $path, '/maps/embed' );
	$is_embed_query = str_starts_with( $path, '/maps' ) && str_contains( $query, 'output=embed' );

	return ( $is_embed_path || $is_embed_query ) ? $url : '';
}

/**
 * Builds the map URL for the Map block.
 *
 * Two routes, because Google offers no single one that is both keyless and
 * documented:
 *
 * - `embed` takes the URL from "Share → Embed a map". No API key, officially
 *   provided, but the editor has to fetch it by hand.
 * - `address` builds the URL. With a key it uses the documented Maps Embed
 *   API. Without one it falls back to `output=embed`, which works and has for
 *   years but is not documented, so Google may withdraw it.
 *
 * @param array<string, mixed> $args {
 *     Map configuration.
 *
 *     @type string $source   Either 'embed' or 'address'.
 *     @type string $embedUrl Pasted embed URL, for the 'embed' source.
 *     @type string $address  Place or address, for the 'address' source.
 *     @type string $apiKey   Optional Maps Embed API key.
 *     @type int    $zoom     Zoom level.
 * }
 * @return string Frameable URL, or an empty string when there is nothing to show.
 */
function suitemart_map_url( array $args ): string {
	$source = suitemart_enum( $args['source'] ?? 'embed', array( 'embed', 'address' ), 'embed' );

	if ( 'embed' === $source ) {
		$url = isset( $args['embedUrl'] ) && is_string( $args['embedUrl'] ) ? $args['embedUrl'] : '';

		return suitemart_map_validate_embed_url( $url );
	}

	$address = isset( $args['address'] ) && is_string( $args['address'] ) ? trim( $args['address'] ) : '';

	if ( '' === $address ) {
		return '';
	}

	$zoom    = suitemart_clamp_int( $args['zoom'] ?? 14, 14, 1, 21 );
	$api_key = isset( $args['apiKey'] ) && is_string( $args['apiKey'] ) ? trim( $args['apiKey'] ) : '';

	if ( '' !== $api_key ) {
		return add_query_arg(
			array(
				'key'  => rawurlencode( $api_key ),
				'q'    => rawurlencode( $address ),
				'zoom' => $zoom,
			),
			'https://www.google.com/maps/embed/v1/place'
		);
	}

	return add_query_arg(
		array(
			'q'      => rawurlencode( $address ),
			'z'      => $zoom,
			'output' => 'embed',
		),
		'https://maps.google.com/maps'
	);
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

/**
 * How many products may be compared at once.
 *
 * A comparison table is read across, so the ceiling is a legibility limit
 * rather than a technical one: past four columns the table stops fitting on a
 * laptop and the rows it exists to compare no longer line up on screen.
 *
 * Both the button and the table read this, and the browser is given the same
 * number through the Interactivity store, so there is one value to change.
 *
 * @return int Between 2 and 6.
 */
function suitemart_compare_limit(): int {
	/**
	 * Filters the maximum number of products in the comparison list.
	 *
	 * @param int $limit Default 4.
	 */
	$limit = apply_filters( 'suitemart_compare_limit', 4 );

	return suitemart_clamp_int( $limit, 4, 2, 6 );
}

/**
 * The DOM id shared by a size guide modal and the button that opens it.
 *
 * The two are separate blocks with no common ancestor, so they cannot pass an
 * id to each other through block context — they have to derive the same one
 * independently. The post they sit inside does that, and it is what keeps a
 * product grid working: every card holds a different product, so every card
 * gets a distinct id and one click opens one modal. Hardcoding the id instead
 * gave four cards the same `id` and `aria-controls`, and opened all four.
 *
 * Outside a post — a pattern previewed on its own, a bare `do_blocks()` call —
 * there is no id to derive from, so the pair fall back to matching in document
 * order: the second button on the page targets the second modal.
 *
 * @param int    $post_id Post the block is rendering against, or 0 if none.
 * @param string $role    Either 'modal' or 'button'.
 * @return string DOM id.
 */
function suitemart_size_guide_id( int $post_id, string $role ): string {
	static $counts = array(
		'modal'  => 0,
		'button' => 0,
	);

	if ( $post_id > 0 ) {
		return 'sm-size-guide-' . $post_id;
	}

	$role = isset( $counts[ $role ] ) ? $role : 'modal';

	++$counts[ $role ];

	return 'sm-size-guide-n' . $counts[ $role ];
}

/**
 * The parts of an attachment a gallery needs in order to swap it in client-side.
 *
 * A variation's image is not always one of the gallery's own slides, so the
 * browser sometimes has to rewrite the visible <img> rather than move to an
 * existing one. Passing the resolved sources means it can do that without a
 * round trip, and without guessing at WordPress's image-size naming.
 *
 * @param int $attachment_id Attachment to describe.
 * @return array{src: string, srcset: string, sizes: string, alt: string}|null
 *         Null when the attachment has no image of the expected size.
 */
function suitemart_gallery_image_data( int $attachment_id ): ?array {
	$image = wp_get_attachment_image_src( $attachment_id, 'woocommerce_single' );

	if ( ! is_array( $image ) || '' === (string) $image[0] ) {
		return null;
	}

	$srcset = wp_get_attachment_image_srcset( $attachment_id, 'woocommerce_single' );
	$sizes  = wp_get_attachment_image_sizes( $attachment_id, 'woocommerce_single' );

	return array(
		'src'    => (string) $image[0],
		'srcset' => is_string( $srcset ) ? $srcset : '',
		'sizes'  => is_string( $sizes ) ? $sizes : '',
		'alt'    => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
	);
}

/**
 * Renders a block image from either an attachment id or a bare URL.
 *
 * Blocks that take an image accept both: `mediaId` when it came from the media
 * library, and `mediaUrl` when a pattern or an import supplied a plain address.
 * Only the id path gets responsive sources, which is the reason to prefer it —
 * the URL path exists so the block still renders rather than vanishing.
 *
 * @param int    $attachment_id Attachment id, or 0 to use the URL.
 * @param string $url           Fallback image URL.
 * @param string $alt           Alternative text.
 * @param string $class_name    Class for the img element.
 * @param string $size          Registered image size for the attachment path.
 * @param bool   $eager         True to load eagerly, for images above the fold.
 * @return string Image markup, or an empty string when there is no image.
 */
function suitemart_block_image( int $attachment_id, string $url, string $alt, string $class_name, string $size = 'large', bool $eager = false ): string {
	$loading = $eager ? 'eager' : 'lazy';

	if ( $attachment_id > 0 ) {
		return (string) wp_get_attachment_image(
			$attachment_id,
			$size,
			false,
			array(
				'class'   => $class_name,
				'alt'     => $alt,
				'loading' => $loading,
			)
		);
	}

	if ( '' === $url ) {
		return '';
	}

	return sprintf(
		'<img class="%s" src="%s" alt="%s" loading="%s" decoding="async" />',
		esc_attr( $class_name ),
		esc_url( $url ),
		esc_attr( $alt ),
		esc_attr( $loading )
	);
}
