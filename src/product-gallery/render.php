<?php
/**
 * Render the Product Gallery block.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! suitemart_has_woocommerce() ) {
	return;
}

$sm_product_id = suitemart_clamp_int(
	$block->context['postId'] ?? get_the_ID(),
	0,
	0,
	PHP_INT_MAX
);

if ( 0 === $sm_product_id ) {
	return;
}

$sm_product = wc_get_product( $sm_product_id );
if ( ! $sm_product || 'product' !== get_post_type( $sm_product_id ) ) {
	return;
}

$sm_main_image_id = $sm_product->get_image_id();
$sm_gallery_ids   = $sm_product->get_gallery_image_ids();

$sm_all_image_ids = array();
if ( $sm_main_image_id ) {
	$sm_all_image_ids[] = $sm_main_image_id;
}
if ( ! empty( $sm_gallery_ids ) ) {
	$sm_all_image_ids = array_merge( $sm_all_image_ids, $sm_gallery_ids );
}

if ( empty( $sm_all_image_ids ) ) {
	// Fallback placeholder.
	$sm_wrapper = get_block_wrapper_attributes( array( 'class' => 'sm-product-gallery sm-product-gallery--empty' ) );
	echo '<div ' . $sm_wrapper . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	printf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_attr__( 'Placeholder', 'suitemart' ) );
	echo '</div>';
	return;
}

$sm_layout = suitemart_enum( $attributes['layout'] ?? 'horizontal', array( 'horizontal', 'vertical', 'grid' ), 'horizontal' );

$sm_wrapper_classes = 'sm-product-gallery sm-product-gallery--' . $sm_layout;
if ( 'grid' !== $sm_layout ) {
	// Interactive for Swiper initialization.
	$sm_wrapper = get_block_wrapper_attributes(
		array(
			'class'               => $sm_wrapper_classes,
			'data-wp-interactive' => 'suitemart/product-gallery',
			'data-wp-init'        => 'callbacks.init',
		)
	);
} else {
	$sm_wrapper = get_block_wrapper_attributes( array( 'class' => $sm_wrapper_classes ) );
}

?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( 'grid' === $sm_layout ) : ?>
		<div class="sm-product-gallery__grid">
			<?php foreach ( $sm_all_image_ids as $sm_id ) : ?>
				<div class="sm-product-gallery__grid-item">
					<?php echo wp_get_attachment_image( $sm_id, 'woocommerce_single' ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<div class="sm-product-gallery__main swiper">
			<div class="sm-product-gallery__main-track swiper-wrapper">
				<?php foreach ( $sm_all_image_ids as $sm_id ) : ?>
					<div class="sm-product-gallery__main-slide swiper-slide">
						<?php echo wp_get_attachment_image( $sm_id, 'woocommerce_single' ); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( count( $sm_all_image_ids ) > 1 ) : ?>
				<div class="sm-product-gallery__button-prev swiper-button-prev"></div>
				<div class="sm-product-gallery__button-next swiper-button-next"></div>
			<?php endif; ?>
		</div>

		<?php if ( count( $sm_all_image_ids ) > 1 ) : ?>
			<div class="sm-product-gallery__thumbs swiper">
				<div class="sm-product-gallery__thumbs-track swiper-wrapper">
					<?php foreach ( $sm_all_image_ids as $sm_id ) : ?>
						<div class="sm-product-gallery__thumbs-slide swiper-slide">
							<?php echo wp_get_attachment_image( $sm_id, 'woocommerce_gallery_thumbnail' ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
