<?php
/**
 * Testimonials grid block.
 *
 * Deliberately not a carousel. Sliding testimonials need Swiper, a pause
 * control, focus management and an accessible carousel role — all of which the
 * Slider block already implements. Putting testimonials inside a Slider is
 * both less code and a better result than a second carousel with its own bugs.
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

$sm_columns = suitemart_clamp_int( $attributes['columns'] ?? 3, 3, 1, 4 );
$sm_tablet  = suitemart_clamp_int( $attributes['columnsTablet'] ?? 2, 2, 1, 3 );

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => sprintf(
			'sm-testimonials sm-testimonials--cols-%d sm-testimonials--tcols-%d',
			$sm_columns,
			$sm_tablet
		),
	)
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
</div>
