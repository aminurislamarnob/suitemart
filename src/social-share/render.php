<?php
/**
 * Share links block.
 *
 * Every network target is a plain link to that service's public share URL. No
 * third-party script is loaded and nothing is embedded, so the block costs
 * nothing until someone clicks it and cannot track a reader who does not.
 *
 * The marks are Suitemart's own simplified glyphs (assets/icons/social.svg).
 * Lucide dropped brand icons, and the services' official logos are trademarks
 * rather than assets to redistribute. Each control carries a text label for
 * assistive technology whatever the visual style.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_appearance = suitemart_enum( $attributes['appearance'] ?? 'icon', array( 'icon', 'icon-label' ), 'icon' );
$sm_shape      = suitemart_enum( $attributes['shape'] ?? 'circle', array( 'square', 'rounded', 'circle', 'bare' ), 'circle' );
$sm_icon_size  = suitemart_clamp_int( $attributes['iconSize'] ?? 18, 18, 12, 48 );
$sm_heading    = isset( $attributes['heading'] ) && is_string( $attributes['heading'] ) ? $attributes['heading'] : '';

$sm_requested = isset( $attributes['networks'] ) && is_array( $attributes['networks'] )
	? array_values( array_filter( $attributes['networks'], 'is_string' ) )
	: array();

$sm_available = suitemart_share_networks();
$sm_networks  = array_values( array_intersect( $sm_requested, array_keys( $sm_available ) ) );

if ( array() === $sm_networks ) {
	return '';
}

/*
 * Share the post being rendered, not whatever URL the request happens to carry:
 * inside a query loop each card must point at its own permalink, and on a
 * paginated or filtered archive `$_SERVER['REQUEST_URI']` would be wrong and
 * untrusted besides.
 */
$sm_post_id = $block->context['postId'] ?? get_the_ID();
$sm_url     = $sm_post_id ? get_permalink( $sm_post_id ) : home_url( add_query_arg( array() ) );
$sm_url     = is_string( $sm_url ) ? $sm_url : home_url( '/' );
$sm_title   = $sm_post_id ? get_the_title( $sm_post_id ) : get_bloginfo( 'name' );

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => sprintf(
			'sm-share sm-share--%s sm-share--%s',
			$sm_appearance,
			$sm_shape
		),
	)
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/share"
	<?php
	echo wp_interactivity_data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
		array(
			'url'     => $sm_url,
			// The clipboard control is useless without JavaScript and without a
			// secure context, so it stays hidden until view.js confirms both.
			// Resolvable on the server, which a JS-only getter would not be.
			'canCopy' => false,
			'didCopy' => false,
		)
	);
	?>
>
	<?php if ( '' !== $sm_heading ) : ?>
		<p class="sm-share__heading"><?php echo esc_html( $sm_heading ); ?></p>
	<?php endif; ?>

	<ul class="sm-share__list">
		<?php foreach ( $sm_networks as $sm_network ) : ?>
			<?php
			$sm_meta  = $sm_available[ $sm_network ];
			$sm_label = $sm_meta['label'];
			?>
			<li class="sm-share__item">
				<?php if ( 'copy' === $sm_network ) : ?>
					<button
						type="button"
						class="sm-share__link sm-share__link--copy"
						hidden
						data-wp-bind--hidden="!context.canCopy"
						data-wp-on--click="actions.copy"
						data-wp-init="callbacks.detectClipboard"
					>
						<?php echo suitemart_get_icon( $sm_meta['icon'], array( 'size' => $sm_icon_size ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
						<span class="<?php echo 'icon' === $sm_appearance ? 'sm-share__label sm-share__label--hidden' : 'sm-share__label'; ?>">
							<?php echo esc_html( $sm_label ); ?>
						</span>
					</button>
				<?php else : ?>
					<a
						class="sm-share__link sm-share__link--<?php echo esc_attr( $sm_network ); ?>"
						href="<?php echo esc_url( suitemart_share_url( $sm_network, $sm_url, $sm_title ) ); ?>"
						<?php
						/*
						 * `noopener` is required: without it the opened tab can
						 * reach back through `window.opener`. `nofollow` keeps
						 * every post from voting for the share endpoints.
						 */
						?>
						target="_blank"
						rel="noopener noreferrer nofollow"
					>
						<?php echo suitemart_get_icon( $sm_meta['icon'], array( 'size' => $sm_icon_size ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
						<span class="<?php echo 'icon' === $sm_appearance ? 'sm-share__label sm-share__label--hidden' : 'sm-share__label'; ?>">
							<?php
							printf(
								/* translators: %s: network name, for example Facebook. */
								esc_html__( 'Share on %s', 'suitemart' ),
								esc_html( $sm_label )
							);
							?>
						</span>
					</a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php
	/*
	 * Copying gives no visual feedback of its own, so the result is announced.
	 * `polite` rather than `assertive`: it is a confirmation, not a warning.
	 */
	?>
	<p class="sm-share__status" role="status" aria-live="polite">
		<span data-wp-bind--hidden="!context.didCopy" hidden>
			<?php esc_html_e( 'Link copied to the clipboard', 'suitemart' ); ?>
		</span>
	</p>
</div>
