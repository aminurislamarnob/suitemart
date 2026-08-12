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

/*
 * Nothing is open on first paint, so the server can seed a plain false. The
 * view module replaces `isOpen` with a getter that compares this instance's id
 * against the one open modal — see the id note below.
 */
wp_interactivity_state(
	'suitemart/size-guide',
	array(
		'openId' => '',
		'isOpen' => false,
	)
);

$sm_title = isset( $attributes['title'] ) ? $attributes['title'] : '';

/*
 * The modal and its button are separate blocks with no shared ancestor, so they
 * agree on an id derived from the post they both sit inside rather than a
 * generated one. This is what keeps a product grid working: every card gets its
 * own id, so ids stay unique and one click opens one modal. It does mean a
 * single post supports a single size guide, which is the only sensible reading
 * of the block anyway.
 */
$sm_post_id  = suitemart_clamp_int( $block->context['postId'] ?? get_the_ID(), 0, 0, PHP_INT_MAX );
$sm_modal_id = suitemart_size_guide_id( $sm_post_id, 'modal' );
$sm_title_id = $sm_modal_id . '-title';

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-size-guide' )
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/size-guide"
	<?php echo wp_interactivity_data_wp_context( array( 'modalId' => $sm_modal_id ) ); ?>
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
		aria-labelledby="<?php echo esc_attr( $sm_title_id ); ?>"
		id="<?php echo esc_attr( $sm_modal_id ); ?>"
		data-wp-bind--inert="!state.isOpen"
	>
		<div class="sm-size-guide__header">
			<?php if ( '' !== $sm_title ) : ?>
				<h2 id="<?php echo esc_attr( $sm_title_id ); ?>" class="sm-size-guide__title"><?php echo esc_html( $sm_title ); ?></h2>
			<?php else : ?>
				<h2 id="<?php echo esc_attr( $sm_title_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'Size guide', 'suitemart' ); ?></h2>
			<?php endif; ?>
			
			<button class="sm-size-guide__close" type="button" data-wp-on--click="actions.close" aria-label="<?php esc_attr_e( 'Close size guide', 'suitemart' ); ?>">
				<?php echo suitemart_get_icon( 'x', array( 'size' => 24 ) ); ?>
			</button>
		</div>
		
		<div class="sm-size-guide__content">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Innerblocks content. ?>
		</div>
	</div>
</div>
