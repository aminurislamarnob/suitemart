<?php
/**
 * Compare button block.
 *
 * Shares the wishlist's storage model and the consequences that come with it:
 * the list lives in the visitor's browser, so no cookie is set, pages stay
 * cacheable, and the server renders every button in the "not added" state and
 * lets the browser correct it.
 *
 * What differs is the ceiling. A comparison table has a fixed number of
 * columns, so the list is capped — and adding past the cap drops the oldest
 * entry rather than refusing the click, because the reader has just asked for
 * this product and a dead control explains nothing.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_product_id = suitemart_clamp_int(
	$block->context['postId'] ?? get_the_ID(),
	0,
	0,
	PHP_INT_MAX
);

// Outside a product context there is nothing to compare.
if ( 0 === $sm_product_id || 'product' !== get_post_type( $sm_product_id ) ) {
	return '';
}

$sm_appearance = suitemart_enum( $attributes['appearance'] ?? 'icon-label', array( 'icon', 'icon-label' ), 'icon-label' );
$sm_icon_size  = suitemart_clamp_int( $attributes['iconSize'] ?? 20, 20, 12, 48 );

/*
 * Every label is seeded so the bindings resolve on the server as well as in
 * the browser. A `data-wp-bind` whose expression the server cannot resolve
 * strips the attribute outright, which would leave this button unnamed.
 */
wp_interactivity_state(
	'suitemart/compare',
	array(
		'addLabel'          => __( 'Add to compare', 'suitemart' ),
		'removeLabel'       => __( 'Remove from compare', 'suitemart' ),
		'addedNotice'       => __( 'Added to your comparison', 'suitemart' ),
		'removedNotice'     => __( 'Removed from your comparison', 'suitemart' ),
		/* translators: %d: maximum number of products that can be compared. */
		'evictedNotice'     => sprintf( __( 'Added. You can compare %d products at a time, so the first one was removed.', 'suitemart' ), suitemart_compare_limit() ),
		'unavailableNotice' => __( 'Your browser is blocking site storage, so the comparison cannot be saved', 'suitemart' ),
		'limit'             => suitemart_compare_limit(),
	)
);

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-compare-button sm-compare-button--' . $sm_appearance )
);
?>
<button
	type="button"
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/compare"
	<?php
	echo wp_interactivity_data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
		array(
			'productId' => $sm_product_id,
			// Rendered as "not added" because the server cannot know better.
			// view.js corrects it as soon as it reads localStorage.
			'isAdded'   => false,
			'label'     => __( 'Add to compare', 'suitemart' ),
		)
	);
	?>
	data-wp-init="callbacks.sync"
	data-wp-on--click="actions.toggle"
	data-wp-class--is-added="context.isAdded"
	data-wp-bind--aria-pressed="context.isAdded"
	data-wp-bind--aria-label="context.label"
	aria-pressed="false"
	aria-label="<?php echo esc_attr__( 'Add to compare', 'suitemart' ); ?>"
>
	<span class="sm-compare-button__icon">
		<?php echo suitemart_get_icon( 'shuffle', array( 'size' => $sm_icon_size ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
	</span>
	<?php if ( 'icon-label' === $sm_appearance ) : ?>
		<span class="sm-compare-button__label" data-wp-text="context.label">
			<?php esc_html_e( 'Add to compare', 'suitemart' ); ?>
		</span>
	<?php endif; ?>
</button>
