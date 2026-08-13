<?php
/**
 * Popup block.
 *
 * A modal dialog holding arbitrary inner blocks: a newsletter form, an offer, an
 * age check. Built on `<dialog>` and opened with `showModal()`, which is the
 * reason this block is short. The browser then owns the hard parts of the APG
 * modal pattern — focus moves into the dialog and is trapped there, everything
 * behind it goes inert, Escape closes it, and it is painted in the top layer, so
 * no z-index on the page can end up above it. A hand-written focus trap is
 * where modals go wrong, and there is no reason to write another one.
 *
 * The markup is served closed and never opened by the server, because every
 * trigger — a delay, a scroll position, a pointer heading for the tab bar —
 * happens in the browser, as does the memory of having seen it, which lives in
 * localStorage so that pages stay cacheable.
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

$sm_trigger   = suitemart_enum( $attributes['trigger'] ?? 'delay', array( 'delay', 'scroll', 'exit' ), 'delay' );
$sm_threshold = suitemart_clamp_int( $attributes['threshold'] ?? 800, 800, 0, 20000 );
$sm_max_width = suitemart_clamp_int( $attributes['maxWidth'] ?? 520, 520, 240, 1200 );

// Seconds in the editor, milliseconds in the browser.
$sm_delay = suitemart_clamp_int( $attributes['delay'] ?? 5, 5, 0, 120 ) * 1000;

$sm_overlay_close = ! isset( $attributes['overlayClose'] ) || (bool) $attributes['overlayClose'];

/*
 * Same arrangement as the floating block: the key is written once by the editor
 * from the client id, because nothing available at render time is stable across
 * requests. No key means no memory, and a popup with no memory is one that
 * greets the same visitor on every page — so this defaults to on and the editor
 * fills the key in the moment the block is inserted.
 */
$sm_key = isset( $attributes['onceKey'] ) && is_string( $attributes['onceKey'] )
	? sanitize_key( $attributes['onceKey'] )
	: '';

$sm_once = ( ! isset( $attributes['showOnce'] ) || (bool) $attributes['showOnce'] ) && '' !== $sm_key;

$sm_label = isset( $attributes['label'] ) && is_string( $attributes['label'] ) && '' !== trim( $attributes['label'] )
	? trim( $attributes['label'] )
	: __( 'Notice', 'suitemart' );

$sm_close_label = isset( $attributes['closeLabel'] ) && is_string( $attributes['closeLabel'] ) && '' !== trim( $attributes['closeLabel'] )
	? trim( $attributes['closeLabel'] )
	: __( 'Close', 'suitemart' );

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'sm-popup',
		'style' => sprintf( '--sm-popup-max-width:%dpx;', $sm_max_width ),
	)
);

$sm_context = array(
	'trigger'      => $sm_trigger,
	'threshold'    => $sm_threshold,
	'delay'        => $sm_delay,
	'overlayClose' => $sm_overlay_close,
	'key'          => $sm_once ? $sm_key : '',
);
?>
<div
	class="sm-popup__mount"
	data-wp-interactive="suitemart/popup"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
	data-wp-init="callbacks.watchForTrigger"
>
	<?php
	/*
	 * The dialog is a sibling of nothing and a child of one plain div, which is
	 * deliberate: `showModal()` moves the element into the top layer, and a
	 * wrapper carrying the block's own padding or background would be left
	 * behind it, drawing an empty box on the page. The wrapper attributes go on
	 * the dialog itself for the same reason.
	 */
	?>
	<dialog
		<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
		aria-label="<?php echo esc_attr( $sm_label ); ?>"
		data-wp-on--close="actions.onClose"
		data-wp-on--click="actions.onBackdropClick"
	>
		<button
			type="button"
			class="sm-popup__close"
			data-wp-on--click="actions.close"
		>
			<?php echo suitemart_get_icon( 'x', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			<span class="screen-reader-text"><?php echo esc_html( $sm_close_label ); ?></span>
		</button>

		<div class="sm-popup__content">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
		</div>
	</dialog>
</div>
