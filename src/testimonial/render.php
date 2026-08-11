<?php
/**
 * Testimonial block.
 *
 * Not locked to a parent: a testimonial is just as likely to sit inside a
 * Slider, a Column or a pattern as inside the Testimonials grid.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_quote  = isset( $attributes['quote'] ) && is_string( $attributes['quote'] ) ? $attributes['quote'] : '';
$sm_author = isset( $attributes['author'] ) && is_string( $attributes['author'] ) ? $attributes['author'] : '';
$sm_role   = isset( $attributes['role'] ) && is_string( $attributes['role'] ) ? $attributes['role'] : '';

if ( '' === $sm_quote ) {
	return '';
}

$sm_rating    = suitemart_clamp_int( $attributes['rating'] ?? 0, 0, 0, 5 );
$sm_align     = suitemart_enum( $attributes['alignment'] ?? 'start', array( 'start', 'center' ), 'start' );
$sm_image_id  = suitemart_clamp_int( $attributes['imageId'] ?? 0, 0, 0, PHP_INT_MAX );
$sm_image_url = isset( $attributes['imageUrl'] ) && is_string( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : '';
$sm_image_alt = isset( $attributes['imageAlt'] ) && is_string( $attributes['imageAlt'] ) ? $attributes['imageAlt'] : '';
$sm_mark      = ! empty( $attributes['showQuoteMark'] );

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-testimonial sm-testimonial--align-' . $sm_align )
);
?>
<figure <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php if ( $sm_mark ) : ?>
		<span class="sm-testimonial__mark">
			<?php
			echo suitemart_get_icon( 'quote', array( 'size' => 28 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
			?>
		</span>
	<?php endif; ?>

	<?php if ( 0 !== $sm_rating ) : ?>
		<?php
		/*
		 * The stars are decoration; the rating is stated in text that is
		 * visually hidden. Repeating five icon labels would have a screen
		 * reader announce "star star star star star" instead of "4 out of 5".
		 */
		?>
		<p class="sm-testimonial__rating">
			<span class="sm-testimonial__stars" aria-hidden="true">
				<?php for ( $sm_i = 1; $sm_i <= 5; $sm_i++ ) : ?>
					<span class="sm-testimonial__star<?php echo $sm_i <= $sm_rating ? ' is-filled' : ''; ?>">
						<?php
						echo suitemart_get_icon( 'star', array( 'size' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
						?>
					</span>
				<?php endfor; ?>
			</span>
			<span class="sm-testimonial__rating-text">
				<?php
				printf(
					/* translators: 1: given rating, 2: maximum rating. */
					esc_html__( 'Rated %1$s out of %2$s', 'suitemart' ),
					esc_html( number_format_i18n( $sm_rating ) ),
					esc_html( number_format_i18n( 5 ) )
				);
				?>
			</span>
		</p>
	<?php endif; ?>

	<blockquote class="sm-testimonial__quote">
		<p><?php echo esc_html( $sm_quote ); ?></p>
	</blockquote>

	<?php if ( '' !== $sm_author || '' !== $sm_image_url || 0 !== $sm_image_id ) : ?>
		<figcaption class="sm-testimonial__attribution">
			<?php if ( 0 !== $sm_image_id ) : ?>
				<?php
				echo wp_get_attachment_image(
					$sm_image_id,
					'thumbnail',
					false,
					array(
						'class'   => 'sm-testimonial__avatar',
						'alt'     => $sm_image_alt,
						'loading' => 'lazy',
					)
				);
				?>
			<?php elseif ( '' !== $sm_image_url ) : ?>
				<img class="sm-testimonial__avatar" src="<?php echo esc_url( $sm_image_url ); ?>" alt="<?php echo esc_attr( $sm_image_alt ); ?>" loading="lazy" decoding="async" />
			<?php endif; ?>

			<span class="sm-testimonial__who">
				<?php if ( '' !== $sm_author ) : ?>
					<span class="sm-testimonial__author"><?php echo esc_html( $sm_author ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $sm_role ) : ?>
					<span class="sm-testimonial__role"><?php echo esc_html( $sm_role ); ?></span>
				<?php endif; ?>
			</span>
		</figcaption>
	<?php endif; ?>
</figure>
