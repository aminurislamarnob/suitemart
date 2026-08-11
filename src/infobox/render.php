<?php
/**
 * Info box block.
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
$sm_url         = isset( $attributes['url'] ) && is_string( $attributes['url'] ) ? $attributes['url'] : '';
$sm_icon        = isset( $attributes['icon'] ) && is_string( $attributes['icon'] ) ? sanitize_key( $attributes['icon'] ) : '';

if ( '' === $sm_title && '' === $sm_description ) {
	return '';
}

$sm_level       = suitemart_clamp_int( $attributes['titleLevel'] ?? 3, 3, 2, 6 );
$sm_icon_size   = suitemart_clamp_int( $attributes['iconSize'] ?? 32, 32, 12, 128 );
$sm_orientation = suitemart_enum( $attributes['orientation'] ?? 'vertical', array( 'vertical', 'horizontal' ), 'vertical' );
$sm_alignment   = suitemart_enum( $attributes['alignment'] ?? 'start', array( 'start', 'center', 'end' ), 'start' );

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => sprintf(
			'sm-infobox sm-infobox--%s sm-infobox--align-%s',
			$sm_orientation,
			$sm_alignment
		),
	)
);
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<?php if ( '' !== $sm_icon ) : ?>
		<div class="sm-infobox__icon" style="font-size:<?php echo esc_attr( (string) $sm_icon_size ); ?>px">
			<?php
			// Decorative: the heading beside it already names the item, so an
			// accessible name here would just be read twice.
			echo suitemart_get_icon( $sm_icon, array( 'size' => $sm_icon_size ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
			?>
		</div>
	<?php endif; ?>

	<div class="sm-infobox__body">
		<?php if ( '' !== $sm_title ) : ?>
			<?php printf( '<h%d class="sm-infobox__title">', (int) $sm_level ); ?>
				<?php if ( '' !== $sm_url ) : ?>
					<a class="sm-infobox__link" href="<?php echo esc_url( $sm_url ); ?>"><?php echo esc_html( $sm_title ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $sm_title ); ?>
				<?php endif; ?>
			<?php printf( '</h%d>', (int) $sm_level ); ?>
		<?php endif; ?>

		<?php if ( '' !== $sm_description ) : ?>
			<p class="sm-infobox__description"><?php echo esc_html( $sm_description ); ?></p>
		<?php endif; ?>
	</div>
</div>
