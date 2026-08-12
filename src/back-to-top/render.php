<?php
/**
 * Back to top block.
 *
 * Hidden until the visitor has scrolled, which is the whole point — a button
 * offering to return you to the top while you are already at the top is noise.
 *
 * "Hidden" here means `inert` and transparent rather than `display: none`,
 * because the button fades in and an element that does not exist cannot
 * animate. `inert` is what keeps it out of the tab order in the meantime; a
 * transparent button that still takes focus is a keyboard trap in miniature,
 * where Tab lands on something invisible.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_threshold  = suitemart_clamp_int( $attributes['threshold'] ?? 400, 400, 0, 20000 );
$sm_position   = suitemart_enum( $attributes['position'] ?? 'end', array( 'start', 'end' ), 'end' );
$sm_appearance = suitemart_enum( $attributes['appearance'] ?? 'icon', array( 'icon', 'icon-label' ), 'icon' );

$sm_label = isset( $attributes['label'] ) && is_string( $attributes['label'] ) && '' !== trim( $attributes['label'] )
	? trim( $attributes['label'] )
	: __( 'Back to top', 'suitemart' );

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => sprintf(
			'sm-back-to-top sm-back-to-top--%s sm-back-to-top--%s',
			$sm_position,
			$sm_appearance
		),
	)
);

/*
 * Seeded false because the server has no idea where the visitor is on the page,
 * and the top is the only honest assumption: a page is served scrolled to the
 * top unless the browser restores a position, which the init callback then
 * corrects for. Guessing the other way would flash the button on every load.
 */
$sm_context = array(
	'isVisible' => false,
	'threshold' => $sm_threshold,
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/back-to-top"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
	data-wp-class--is-visible="context.isVisible"
	data-wp-init="callbacks.watchScroll"
>
	<button
		type="button"
		class="sm-back-to-top__button"
		inert
		data-wp-bind--inert="!context.isVisible"
		data-wp-on--click="actions.toTop"
	>
		<?php echo suitemart_get_icon( 'arrow-up', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		<span class="<?php echo 'icon' === $sm_appearance ? 'screen-reader-text' : 'sm-back-to-top__label'; ?>">
			<?php echo esc_html( $sm_label ); ?>
		</span>
	</button>
</div>
