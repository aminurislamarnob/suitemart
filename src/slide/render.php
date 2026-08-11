<?php
/**
 * Slide block.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// `swiper-slide` is required by Swiper; `sm-slider__slide` carries our styling
// and the no-JavaScript scroll-snap behaviour.
$sm_wrapper = get_block_wrapper_attributes( array( 'class' => 'sm-slider__slide swiper-slide' ) );
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
</div>
