<?php
/**
 * Timeline block.
 *
 * Rendered as an ordered list because that is what a timeline is: a sequence
 * where the order carries meaning. A screen reader then announces "list, 5
 * items" and the position of each one, which no amount of styling conveys.
 *
 * That is also why timeline-item is locked to this parent — an arbitrary block
 * dropped between the list items would be invalid inside an `<ol>`.
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

$sm_layout = suitemart_enum( $attributes['layout'] ?? 'stacked', array( 'stacked', 'alternating' ), 'stacked' );

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-timeline sm-timeline--' . $sm_layout )
);
?>
<ol <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
</ol>
