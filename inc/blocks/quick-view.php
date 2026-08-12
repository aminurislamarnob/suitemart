<?php
/**
 * The shared quick view modal.
 *
 * One modal serves every Quick View button on a page: the buttons carry only a
 * product id, and the modal fetches that product from the Store API when it
 * opens. Keeping it here rather than in the block's render.php means the global
 * function is declared in a file where phpcs still checks global prefixes, and
 * that it is hooked exactly once however many buttons render.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Queues the shared modal for the footer, once per request.
 *
 * @return void
 */
function suitemart_register_quick_view_modal(): void {
	static $registered = false;

	if ( $registered ) {
		return;
	}

	$registered = true;

	add_action( 'wp_footer', 'suitemart_render_quick_view_modal' );
}

/**
 * Prints the shared quick view modal.
 *
 * @return void
 */
function suitemart_render_quick_view_modal(): void {
	?>
	<div
		class="sm-quick-view-modal"
		data-wp-interactive="suitemart/quick-view"
		data-wp-class--is-open="state.isOpen"
		data-wp-on-document--keydown="actions.handleKeydown"
		data-wp-watch="callbacks.onToggle"
	>
		<div class="sm-quick-view-modal__backdrop" data-wp-on--click="actions.close" aria-hidden="true"></div>
		
		<div class="sm-quick-view-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="sm-quick-view-title" id="sm-quick-view-dialog" data-wp-bind--inert="!state.isOpen">
			<div class="sm-quick-view-modal__header">
				<h2 id="sm-quick-view-title" class="screen-reader-text"><?php esc_html_e( 'Quick view', 'suitemart' ); ?></h2>
				<button type="button" class="sm-quick-view-modal__close" data-wp-on--click="actions.close">
					<?php echo suitemart_get_icon( 'x', array( 'label' => __( 'Close quick view', 'suitemart' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
			
			<div class="sm-quick-view-modal__body">
				<!-- Loading state -->
				<div class="sm-quick-view-modal__loading" data-wp-bind--hidden="!state.isLoading">
					<span class="sm-quick-view-modal__spinner" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Loading product details...', 'suitemart' ); ?></span>
				</div>

				<!-- Error state -->
				<div class="sm-quick-view-modal__error" data-wp-bind--hidden="!state.statusText">
					<p data-wp-text="state.statusText"></p>
				</div>

				<!-- Product content -->
				<div class="sm-quick-view-modal__content" data-wp-bind--hidden="!state.product">
					<div class="sm-quick-view-modal__gallery">
						<img data-wp-bind--src="state.product.image" data-wp-bind--alt="state.product.imageAlt" />
					</div>
					<div class="sm-quick-view-modal__summary">
						<h3 class="sm-quick-view-modal__product-title" data-wp-text="state.product.name"></h3>
						<p class="sm-quick-view-modal__price" data-wp-text="state.product.price"></p>
						<div class="sm-quick-view-modal__description" data-wp-text="state.product.shortDescription"></div>
						
						<div class="sm-quick-view-modal__actions">
							<a class="sm-quick-view-modal__add-to-cart wp-element-button" data-wp-bind--href="state.product.addToCartUrl" data-wp-text="state.product.addToCartText">
								<?php esc_html_e( 'Add to cart', 'suitemart' ); ?>
							</a>
							<a class="sm-quick-view-modal__view-details" data-wp-bind--href="state.product.permalink">
								<?php esc_html_e( 'View details', 'suitemart' ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
