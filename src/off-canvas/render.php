<?php
/**
 * Off-canvas panel block.
 *
 * Rendered as a modal dialog: while it is open the rest of the page is inert,
 * focus is trapped inside, Escape closes it, and focus returns to whatever
 * opened it. Those four behaviours are what separate a dialog from a div that
 * slides in.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_panel_id = isset( $attributes['panelId'] ) && is_string( $attributes['panelId'] ) && '' !== $attributes['panelId']
	? sanitize_key( $attributes['panelId'] )
	: 'panel';

$sm_side = suitemart_enum( $attributes['side'] ?? 'end', array( 'start', 'end', 'top', 'bottom' ), 'end' );

$sm_title = isset( $attributes['title'] ) && is_string( $attributes['title'] ) && '' !== $attributes['title']
	? $attributes['title']
	: __( 'Panel', 'suitemart' );

// The size lands in a CSS custom property, so only accept a plain CSS length.
$sm_size = isset( $attributes['size'] ) && is_string( $attributes['size'] ) ? trim( $attributes['size'] ) : '22rem';

if ( 1 !== preg_match( '/^\d{1,4}(\.\d{1,2})?(px|rem|em|vw|vh|%)$/', $sm_size ) ) {
	$sm_size = '22rem';
}

$sm_dom_id   = 'sm-off-canvas-' . $sm_panel_id;
$sm_title_id = $sm_dom_id . '-title';

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => sprintf( 'sm-off-canvas sm-off-canvas--%s', $sm_side ),
		'style' => sprintf( '--sm-off-canvas-size:%s;', $sm_size ),
	)
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/off-canvas"
	<?php echo wp_interactivity_data_wp_context( array( 'panelId' => $sm_panel_id ) ); ?>
	data-wp-class--is-open="state.isOpen"
	data-wp-watch="callbacks.onToggle"
	data-wp-on-document--keydown="actions.handleKeydown"
>
	<div class="sm-off-canvas__scrim" data-wp-on--click="actions.close" aria-hidden="true"></div>

	<div
		class="sm-off-canvas__panel"
		id="<?php echo esc_attr( $sm_dom_id ); ?>"
		role="dialog"
		aria-modal="true"
		aria-labelledby="<?php echo esc_attr( $sm_title_id ); ?>"
		data-wp-bind--inert="!state.isOpen"
	>
		<div class="sm-off-canvas__header">
			<h2 class="sm-off-canvas__title" id="<?php echo esc_attr( $sm_title_id ); ?>">
				<?php echo esc_html( $sm_title ); ?>
			</h2>
			<button
				type="button"
				class="sm-off-canvas__close"
				data-wp-on--click="actions.close"
			>
				<?php echo suitemart_get_icon( 'x', array( 'label' => __( 'Close panel', 'suitemart' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			</button>
		</div>

		<div class="sm-off-canvas__body">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
		</div>
	</div>
</div>
