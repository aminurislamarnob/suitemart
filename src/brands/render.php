<?php
/**
 * Logo strip block.
 *
 * A layout container rather than a data source: the logos are ordinary
 * core/image blocks, so alt text, linking and the media library all behave
 * exactly as an editor already expects. This block only supplies the grid,
 * the uniform logo height and the muted treatment.
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

$sm_columns        = suitemart_clamp_int( $attributes['columns'] ?? 6, 6, 1, 10 );
$sm_columns_mobile = suitemart_clamp_int( $attributes['columnsMobile'] ?? 2, 2, 1, 6 );
$sm_height         = suitemart_clamp_int( $attributes['logoHeight'] ?? 48, 48, 16, 200 );

$sm_classes = sprintf( 'sm-brands sm-brands--cols-%d sm-brands--mcols-%d', $sm_columns, $sm_columns_mobile );

if ( ! empty( $attributes['muted'] ) ) {
	$sm_classes .= ' sm-brands--muted';
}

if ( ! empty( $attributes['dividers'] ) ) {
	$sm_classes .= ' sm-brands--dividers';
}

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => $sm_classes,
		'style' => sprintf( '--sm-brands-height:%dpx', $sm_height ),
	)
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
</div>
