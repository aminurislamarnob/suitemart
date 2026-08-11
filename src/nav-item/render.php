<?php
/**
 * Navigation item block.
 *
 * Renders either a plain link or a disclosure button that owns a mega panel.
 * The two cases are genuinely different elements — a control that opens a panel
 * must be a <button>, not an <a href="#"> — so they are not unified.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup (the mega panel).
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_label = isset( $attributes['label'] ) ? (string) $attributes['label'] : '';

if ( '' === $sm_label ) {
	return '';
}

$sm_url       = isset( $attributes['url'] ) ? (string) $attributes['url'] : '';
$sm_has_panel = ! empty( $attributes['hasPanel'] ) && '' !== trim( $content );
$sm_badge     = isset( $attributes['badge'] ) ? (string) $attributes['badge'] : '';
$sm_new_tab   = ! empty( $attributes['opensInNewTab'] );

$sm_item_id  = wp_unique_id( 'sm-nav-item-' );
$sm_panel_id = $sm_item_id . '-panel';

// Marks the current page so assistive technology and styling agree on it.
$sm_is_current = '' !== $sm_url && untrailingslashit( $sm_url ) === untrailingslashit(
	home_url( add_query_arg( array() ) )
);

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'sm-nav-item' . ( $sm_has_panel ? ' sm-nav-item--has-panel' : '' ),
	)
);

$sm_badge_markup = '' !== $sm_badge
	? sprintf( '<span class="sm-nav-item__badge">%s</span>', esc_html( $sm_badge ) )
	: '';
?>
<li
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	<?php
	/*
	 * The namespace is declared here rather than inherited from the parent
	 * navigation. Directives resolve their namespace from the nearest ancestor
	 * that declares one, so relying on the parent makes this block silently
	 * wrong wherever it is rendered on its own — in a pattern preview, or in
	 * the REST response the editor uses.
	 */
	?>
	data-wp-interactive="suitemart/navigation"
	<?php echo wp_interactivity_data_wp_context( array( 'itemId' => $sm_item_id ) ); ?>
	data-wp-class--is-open="state.isOpen"
	<?php if ( $sm_has_panel ) : ?>
		data-wp-on--mouseenter="actions.pointerEnter"
		data-wp-on--mouseleave="actions.pointerLeave"
		data-wp-on--focusout="actions.handleFocusOut"
	<?php endif; ?>
>
	<?php if ( $sm_has_panel ) : ?>
		<button
			type="button"
			class="sm-nav-item__trigger"
			id="<?php echo esc_attr( $sm_item_id ); ?>"
			aria-controls="<?php echo esc_attr( $sm_panel_id ); ?>"
			aria-haspopup="true"
			data-wp-bind--aria-expanded="state.isOpen"
			data-wp-on--click="actions.toggleItem"
			data-wp-on--keydown="actions.handleTriggerKeydown"
		>
			<span class="sm-nav-item__label"><?php echo esc_html( $sm_label ); ?></span>
			<?php
			echo $sm_badge_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
			echo suitemart_get_icon( 'chevron-down', array( 'class' => 'sm-nav-item__chevron' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
			?>
		</button>

		<div class="sm-nav-item__panel-wrap" id="<?php echo esc_attr( $sm_panel_id ); ?>" data-wp-bind--hidden="!state.isOpen">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
		</div>
	<?php else : ?>
		<a
			class="sm-nav-item__link"
			href="<?php echo esc_url( '' !== $sm_url ? $sm_url : '#' ); ?>"
			<?php echo $sm_is_current ? ' aria-current="page"' : ''; ?>
			<?php if ( $sm_new_tab ) : ?>
				target="_blank" rel="noopener<?php echo ! empty( $attributes['rel'] ) ? ' ' . esc_attr( (string) $attributes['rel'] ) : ''; ?>"
			<?php elseif ( ! empty( $attributes['rel'] ) ) : ?>
				rel="<?php echo esc_attr( (string) $attributes['rel'] ); ?>"
			<?php endif; ?>
		>
			<span class="sm-nav-item__label"><?php echo esc_html( $sm_label ); ?></span>
			<?php echo $sm_badge_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>
		</a>
	<?php endif; ?>
</li>
