<?php
/**
 * Tabs block.
 *
 * Implements the WAI-ARIA APG tabs pattern. The parent renders the tab list
 * because the buttons must be siblings inside one `role="tablist"`, while the
 * labels live on the child blocks — so this reads its children's attributes
 * rather than having each child render its own button.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup (the panels).
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_children = isset( $block->parsed_block['innerBlocks'] ) && is_array( $block->parsed_block['innerBlocks'] )
	? $block->parsed_block['innerBlocks']
	: array();

if ( array() === $sm_children ) {
	return '';
}

$sm_orientation = suitemart_enum( $attributes['orientation'] ?? 'horizontal', array( 'horizontal', 'vertical' ), 'horizontal' );
$sm_activation  = suitemart_enum( $attributes['activation'] ?? 'automatic', array( 'automatic', 'manual' ), 'automatic' );

$sm_group = wp_unique_id( 'sm-tabs-' );

// Ids are assigned here and mirrored onto the panels through block context, so
// aria-controls and aria-labelledby always point at each other.
$sm_tabs = array();

foreach ( $sm_children as $sm_index => $sm_child ) {
	$sm_label = isset( $sm_child['attrs']['label'] ) && is_string( $sm_child['attrs']['label'] )
		? $sm_child['attrs']['label']
		: '';

	$sm_tabs[] = array(
		'label'   => '' !== $sm_label
			/* translators: %d: tab number. */
			? $sm_label : sprintf( __( 'Tab %d', 'suitemart' ), $sm_index + 1 ),
		'icon'    => isset( $sm_child['attrs']['icon'] ) && is_string( $sm_child['attrs']['icon'] )
			? sanitize_key( $sm_child['attrs']['icon'] )
			: '',
		'tabId'   => $sm_group . '-tab-' . $sm_index,
		'panelId' => $sm_group . '-panel-' . $sm_index,
	);
}

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => sprintf( 'sm-tabs sm-tabs--%s', $sm_orientation ),
	)
);

$sm_context = array(
	'activeIndex' => 0,
	'group'       => $sm_group,
	'manual'      => 'manual' === $sm_activation,
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/tabs"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
>
	<div
		class="sm-tabs__list"
		role="tablist"
		aria-orientation="<?php echo esc_attr( $sm_orientation ); ?>"
		data-wp-on--keydown="actions.handleListKeydown"
	>
		<?php foreach ( $sm_tabs as $sm_index => $sm_tab ) : ?>
			<button
				type="button"
				class="sm-tabs__tab<?php echo 0 === $sm_index ? ' is-active' : ''; ?>"
				id="<?php echo esc_attr( $sm_tab['tabId'] ); ?>"
				role="tab"
				aria-controls="<?php echo esc_attr( $sm_tab['panelId'] ); ?>"
				aria-selected="<?php echo 0 === $sm_index ? 'true' : 'false'; ?>"
				<?php
				/*
				 * Roving tabindex: exactly one tab is in the tab order, and the
				 * arrow keys move between them. Without this, Tab would step
				 * through every tab before reaching the panel content.
				 */
				?>
				tabindex="<?php echo 0 === $sm_index ? '0' : '-1'; ?>"
				<?php echo wp_interactivity_data_wp_context( array( 'index' => $sm_index ) ); ?>
				data-wp-on--click="actions.selectTab"
				data-wp-bind--aria-selected="state.isSelected"
				data-wp-bind--tabindex="state.tabIndex"
				data-wp-class--is-active="state.isSelected"
			>
				<?php if ( '' !== $sm_tab['icon'] ) : ?>
					<?php echo suitemart_get_icon( $sm_tab['icon'], array( 'size' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
				<?php endif; ?>
				<span class="sm-tabs__tab-label"><?php echo esc_html( $sm_tab['label'] ); ?></span>
			</button>
		<?php endforeach; ?>
	</div>

	<div class="sm-tabs__panels">
		<?php
		/*
		 * The panels are wired up here rather than in the child block. Each
		 * panel's id has to match the `aria-controls` of its button, so both
		 * sides of that pair are generated in one place — a child assigning its
		 * own id would need to know its position among its siblings, which it
		 * cannot see.
		 */
		$sm_panels    = new WP_HTML_Tag_Processor( $content );
		$sm_panel_num = 0;

		while ( $sm_panels->next_tag( array( 'class_name' => 'sm-tabs__panel' ) ) ) {
			if ( ! isset( $sm_tabs[ $sm_panel_num ] ) ) {
				break;
			}

			$sm_tab = $sm_tabs[ $sm_panel_num ];

			$sm_panels->set_attribute( 'id', $sm_tab['panelId'] );
			$sm_panels->set_attribute( 'role', 'tabpanel' );
			$sm_panels->set_attribute( 'aria-labelledby', $sm_tab['tabId'] );
			$sm_panels->set_attribute(
				'data-wp-context',
				(string) wp_json_encode( array( 'index' => $sm_panel_num ) )
			);
			$sm_panels->set_attribute( 'data-wp-bind--hidden', '!state.isActivePanel' );

			// A panel holding focusable content must itself be focusable, so
			// that Tab from the selected tab lands inside it.
			$sm_panels->set_attribute( 'tabindex', '0' );

			if ( 0 !== $sm_panel_num ) {
				$sm_panels->set_attribute( 'hidden', true );
			}

			++$sm_panel_num;
		}

		echo $sm_panels->get_updated_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress; the tag processor only rewrites attributes.
		?>
	</div>
</div>
