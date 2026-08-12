<?php
/**
 * Hotspot marker block.
 *
 * The WAI-ARIA APG disclosure pattern: a button that owns a panel, says whether
 * it is open, and gives focus back when Escape closes it.
 *
 * **Every open/closed value binds to `context`, never to a `state` getter.**
 * Directives are evaluated on the server too, and an expression the server
 * cannot resolve does not leave the markup alone — it strips the attribute.
 * A `state.isOpen` derived in JavaScript from this marker's position among its
 * siblings would resolve to null server-side, which deletes `aria-expanded`
 * outright and, through `!state.isOpen`, adds `hidden` for the wrong reason.
 * Context is seeded here in PHP, so both bindings resolve before any script
 * runs and the served HTML is already correct.
 *
 * That is also why closing the other markers is left to the outside-click
 * handler rather than to a shared open index: one marker cannot write another
 * marker's context, and hoisting the flag into global state would make every
 * marker on the page open together.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup (the panel).
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return '';
}

$sm_x         = suitemart_clamp_int( $attributes['x'] ?? 50, 50, 0, 100 );
$sm_y         = suitemart_clamp_int( $attributes['y'] ?? 50, 50, 0, 100 );
$sm_placement = suitemart_enum( $attributes['placement'] ?? 'top', array( 'top', 'bottom', 'start', 'end' ), 'top' );

$sm_label = isset( $attributes['label'] ) && is_string( $attributes['label'] ) && '' !== trim( $attributes['label'] )
	? $attributes['label']
	: __( 'Show details', 'suitemart' );

// Ids are minted per render, never hardcoded: a hotspot image can appear twice
// on one page, and duplicate ids would make one marker control the other's
// panel.
$sm_id       = wp_unique_id( 'sm-hotspot-' );
$sm_panel_id = $sm_id . '-panel';

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'sm-hotspots__point sm-hotspots__point--' . $sm_placement,
		'style' => sprintf( '--sm-hotspot-x:%d%%;--sm-hotspot-y:%d%%;', $sm_x, $sm_y ),
	)
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/hotspots"
	<?php echo wp_interactivity_data_wp_context( array( 'isOpen' => false ) ); ?>
	data-wp-class--is-open="context.isOpen"
	data-wp-on-document--click="callbacks.closeOnOutsideClick"
	data-wp-on-document--keydown="callbacks.closeOnEscape"
>
	<button
		type="button"
		class="sm-hotspots__marker"
		id="<?php echo esc_attr( $sm_id ); ?>"
		aria-controls="<?php echo esc_attr( $sm_panel_id ); ?>"
		aria-expanded="false"
		data-wp-bind--aria-expanded="context.isOpen"
		data-wp-on--click="actions.toggle"
	>
		<?php
		/*
		 * The visible mark is drawn by the stylesheet — a dot, a plus, or a CSS
		 * counter — so the button's only text is the one screen readers use.
		 */
		?>
		<span class="screen-reader-text"><?php echo esc_html( $sm_label ); ?></span>
	</button>

	<?php
	/*
	 * The frame is forced left-to-right so markers stay over the part of the
	 * photograph they point at — see the note in the stylesheet. That would
	 * drag the panel's text along with it, so each panel restores the site's
	 * own direction explicitly.
	 */
	?>
	<div
		class="sm-hotspots__panel"
		dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>"
		id="<?php echo esc_attr( $sm_panel_id ); ?>"
		aria-labelledby="<?php echo esc_attr( $sm_id ); ?>"
		hidden
		data-wp-bind--hidden="!context.isOpen"
	>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
	</div>
</div>
