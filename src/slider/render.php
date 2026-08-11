<?php
/**
 * Slider block.
 *
 * The markup is a plain, scrollable list of slides that works with no
 * JavaScript at all: it scrolls horizontally with CSS scroll-snap, every slide
 * is reachable by keyboard, and the arrows are ordinary buttons. Swiper then
 * upgrades it. If the module fails to load, the block degrades to a swipeable
 * row rather than to nothing.
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

$sm_per_view = array(
	'mobile'  => suitemart_clamp_int( $attributes['slidesPerView'] ?? 1, 1, 1, 8 ),
	'tablet'  => suitemart_clamp_int( $attributes['slidesPerViewTablet'] ?? 2, 2, 1, 8 ),
	'desktop' => suitemart_clamp_int( $attributes['slidesPerViewDesktop'] ?? 3, 3, 1, 8 ),
);

$sm_gap      = suitemart_clamp_int( $attributes['spaceBetween'] ?? 16, 16, 0, 96 );
$sm_delay    = suitemart_clamp_int( $attributes['autoplayDelay'] ?? 5000, 5000, 1000, 30000 );
$sm_loop     = ! empty( $attributes['loop'] );
$sm_autoplay = ! empty( $attributes['autoplay'] );
$sm_arrows   = ! isset( $attributes['showArrows'] ) || (bool) $attributes['showArrows'];
$sm_dots     = ! isset( $attributes['showPagination'] ) || (bool) $attributes['showPagination'];

$sm_label = isset( $attributes['label'] ) && is_string( $attributes['label'] ) && '' !== $attributes['label']
	? $attributes['label']
	: __( 'Carousel', 'suitemart' );

$sm_id = wp_unique_id( 'sm-slider-' );

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'sm-slider',
		'style' => sprintf(
			'--sm-slider-gap:%dpx;--sm-slider-per-view:%d;--sm-slider-per-view-md:%d;--sm-slider-per-view-lg:%d;',
			$sm_gap,
			$sm_per_view['mobile'],
			$sm_per_view['tablet'],
			$sm_per_view['desktop']
		),
	)
);

$sm_options = array(
	'slidesPerView' => $sm_per_view['mobile'],
	'spaceBetween'  => $sm_gap,
	'loop'          => $sm_loop,
	'autoplay'      => $sm_autoplay,
	'autoplayDelay' => $sm_delay,
	'breakpoints'   => array(
		'768'  => $sm_per_view['tablet'],
		'1024' => $sm_per_view['desktop'],
	),
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	role="group"
	aria-roledescription="carousel"
	aria-label="<?php echo esc_attr( $sm_label ); ?>"
	data-wp-interactive="suitemart/slider"
	<?php echo wp_interactivity_data_wp_context( array( 'options' => $sm_options ) ); ?>
	data-wp-init="callbacks.mount"
>
	<div class="sm-slider__viewport swiper" id="<?php echo esc_attr( $sm_id ); ?>">
		<div class="sm-slider__track swiper-wrapper">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
		</div>
	</div>

	<?php if ( $sm_arrows ) : ?>
		<div class="sm-slider__nav">
			<button type="button" class="sm-slider__arrow sm-slider__arrow--prev" data-wp-on--click="actions.previous">
				<?php echo suitemart_get_icon( 'chevron-left', array( 'label' => __( 'Previous slide', 'suitemart' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			</button>
			<button type="button" class="sm-slider__arrow sm-slider__arrow--next" data-wp-on--click="actions.next">
				<?php echo suitemart_get_icon( 'chevron-right', array( 'label' => __( 'Next slide', 'suitemart' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			</button>
		</div>
	<?php endif; ?>

	<?php if ( $sm_dots ) : ?>
		<div class="sm-slider__pagination swiper-pagination"></div>
	<?php endif; ?>

	<?php if ( $sm_autoplay ) : ?>
		<?php
		/*
		 * WCAG 2.2.2: anything that moves automatically for more than five
		 * seconds needs a visible, keyboard-reachable way to stop it.
		 */
		?>
		<button type="button" class="sm-slider__autoplay-toggle" data-wp-on--click="actions.toggleAutoplay">
			<span data-wp-bind--hidden="context.isPaused"><?php esc_html_e( 'Pause', 'suitemart' ); ?></span>
			<span data-wp-bind--hidden="!context.isPaused" hidden><?php esc_html_e( 'Play', 'suitemart' ); ?></span>
		</button>
	<?php endif; ?>
</div>
