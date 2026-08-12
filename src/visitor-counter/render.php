<?php
/**
 * Visitor counter block.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_min_visitors = suitemart_clamp_int( $attributes['minVisitors'] ?? 20, 20, 0, 1000 );
$sm_max_visitors = suitemart_clamp_int( $attributes['maxVisitors'] ?? 50, 50, $sm_min_visitors, 1000 );

$sm_product_id = suitemart_clamp_int( $block->context['postId'] ?? get_the_ID(), 0, 0, PHP_INT_MAX );

/**
 * Filters the number of people currently viewing this product.
 *
 * Out of the box this block has no analytics source, so it simulates a figure
 * inside the configured range. That is a fabricated claim: several
 * jurisdictions treat invented social proof as a deceptive commercial practice,
 * and shops selling into them should either connect a real source through this
 * filter or leave the block out of their templates.
 *
 * Return an integer to report a measured count, or null to keep the simulation.
 *
 * @since 1.0.0
 *
 * @param int|null $count      Measured viewer count, or null when unmeasured.
 * @param int      $product_id Product being viewed.
 */
$sm_measured = apply_filters( 'suitemart_visitor_count', null, $sm_product_id );

$sm_simulated = null === $sm_measured;
$sm_initial   = $sm_simulated
	? wp_rand( $sm_min_visitors, $sm_max_visitors )
	: suitemart_clamp_int( $sm_measured, 0, 0, PHP_INT_MAX );

$sm_context = array(
	'min'       => $sm_min_visitors,
	'max'       => $sm_max_visitors,
	'current'   => $sm_initial,
	// Only the simulated figure drifts. A measured one is left exactly as the
	// data source reported it.
	'simulated' => $sm_simulated,
);

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-visitor-counter' )
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/visitor-counter"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
	data-wp-init="callbacks.start"
>
	<?php echo suitemart_get_icon( 'users', array( 'size' => 20 ) ); ?>
	<span>
		<?php
		/*
		 * The <strong> is supplied as an argument rather than written into the
		 * translatable string. Inside it, a translator can reword the directive
		 * away — and wp_kses would then strip the attribute, leaving a number
		 * that never updates.
		 */
		echo wp_kses(
			sprintf(
				/* translators: %s: number of people viewing, already wrapped in a <strong> element. */
				_n(
					'%s person is viewing this right now.',
					'%s people are viewing this right now.',
					$sm_initial,
					'suitemart'
				),
				sprintf(
					'<strong data-wp-text="context.current">%s</strong>',
					esc_html( number_format_i18n( $sm_initial ) )
				)
			),
			array(
				'strong' => array( 'data-wp-text' => true ),
			)
		);
		?>
	</span>
</div>
