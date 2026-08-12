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

$sm_initial = wp_rand( $sm_min_visitors, $sm_max_visitors );

$sm_context = array(
	'min'     => $sm_min_visitors,
	'max'     => $sm_max_visitors,
	'current' => $sm_initial,
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
	<?php suitemart_get_icon( 'users', array( 'size' => 20 ) ); ?>
	<span>
		<?php
		echo wp_kses(
			sprintf(
				/* translators: %s: number of viewers. */
				__( '<strong data-wp-text="context.current">%s</strong> people are viewing this right now.', 'suitemart' ),
				(string) $sm_initial
			),
			array(
				'strong' => array( 'data-wp-text' => true ),
			)
		);
		?>
	</span>
</div>
