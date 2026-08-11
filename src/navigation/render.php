<?php
/**
 * Navigation block.
 *
 * Implements the WAI-ARIA APG "disclosure navigation" pattern rather than a
 * menubar: menubar semantics imply application-style arrow-key navigation that
 * users do not expect on a website, and get wrong more often than not.
 *
 * The whole navigation shares one Interactivity context so that only one panel
 * can be open at a time — nav items compare their own id against `activeId`.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_breakpoint = suitemart_enum(
	$attributes['mobileBreakpoint'] ?? 'lg',
	array( 'sm', 'md', 'lg', 'xl', 'never' ),
	'lg'
);

$sm_trigger = suitemart_enum(
	$attributes['submenuTrigger'] ?? 'hover',
	array( 'hover', 'click' ),
	'hover'
);

$sm_label = isset( $attributes['ariaLabel'] ) && '' !== $attributes['ariaLabel']
	? (string) $attributes['ariaLabel']
	: __( 'Main navigation', 'suitemart' );

$sm_open_icon = suitemart_enum( $attributes['openIcon'] ?? 'menu', array( 'menu', 'grid', 'list' ), 'menu' );

$sm_id = wp_unique_id( 'sm-nav-' );

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'sm-nav sm-nav--break-' . $sm_breakpoint . ' sm-nav--trigger-' . $sm_trigger,
	)
);

// `hoverIntent` is read by the view module: when false, pointer events are
// ignored entirely and the panel is click-only.
$sm_context = array(
	'activeId'     => '',
	'isDrawerOpen' => false,
	'hoverIntent'  => 'hover' === $sm_trigger,
);
?>
<nav
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	aria-label="<?php echo esc_attr( $sm_label ); ?>"
	data-wp-interactive="suitemart/navigation"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
	data-wp-on-document--keydown="actions.handleDocumentKeydown"
	data-wp-on-document--click="actions.handleDocumentClick"
	data-wp-on-window--resize="callbacks.handleResize"
	data-wp-class--is-drawer-open="context.isDrawerOpen"
	data-wp-watch="callbacks.lockBodyScroll"
>
	<button
		type="button"
		class="sm-nav__toggle"
		id="<?php echo esc_attr( $sm_id . '-toggle' ); ?>"
		aria-controls="<?php echo esc_attr( $sm_id . '-drawer' ); ?>"
		data-wp-bind--aria-expanded="context.isDrawerOpen"
		data-wp-on--click="actions.toggleDrawer"
	>
		<span class="sm-nav__toggle-icon" data-wp-bind--hidden="context.isDrawerOpen">
			<?php echo suitemart_get_icon( $sm_open_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		</span>
		<span class="sm-nav__toggle-icon" data-wp-bind--hidden="!context.isDrawerOpen">
			<?php echo suitemart_get_icon( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		</span>
		<span class="sm-nav__toggle-label"><?php esc_html_e( 'Menu', 'suitemart' ); ?></span>
	</button>

	<div
		class="sm-nav__drawer"
		id="<?php echo esc_attr( $sm_id . '-drawer' ); ?>"
		data-wp-bind--inert="state.drawerIsInert"
	>
		<ul class="sm-nav__list" role="list">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
		</ul>
	</div>

	<div class="sm-nav__scrim" data-wp-on--click="actions.closeAll" aria-hidden="true"></div>
</nav>
