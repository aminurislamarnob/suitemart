<?php
/**
 * Tab panel block.
 *
 * Renders only the panel. Its id, ARIA wiring and initial visibility are
 * applied by the parent Tabs block, which is the only place that knows this
 * panel's position among its siblings.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_wrapper = get_block_wrapper_attributes( array( 'class' => 'sm-tabs__panel' ) );
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
</div>
