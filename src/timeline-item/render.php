<?php
/**
 * Timeline entry block.
 *
 * Renders an `<li>` because its parent renders an `<ol>`. That pairing is the
 * reason this block declares a parent rather than being placeable anywhere.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_date  = isset( $attributes['date'] ) && is_string( $attributes['date'] ) ? $attributes['date'] : '';
$sm_title = isset( $attributes['title'] ) && is_string( $attributes['title'] ) ? $attributes['title'] : '';
$sm_level = suitemart_clamp_int( $attributes['titleLevel'] ?? 3, 3, 2, 6 );

$sm_classes = 'sm-timeline__item';

if ( ! empty( $attributes['isComplete'] ) ) {
	$sm_classes .= ' is-complete';
}

$sm_wrapper = get_block_wrapper_attributes( array( 'class' => $sm_classes ) );
?>
<li <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php // The dot is drawn entirely in CSS; the list position is what carries the meaning. ?>
	<span class="sm-timeline__dot" aria-hidden="true"></span>

	<?php if ( '' !== $sm_date ) : ?>
		<p class="sm-timeline__date"><?php echo esc_html( $sm_date ); ?></p>
	<?php endif; ?>

	<?php if ( '' !== $sm_title ) : ?>
		<?php printf( '<h%d class="sm-timeline__title">%s</h%d>', (int) $sm_level, esc_html( $sm_title ), (int) $sm_level ); ?>
	<?php endif; ?>

	<?php if ( '' !== trim( $content ) ) : ?>
		<div class="sm-timeline__body">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
		</div>
	<?php endif; ?>
</li>
