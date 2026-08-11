<?php
/**
 * Live search block.
 *
 * A real `<form>` pointed at the site's search URL, so submitting it works with
 * JavaScript disabled or still loading. The suggestion list is an enhancement
 * layered on top, following the ARIA combobox pattern.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_post_type = isset( $attributes['postType'] ) && is_string( $attributes['postType'] )
	? sanitize_key( $attributes['postType'] )
	: 'any';

// Only offer post types that are actually searchable and REST-enabled;
// anything else would return nothing and look broken.
$sm_allowed = array( 'any', 'post', 'page' );

if ( suitemart_has_woocommerce() ) {
	$sm_allowed[] = 'product';
}

if ( ! in_array( $sm_post_type, $sm_allowed, true ) ) {
	$sm_post_type = 'any';
}

$sm_limit = suitemart_clamp_int( $attributes['resultLimit'] ?? 6, 6, 1, 20 );

$sm_placeholder = isset( $attributes['placeholder'] ) && is_string( $attributes['placeholder'] ) && '' !== $attributes['placeholder']
	? $attributes['placeholder']
	: __( 'Search…', 'suitemart' );

$sm_button_text = isset( $attributes['buttonText'] ) && is_string( $attributes['buttonText'] ) && '' !== $attributes['buttonText']
	? $attributes['buttonText']
	: __( 'Search', 'suitemart' );

$sm_show_images = ! isset( $attributes['showImages'] ) || (bool) $attributes['showImages'];

$sm_id        = wp_unique_id( 'sm-search-' );
$sm_input_id  = $sm_id . '-input';
$sm_list_id   = $sm_id . '-results';
$sm_status_id = $sm_id . '-status';

$sm_wrapper = get_block_wrapper_attributes( array( 'class' => 'sm-search' ) );

$sm_context = array(
	'query'       => '',
	'results'     => array(),
	'isOpen'      => false,
	'isLoading'   => false,
	'activeIndex' => -1,
	'searchUrl'   => rest_url( 'wp/v2/search' ),
	'postType'    => $sm_post_type,
	'limit'       => $sm_limit,
	'listId'      => $sm_list_id,
	'showImages'  => $sm_show_images,
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/ajax-search"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
	data-wp-on-document--click="actions.handleDocumentClick"
	data-wp-class--is-open="context.isOpen"
>
	<form class="sm-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="<?php echo esc_attr( $sm_input_id ); ?>">
			<?php esc_html_e( 'Search for', 'suitemart' ); ?>
		</label>

		<?php
		/*
		 * Combobox wiring: the input owns the listbox through aria-controls,
		 * reports whether it is open, and points at the highlighted option with
		 * aria-activedescendant. Focus never leaves the input, which is what
		 * lets the user keep typing while arrowing through results.
		 */
		?>
		<input
			class="sm-search__input"
			id="<?php echo esc_attr( $sm_input_id ); ?>"
			type="search"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php echo esc_attr( $sm_placeholder ); ?>"
			autocomplete="off"
			role="combobox"
			aria-expanded="false"
			aria-controls="<?php echo esc_attr( $sm_list_id ); ?>"
			aria-autocomplete="list"
			data-wp-bind--aria-expanded="context.isOpen"
			data-wp-bind--aria-activedescendant="state.activeOptionId"
			data-wp-on--input="actions.handleInput"
			data-wp-on--keydown="actions.handleKeydown"
			data-wp-on--focus="actions.handleFocus"
		/>

		<?php if ( 'any' !== $sm_post_type ) : ?>
			<input type="hidden" name="post_type" value="<?php echo esc_attr( $sm_post_type ); ?>" />
		<?php endif; ?>

		<button class="sm-search__submit" type="submit">
			<?php echo suitemart_get_icon( 'search', array( 'label' => $sm_button_text ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		</button>
	</form>

	<div class="sm-search__panel" data-wp-bind--hidden="!context.isOpen" hidden>
		<ul
			class="sm-search__results"
			id="<?php echo esc_attr( $sm_list_id ); ?>"
			role="listbox"
			aria-label="<?php esc_attr_e( 'Search suggestions', 'suitemart' ); ?>"
		>
			<template data-wp-each--result="context.results">
				<li
					class="sm-search__result"
					role="option"
					data-wp-bind--id="state.optionId"
					data-wp-bind--aria-selected="state.isActiveOption"
					data-wp-class--is-active="state.isActiveOption"
					data-wp-on--mouseenter="actions.hoverOption"
				>
					<a class="sm-search__result-link" data-wp-bind--href="context.result.url" tabindex="-1">
						<?php if ( $sm_show_images ) : ?>
							<img
								class="sm-search__result-image"
								data-wp-bind--src="context.result.image"
								data-wp-bind--hidden="!context.result.image"
								alt=""
								loading="lazy"
								decoding="async"
							/>
						<?php endif; ?>
						<span class="sm-search__result-title" data-wp-text="context.result.title"></span>
					</a>
				</li>
			</template>
		</ul>

		<p class="sm-search__empty" data-wp-bind--hidden="!state.isEmpty" hidden>
			<?php esc_html_e( 'No matches found.', 'suitemart' ); ?>
		</p>
	</div>

	<?php
	/*
	 * Result counts are announced politely rather than by moving focus, so a
	 * screen reader user hears how many suggestions appeared without being
	 * interrupted mid-word.
	 */
	?>
	<p
		class="screen-reader-text"
		id="<?php echo esc_attr( $sm_status_id ); ?>"
		role="status"
		aria-live="polite"
		data-wp-text="state.statusMessage"
	></p>
</div>
