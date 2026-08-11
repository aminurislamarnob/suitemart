<?php
/**
 * Price list item block.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_title       = isset( $attributes['title'] ) && is_string( $attributes['title'] ) ? $attributes['title'] : '';
$sm_description = isset( $attributes['description'] ) && is_string( $attributes['description'] ) ? $attributes['description'] : '';
$sm_price       = isset( $attributes['price'] ) && is_string( $attributes['price'] ) ? $attributes['price'] : '';
$sm_old_price   = isset( $attributes['oldPrice'] ) && is_string( $attributes['oldPrice'] ) ? $attributes['oldPrice'] : '';
$sm_badge       = isset( $attributes['badge'] ) && is_string( $attributes['badge'] ) ? $attributes['badge'] : '';
$sm_url         = isset( $attributes['url'] ) && is_string( $attributes['url'] ) ? $attributes['url'] : '';
$sm_leader      = ! empty( $attributes['showLeader'] );

if ( '' === $sm_title && '' === $sm_price ) {
	return '';
}

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-menu-price' . ( $sm_leader ? ' sm-menu-price--leader' : '' ) )
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<div class="sm-menu-price__head">
		<p class="sm-menu-price__title">
			<?php if ( '' !== $sm_url ) : ?>
				<a class="sm-menu-price__link" href="<?php echo esc_url( $sm_url ); ?>"><?php echo esc_html( $sm_title ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $sm_title ); ?>
			<?php endif; ?>

			<?php if ( '' !== $sm_badge ) : ?>
				<span class="sm-menu-price__badge"><?php echo esc_html( $sm_badge ); ?></span>
			<?php endif; ?>
		</p>

		<?php if ( $sm_leader ) : ?>
			<span class="sm-menu-price__leader" aria-hidden="true"></span>
		<?php endif; ?>

		<p class="sm-menu-price__price">
			<?php if ( '' !== $sm_old_price ) : ?>
				<?php
				/*
				 * <del> rather than a struck-through <span>: the mark-up itself
				 * has to say the old price no longer applies, because a line
				 * drawn only in CSS says nothing to a screen reader.
				 */
				?>
				<del class="sm-menu-price__old"><?php echo esc_html( $sm_old_price ); ?></del>
			<?php endif; ?>
			<span class="sm-menu-price__amount"><?php echo esc_html( $sm_price ); ?></span>
		</p>
	</div>

	<?php if ( '' !== $sm_description ) : ?>
		<p class="sm-menu-price__description"><?php echo esc_html( $sm_description ); ?></p>
	<?php endif; ?>
</div>
