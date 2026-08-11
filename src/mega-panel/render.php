<?php
/**
 * Mega panel block.
 *
 * A plain container. Everything that makes it a dropdown — visibility, ARIA
 * wiring, keyboard handling — belongs to the parent nav item, so this block can
 * hold literally any inner blocks without knowing anything about navigation.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_width = suitemart_enum(
	$attributes['panelWidth'] ?? 'auto',
	array( 'auto', 'content', 'wide', 'full' ),
	'auto'
);

$sm_align = suitemart_enum(
	$attributes['align'] ?? 'start',
	array( 'start', 'center', 'end' ),
	'start'
);

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => sprintf( 'sm-mega-panel sm-mega-panel--%s sm-mega-panel--align-%s', $sm_width, $sm_align ),
	)
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<div class="sm-mega-panel__inner">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
	</div>
</div>
