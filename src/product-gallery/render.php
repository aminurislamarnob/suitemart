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

/*
 * Variation images.
 *
 * WooCommerce writes the chosen variation's id into its own `woocommerce/products`
 * Interactivity store, but that store is locked private — its unlock string says
 * in as many words that reading it will break on the next release. What is stable
 * is the form: both the classic and the block add-to-cart forms post one
 * `attribute_<name>` field per attribute, because that is what the cart consumes.
 * So the browser reads those fields and does its own matching against the table
 * seeded here, and never touches Woo's internals.
 *
 * Only variations that carry their own image are listed. One that does not shows
 * the parent image, which is already what the gallery renders.
 */
$sm_variations = array();

if ( $sm_product->is_type( 'variable' ) ) {
	foreach ( $sm_product->get_children() as $sm_child_id ) {
		$sm_variation = wc_get_product( $sm_child_id );

		if ( ! $sm_variation instanceof WC_Product_Variation ) {
			continue;
		}

		/*
		 * The 'edit' context matters: read normally, a variation without its own
		 * image reports the parent's, so every variation would be listed and
		 * every one of them would "switch" to the image already on screen.
		 */
		$sm_variation_image_id = (int) $sm_variation->get_image_id( 'edit' );

		if ( ! $sm_variation_image_id ) {
			continue;
		}

		// An empty value means "any", so it matches whatever is chosen.
		$sm_attributes = array();

		foreach ( $sm_variation->get_variation_attributes() as $sm_key => $sm_value ) {
			$sm_attributes[ $sm_key ] = strtolower( (string) $sm_value );
		}

		$sm_variations[] = array(
			'attributes' => (object) $sm_attributes,
			'imageId'    => $sm_variation_image_id,
			'image'      => suitemart_gallery_image_data( $sm_variation_image_id ),
		);
	}
}

$sm_wrapper_classes = 'sm-product-gallery sm-product-gallery--' . $sm_layout;

/*
 * One init callback does both jobs. Unique directive suffixes (`data-wp-init---name`)
 * would let two coexist, but those landed in WordPress 6.9 and the theme floor is
 * 6.7 (decision 19).
 */
$sm_needs_js = 'grid' !== $sm_layout || ! empty( $sm_variations );

if ( $sm_needs_js ) {
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

$sm_context = array(
	'variations' => $sm_variations,
	'slideIds'   => array_map( 'intval', array_values( $sm_all_image_ids ) ),
);

?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php echo $sm_needs_js ? wp_interactivity_data_wp_context( $sm_context ) : ''; ?>
>
	<?php if ( 'grid' === $sm_layout ) : ?>
		<div class="sm-product-gallery__grid">
			<?php foreach ( $sm_all_image_ids as $sm_id ) : ?>
				<div class="sm-product-gallery__grid-item" data-sm-image-id="<?php echo esc_attr( (string) $sm_id ); ?>">
					<?php echo wp_get_attachment_image( $sm_id, 'woocommerce_single' ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<div class="sm-product-gallery__main swiper">
			<div class="sm-product-gallery__main-track swiper-wrapper">
				<?php foreach ( $sm_all_image_ids as $sm_id ) : ?>
					<div class="sm-product-gallery__main-slide swiper-slide" data-sm-image-id="<?php echo esc_attr( (string) $sm_id ); ?>">
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
						<div class="sm-product-gallery__thumbs-slide swiper-slide" data-sm-image-id="<?php echo esc_attr( (string) $sm_id ); ?>">
							<?php echo wp_get_attachment_image( $sm_id, 'woocommerce_gallery_thumbnail' ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
