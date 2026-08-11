<?php
/**
 * Map block.
 *
 * An iframe rather than a JavaScript map library. Leaflet would need a tile
 * provider, and every provider that permits redistribution in a commercial
 * theme wants the site owner's own API key — so the "no configuration" path
 * does not exist there either. An iframe at least works from a pasted link.
 *
 * The consent option matters for buyers in the EU: an embedded Google map is a
 * third-party request made on page load, before anyone has agreed to anything.
 * With it on, nothing is requested until the reader asks for the map.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_url = suitemart_map_url(
	array(
		'source'   => $attributes['source'] ?? 'embed',
		'embedUrl' => $attributes['embedUrl'] ?? '',
		'address'  => $attributes['address'] ?? '',
		'apiKey'   => $attributes['apiKey'] ?? '',
		'zoom'     => $attributes['zoom'] ?? 14,
	)
);

// Nothing configured, or a URL that is not a Google map. Render nothing rather
// than an empty frame.
if ( '' === $sm_url ) {
	return '';
}

$sm_height        = suitemart_clamp_int( $attributes['height'] ?? 420, 420, 120, 1200 );
$sm_height_mobile = suitemart_clamp_int( $attributes['heightMobile'] ?? 280, 280, 120, 1200 );
$sm_consent       = ! empty( $attributes['requireConsent'] );

/*
 * An iframe is announced by its title and nothing else, so "Map" is a poor but
 * honest default and the editor is prompted to better it.
 */
$sm_title = isset( $attributes['title'] ) && is_string( $attributes['title'] ) && '' !== trim( $attributes['title'] )
	? $attributes['title']
	: __( 'Map', 'suitemart' );

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'sm-map' . ( $sm_consent ? ' sm-map--consent' : '' ),
		'style' => sprintf(
			'--sm-map-height:%dpx;--sm-map-height-mobile:%dpx',
			$sm_height,
			$sm_height_mobile
		),
	)
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/map"
	<?php
	echo wp_interactivity_data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
		array(
			// With consent required the frame starts empty and the URL is held
			// here until the reader asks for it, so nothing is requested from
			// Google on page load. Both values resolve on the server.
			'src'        => $sm_consent ? '' : $sm_url,
			'pendingSrc' => $sm_url,
			'isLoaded'   => ! $sm_consent,
		)
	);
	?>
>
	<?php if ( $sm_consent ) : ?>
		<div class="sm-map__consent" data-wp-bind--hidden="context.isLoaded">
			<span class="sm-map__consent-icon">
				<?php echo suitemart_get_icon( 'map-pin', array( 'size' => 28 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			</span>
			<p class="sm-map__consent-text">
				<?php esc_html_e( 'This map is loaded from Google, which will receive your IP address.', 'suitemart' ); ?>
			</p>
			<button type="button" class="sm-map__consent-button" data-wp-on--click="actions.load">
				<?php esc_html_e( 'Load the map', 'suitemart' ); ?>
			</button>
		</div>
	<?php endif; ?>

	<iframe
		class="sm-map__frame"
		title="<?php echo esc_attr( $sm_title ); ?>"
		<?php
		/*
		 * `src` is bound rather than printed so the consent flow can supply it
		 * later. The server resolves the same expression, so with consent off
		 * the frame is fully formed in the HTML and needs no JavaScript at all.
		 */
		?>
		src="<?php echo esc_url( $sm_consent ? '' : $sm_url ); ?>"
		data-wp-bind--src="context.src"
		data-wp-bind--hidden="!context.isLoaded"
		<?php echo $sm_consent ? 'hidden' : ''; ?>
		loading="lazy"
		referrerpolicy="no-referrer-when-downgrade"
		allowfullscreen
	></iframe>
</div>
