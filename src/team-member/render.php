<?php
/**
 * Team member block.
 *
 * The portrait is resolved from its attachment id rather than the saved URL
 * wherever possible, so it picks up responsive srcset, lazy loading and any
 * regenerated sizes. The saved URL is only a fallback for images that are not
 * in the media library.
 *
 * Inner blocks hold the social links: core/social-links already does that job
 * properly, including brand icons this theme's Lucide sprite deliberately
 * does not carry.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_name = isset( $attributes['name'] ) && is_string( $attributes['name'] ) ? $attributes['name'] : '';
$sm_role = isset( $attributes['role'] ) && is_string( $attributes['role'] ) ? $attributes['role'] : '';
$sm_bio  = isset( $attributes['bio'] ) && is_string( $attributes['bio'] ) ? $attributes['bio'] : '';

$sm_image_id  = suitemart_clamp_int( $attributes['imageId'] ?? 0, 0, 0, PHP_INT_MAX );
$sm_image_url = isset( $attributes['imageUrl'] ) && is_string( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : '';
$sm_image_alt = isset( $attributes['imageAlt'] ) && is_string( $attributes['imageAlt'] ) ? $attributes['imageAlt'] : '';

$sm_level = suitemart_clamp_int( $attributes['nameLevel'] ?? 3, 3, 2, 6 );
$sm_shape = suitemart_enum( $attributes['imageShape'] ?? 'rounded', array( 'square', 'rounded', 'circle' ), 'rounded' );
$sm_ratio = suitemart_enum( $attributes['aspectRatio'] ?? '0.8', array( '1', '0.8', '0.75', '1.25' ), '0.8' );
$sm_align = suitemart_enum( $attributes['alignment'] ?? 'start', array( 'start', 'center' ), 'start' );

if ( '' === $sm_name && '' === $sm_role && 0 === $sm_image_id && '' === $sm_image_url ) {
	return '';
}

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => sprintf(
			'sm-team-member sm-team-member--%s sm-team-member--align-%s',
			$sm_shape,
			$sm_align
		),
		'style' => '--sm-team-ratio:' . $sm_ratio,
	)
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php if ( 0 !== $sm_image_id || '' !== $sm_image_url ) : ?>
		<figure class="sm-team-member__figure">
			<?php
			if ( 0 !== $sm_image_id ) {
				echo wp_get_attachment_image(
					$sm_image_id,
					'medium_large',
					false,
					array(
						'class'   => 'sm-team-member__image',
						'alt'     => $sm_image_alt,
						'loading' => 'lazy',
					)
				);
			} else {
				printf(
					'<img class="sm-team-member__image" src="%1$s" alt="%2$s" loading="lazy" decoding="async" />',
					esc_url( $sm_image_url ),
					esc_attr( $sm_image_alt )
				);
			}
			?>
		</figure>
	<?php endif; ?>

	<div class="sm-team-member__body">
		<?php if ( '' !== $sm_name ) : ?>
			<?php printf( '<h%d class="sm-team-member__name">%s</h%d>', (int) $sm_level, esc_html( $sm_name ), (int) $sm_level ); ?>
		<?php endif; ?>

		<?php if ( '' !== $sm_role ) : ?>
			<p class="sm-team-member__role"><?php echo esc_html( $sm_role ); ?></p>
		<?php endif; ?>

		<?php if ( '' !== $sm_bio ) : ?>
			<p class="sm-team-member__bio"><?php echo esc_html( $sm_bio ); ?></p>
		<?php endif; ?>

		<?php if ( '' !== trim( $content ) ) : ?>
			<div class="sm-team-member__links">
				<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
			</div>
		<?php endif; ?>
	</div>
</div>
