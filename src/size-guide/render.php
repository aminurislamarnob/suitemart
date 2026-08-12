<?php
/**
 * Size guide block.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// Seed state.
wp_interactivity_state( 'suitemart/size-guide', array( 'isOpen' => false ) );

$sm_title = isset( $attributes['title'] ) ? $attributes['title'] : '';

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-size-guide' )
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/size-guide"
	data-wp-class--is-open="state.isOpen"
	data-wp-on-document--keydown="actions.handleKeydown"
	data-wp-watch="callbacks.onToggle"
>
	<div
		class="sm-size-guide__backdrop"
		data-wp-on--click="actions.close"
		aria-hidden="true"
	></div>
	
	<div
		class="sm-size-guide__dialog"
		role="dialog"
		aria-modal="true"
		aria-labelledby="sm-size-guide-title"
		id="sm-size-guide-modal"
		data-wp-bind--inert="!state.isOpen"
	>
		<div class="sm-size-guide__header">
			<?php if ( '' !== $sm_title ) : ?>
				<h2 id="sm-size-guide-title" class="sm-size-guide__title"><?php echo esc_html( $sm_title ); ?></h2>
			<?php else : ?>
				<h2 id="sm-size-guide-title" class="screen-reader-text"><?php esc_html_e( 'Size guide', 'suitemart' ); ?></h2>
			<?php endif; ?>
			
			<button class="sm-size-guide__close" type="button" data-wp-on--click="actions.close" aria-label="<?php esc_attr_e( 'Close size guide', 'suitemart' ); ?>">
				<?php suitemart_get_icon( 'x', array( 'size' => 24 ) ); ?>
			</button>
		</div>
		
		<div class="sm-size-guide__content">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Innerblocks content. ?>
		</div>
	</div>
</div>
