<?php
/**
 * Wishlist button block.
 *
 * The wishlist lives in the visitor's browser, so the server genuinely cannot
 * know whether this product is on it. The button is therefore rendered in the
 * "not saved" state and corrected on hydration.
 *
 * That is a deliberate trade rather than an oversight. Storing the list
 * server-side would mean a cookie, and a cookie that varies per visitor stops
 * a full-page cache serving one copy of a page to everybody — which the
 * caching integrations in P4 depend on. It would also mean recording something
 * about every guest.
 *
 * `aria-pressed` carries the state, so a screen reader is told the button is a
 * toggle and hears the correction when it happens.
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

// Outside a product context there is nothing to save.
if ( 0 === $sm_product_id || 'product' !== get_post_type( $sm_product_id ) ) {
	return '';
}

$sm_appearance = suitemart_enum( $attributes['appearance'] ?? 'icon-label', array( 'icon', 'icon-label' ), 'icon-label' );
$sm_icon_size  = suitemart_clamp_int( $attributes['iconSize'] ?? 20, 20, 12, 48 );

/*
 * Both labels are seeded so the binding resolves on the server as well as in
 * the browser. A `data-wp-bind` whose expression the server cannot resolve
 * strips the attribute outright, which would leave this button unnamed.
 */
wp_interactivity_state(
	'suitemart/wishlist',
	array(
		'addLabel'          => __( 'Add to wishlist', 'suitemart' ),
		'removeLabel'       => __( 'Remove from wishlist', 'suitemart' ),
		'addedNotice'       => __( 'Added to your wishlist', 'suitemart' ),
		'removedNotice'     => __( 'Removed from your wishlist', 'suitemart' ),
		// Shown when the browser refuses to store anything, which is the one
		// case where the button cannot do what it offers.
		'unavailableNotice' => __( 'Your browser is blocking site storage, so the wishlist cannot be saved', 'suitemart' ),
	)
);

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-wishlist-button sm-wishlist-button--' . $sm_appearance )
);
?>
<button
	type="button"
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/wishlist"
	<?php
	echo wp_interactivity_data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
		array(
			'productId' => $sm_product_id,
			// Rendered as "not saved" because the server cannot know better.
			// view.js corrects it as soon as it reads localStorage.
			'isSaved'   => false,
			'label'     => __( 'Add to wishlist', 'suitemart' ),
		)
	);
	?>
	data-wp-init="callbacks.sync"
	data-wp-on--click="actions.toggle"
	data-wp-class--is-saved="context.isSaved"
	data-wp-bind--aria-pressed="context.isSaved"
	data-wp-bind--aria-label="context.label"
	aria-pressed="false"
	aria-label="<?php echo esc_attr__( 'Add to wishlist', 'suitemart' ); ?>"
>
	<span class="sm-wishlist-button__icon">
		<?php echo suitemart_get_icon( 'heart', array( 'size' => $sm_icon_size ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
	</span>
	<?php if ( 'icon-label' === $sm_appearance ) : ?>
		<span class="sm-wishlist-button__label" data-wp-text="context.label">
			<?php esc_html_e( 'Add to wishlist', 'suitemart' ); ?>
		</span>
	<?php endif; ?>
</button>
