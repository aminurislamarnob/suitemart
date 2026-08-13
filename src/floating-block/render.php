<?php
/**
 * Floating block.
 *
 * A container pinned to a corner of the viewport, holding whatever blocks the
 * editor puts in it. Woodmart's equivalent is a promo panel, but the useful
 * thing here is that the contents are arbitrary — a discount code, a chat
 * prompt, a shipping notice — so the block itself only decides where it sits,
 * when it appears, and how it goes away.
 *
 * Whether it is served open matters. With no memory and no trigger the block is
 * served visible, so it works with JavaScript switched off. As soon as it has
 * anything to check first — a delay, a scroll position, a previous dismissal
 * kept in localStorage, which the server cannot see because the answer is
 * per-browser and the page has to stay cacheable — it is served hidden instead
 * and opened by the browser. Served the other way round it would flash on every
 * load at people who had already closed it.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return '';
}

$sm_position = suitemart_enum(
	$attributes['position'] ?? 'bottom-end',
	array( 'top-start', 'top-end', 'middle-start', 'middle-end', 'bottom-start', 'bottom-end' ),
	'bottom-end'
);

$sm_trigger   = suitemart_enum( $attributes['trigger'] ?? 'immediate', array( 'immediate', 'scroll', 'delay' ), 'immediate' );
$sm_threshold = suitemart_clamp_int( $attributes['threshold'] ?? 600, 600, 0, 20000 );
$sm_max_width = suitemart_clamp_int( $attributes['maxWidth'] ?? 360, 360, 160, 1200 );

// Seconds in the editor, milliseconds in the browser. Capped at two minutes:
// past that it is not a delayed panel, it is one nobody will ever see.
$sm_delay = suitemart_clamp_int( $attributes['delay'] ?? 5, 5, 0, 120 ) * 1000;

$sm_dismissible = ! isset( $attributes['dismissible'] ) || (bool) $attributes['dismissible'];
$sm_remember    = isset( $attributes['remember'] ) && (bool) $attributes['remember'];

/*
 * The key is written once by the editor and saved with the block, because the
 * only identifiers available at render time — the loop position, an id from
 * wp_unique_id() — change between requests, and a remembered dismissal keyed on
 * one of those would either be forgotten immediately or applied to the wrong
 * panel. No key means no memory, which is why the editor sets one the moment
 * the toggle is turned on.
 */
$sm_key = isset( $attributes['rememberKey'] ) && is_string( $attributes['rememberKey'] )
	? sanitize_key( $attributes['rememberKey'] )
	: '';

// And there is nothing to remember about a panel that cannot be closed.
$sm_remember = $sm_remember && $sm_dismissible && '' !== $sm_key;

$sm_dismiss_label = isset( $attributes['dismissLabel'] ) && is_string( $attributes['dismissLabel'] ) && '' !== trim( $attributes['dismissLabel'] )
	? trim( $attributes['dismissLabel'] )
	: __( 'Close', 'suitemart' );

$sm_starts_open = 'immediate' === $sm_trigger && ! $sm_remember;

$sm_classes = array(
	'sm-floating-block',
	'sm-floating-block--' . $sm_position,
);

if ( isset( $attributes['hideOnMobile'] ) && (bool) $attributes['hideOnMobile'] ) {
	$sm_classes[] = 'is-hidden-on-mobile';
}

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $sm_classes ),
		'style' => sprintf( '--sm-floating-max-width:%dpx;', $sm_max_width ),
	)
);

$sm_context = array(
	'isOpen'    => $sm_starts_open,
	'trigger'   => $sm_trigger,
	'threshold' => $sm_threshold,
	'delay'     => $sm_delay,
	'key'       => $sm_remember ? $sm_key : '',
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	<?php echo $sm_starts_open ? '' : 'hidden'; ?>
	data-wp-interactive="suitemart/floating-block"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
	data-wp-bind--hidden="!context.isOpen"
	data-wp-init="callbacks.decideVisibility"
>
	<div class="sm-floating-block__content">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
	</div>

	<?php if ( $sm_dismissible ) : ?>
		<button
			type="button"
			class="sm-floating-block__dismiss"
			data-wp-on--click="actions.dismiss"
		>
			<?php echo suitemart_get_icon( 'x', array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			<span class="screen-reader-text"><?php echo esc_html( $sm_dismiss_label ); ?></span>
		</button>
	<?php endif; ?>
</div>
