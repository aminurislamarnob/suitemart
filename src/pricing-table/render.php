<?php
/**
 * Pricing plan block.
 *
 * One plan, not a whole table: plans go side by side in a Columns block, which
 * already handles responsive stacking and per-column widths. The feature list
 * and the button are inner blocks so an editor can use core's list and button
 * controls rather than a bespoke repeater.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_plan     = isset( $attributes['planName'] ) && is_string( $attributes['planName'] ) ? $attributes['planName'] : '';
$sm_currency = isset( $attributes['currency'] ) && is_string( $attributes['currency'] ) ? $attributes['currency'] : '';
$sm_price    = isset( $attributes['price'] ) && is_string( $attributes['price'] ) ? $attributes['price'] : '';
$sm_period   = isset( $attributes['period'] ) && is_string( $attributes['period'] ) ? $attributes['period'] : '';
$sm_summary  = isset( $attributes['summary'] ) && is_string( $attributes['summary'] ) ? $attributes['summary'] : '';
$sm_badge    = isset( $attributes['badge'] ) && is_string( $attributes['badge'] ) ? $attributes['badge'] : '';

if ( '' === $sm_plan && '' === $sm_price ) {
	return '';
}

$sm_level    = suitemart_clamp_int( $attributes['planLevel'] ?? 3, 3, 2, 6 );
$sm_featured = ! empty( $attributes['featured'] );

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-pricing' . ( $sm_featured ? ' sm-pricing--featured' : '' ) )
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php if ( '' !== $sm_badge ) : ?>
		<p class="sm-pricing__badge"><?php echo esc_html( $sm_badge ); ?></p>
	<?php endif; ?>

	<?php if ( '' !== $sm_plan ) : ?>
		<?php printf( '<h%d class="sm-pricing__name">%s</h%d>', (int) $sm_level, esc_html( $sm_plan ), (int) $sm_level ); ?>
	<?php endif; ?>

	<?php if ( '' !== $sm_price ) : ?>
		<p class="sm-pricing__price">
			<?php if ( '' !== $sm_currency ) : ?>
				<span class="sm-pricing__currency"><?php echo esc_html( $sm_currency ); ?></span>
			<?php endif; ?>
			<span class="sm-pricing__amount"><?php echo esc_html( $sm_price ); ?></span>
			<?php if ( '' !== $sm_period ) : ?>
				<span class="sm-pricing__period"><?php echo esc_html( $sm_period ); ?></span>
			<?php endif; ?>
		</p>
	<?php endif; ?>

	<?php if ( '' !== $sm_summary ) : ?>
		<p class="sm-pricing__summary"><?php echo esc_html( $sm_summary ); ?></p>
	<?php endif; ?>

	<div class="sm-pricing__body">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
	</div>
</div>
