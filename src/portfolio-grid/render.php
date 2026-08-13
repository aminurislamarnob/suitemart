<?php
/**
 * Portfolio grid block.
 *
 * A CSS grid of projects with category filters. Every project the query matched
 * is in the page from the start, and filtering hides the ones that do not match
 * — no Isotope, no absolute positioning, no second request (decision 8). That
 * choice is what makes the block work with JavaScript switched off: without it
 * the filters are simply absent and the whole grid is shown, which is the right
 * answer rather than a broken one.
 *
 * The filter state lives in one context on the root, and each project's
 * visibility is derived from it — declared twice, in PHP here and again in
 * `view.js`, because a binding that depends on the element's own context has to
 * resolve on the server too. An unresolved directive does not leave the markup
 * alone; it deletes the attribute it was written beside.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup (unused).
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( ! post_type_exists( 'portfolio' ) ) {
	return '';
}

$sm_count    = suitemart_clamp_int( $attributes['postsToShow'] ?? 12, 12, 1, 48 );
$sm_columns  = suitemart_clamp_int( $attributes['columns'] ?? 3, 3, 1, 6 );
$sm_level    = suitemart_clamp_int( $attributes['headingLevel'] ?? 3, 3, 2, 6 );
$sm_order    = 'asc' === ( $attributes['order'] ?? 'desc' ) ? 'ASC' : 'DESC';
$sm_filters  = ! isset( $attributes['showFilters'] ) || (bool) $attributes['showFilters'];
$sm_excerpt  = isset( $attributes['showExcerpt'] ) && (bool) $attributes['showExcerpt'];
$sm_order_by = suitemart_enum(
	$attributes['orderBy'] ?? 'date',
	array( 'date', 'title', 'menu_order', 'rand' ),
	'date'
);

$sm_query_args = array(
	'post_type'      => 'portfolio',
	'post_status'    => 'publish',
	'posts_per_page' => $sm_count,
	'orderby'        => $sm_order_by,
	'order'          => $sm_order,
	'no_found_rows'  => true,
);

$sm_terms = array();

if ( isset( $attributes['terms'] ) && is_array( $attributes['terms'] ) ) {
	$sm_terms = array_values( array_filter( array_map( 'absint', $attributes['terms'] ) ) );
}

if ( array() !== $sm_terms ) {
	$sm_query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Narrowing the grid is the point of the control.
		array(
			'taxonomy' => 'project-cat',
			'field'    => 'term_id',
			'terms'    => $sm_terms,
		),
	);
}

$sm_projects = new WP_Query( $sm_query_args );

if ( ! $sm_projects->have_posts() ) {
	return '';
}

/*
 * The filter bar is built from the categories the shown projects are actually
 * in, not from every category that exists. A filter that empties the grid is a
 * dead control, and the ones that would do that are exactly the categories
 * whose projects fell outside `postsToShow`.
 */
$sm_used   = array();
$sm_labels = array();
$sm_cards  = array();

while ( $sm_projects->have_posts() ) {
	$sm_projects->the_post();

	$sm_slugs = array();

	foreach ( (array) get_the_terms( get_the_ID(), 'project-cat' ) as $sm_term ) {
		if ( ! $sm_term instanceof WP_Term ) {
			continue;
		}

		$sm_slugs[]                  = $sm_term->slug;
		$sm_labels[ $sm_term->slug ] = $sm_term->name;
		$sm_used[ $sm_term->slug ]   = ( $sm_used[ $sm_term->slug ] ?? 0 ) + 1;
	}

	$sm_cards[] = array(
		'id'      => (int) get_the_ID(),
		'title'   => get_the_title(),
		'link'    => (string) get_permalink(),
		'thumb'   => (int) get_post_thumbnail_id(),
		'excerpt' => $sm_excerpt ? wp_trim_words( get_the_excerpt(), 18 ) : '',
		'terms'   => $sm_slugs,
	);
}

wp_reset_postdata();

$sm_label = isset( $attributes['label'] ) && is_string( $attributes['label'] ) && '' !== trim( $attributes['label'] )
	? trim( $attributes['label'] )
	: __( 'Portfolio', 'suitemart' );

$sm_all_label = __( 'All', 'suitemart' );

/*
 * Two halves of the same logic. The PHP closures decide what the page is served
 * as; view.js decides what it becomes. They have to agree, or the grid changes
 * the moment the module loads.
 */
wp_interactivity_state(
	'suitemart/portfolio-grid',
	array(
		'isHidden'  => static function (): bool {
			$sm_ctx    = wp_interactivity_get_context();
			$sm_active = $sm_ctx['active'] ?? '';

			if ( '' === $sm_active ) {
				return false;
			}

			return ! in_array( $sm_active, (array) ( $sm_ctx['terms'] ?? array() ), true );
		},
		'isPressed' => static function (): bool {
			$sm_ctx = wp_interactivity_get_context();

			return ( $sm_ctx['slug'] ?? '' ) === ( $sm_ctx['active'] ?? '' );
		},
		'status'    => static function (): string {
			$sm_ctx    = wp_interactivity_get_context();
			$sm_active = $sm_ctx['active'] ?? '';

			if ( '' === $sm_active ) {
				return (string) ( $sm_ctx['showingAll'] ?? '' );
			}

			return sprintf(
				(string) ( $sm_ctx['showingOne'] ?? '%s' ),
				(string) ( ( $sm_ctx['labels'] ?? array() )[ $sm_active ] ?? $sm_active )
			);
		},
	)
);

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'sm-portfolio-grid',
		'style' => sprintf( '--sm-portfolio-columns:%d;', $sm_columns ),
	)
);

$sm_context = array(
	'active'     => '',
	'labels'     => $sm_labels,
	// The two announcements, translated here because the browser has no text
	// domain to look them up in.
	'showingAll' => __( 'Showing all projects', 'suitemart' ),
	/* translators: %s: Project category name. */
	'showingOne' => __( 'Showing %s', 'suitemart' ),
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	role="group"
	aria-label="<?php echo esc_attr( $sm_label ); ?>"
	data-wp-interactive="suitemart/portfolio-grid"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
>
	<?php if ( $sm_filters && count( $sm_used ) > 1 ) : ?>
		<?php
		/*
		 * One click handler on the bar rather than one per button. The buttons
		 * carry a context of their own so each can work out whether it is the
		 * pressed one, and a write from inside a child context would land in
		 * that child rather than on the grid's `active`. The bar has no context
		 * of its own, so its handler writes where the whole grid can see it.
		 */
		?>
		<div
			class="sm-portfolio-grid__filters"
			role="group"
			aria-label="<?php esc_attr_e( 'Filter by category', 'suitemart' ); ?>"
			data-wp-on--click="actions.filter"
		>
			<button
				type="button"
				class="sm-portfolio-grid__filter"
				data-slug=""
				aria-pressed="true"
				<?php echo wp_interactivity_data_wp_context( array( 'slug' => '' ) ); ?>
				data-wp-bind--aria-pressed="state.isPressed"
			>
				<?php echo esc_html( $sm_all_label ); ?>
			</button>

			<?php foreach ( $sm_used as $sm_slug => $sm_total ) : ?>
				<button
					type="button"
					class="sm-portfolio-grid__filter"
					data-slug="<?php echo esc_attr( $sm_slug ); ?>"
					aria-pressed="false"
					<?php echo wp_interactivity_data_wp_context( array( 'slug' => $sm_slug ) ); ?>
					data-wp-bind--aria-pressed="state.isPressed"
				>
					<?php echo esc_html( $sm_labels[ $sm_slug ] ); ?>
					<span class="sm-portfolio-grid__count"><?php echo esc_html( (string) $sm_total ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<p class="screen-reader-text" aria-live="polite" data-wp-text="state.status">
			<?php echo esc_html( $sm_context['showingAll'] ); ?>
		</p>
	<?php endif; ?>

	<div class="sm-portfolio-grid__items">
		<?php foreach ( $sm_cards as $sm_card ) : ?>
			<article
				class="sm-portfolio-grid__item"
				<?php echo wp_interactivity_data_wp_context( array( 'terms' => $sm_card['terms'] ) ); ?>
				data-wp-bind--hidden="state.isHidden"
			>
				<?php if ( $sm_card['thumb'] > 0 ) : ?>
					<a class="sm-portfolio-grid__media" href="<?php echo esc_url( $sm_card['link'] ); ?>" tabindex="-1" aria-hidden="true">
						<?php
						// Hidden from assistive technology and out of the tab
						// order: the title below it is the same link.
						echo suitemart_block_image( $sm_card['thumb'], '', '', 'sm-portfolio-grid__image', 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
						?>
					</a>
				<?php endif; ?>

				<h<?php echo (int) $sm_level; ?> class="sm-portfolio-grid__title">
					<a class="sm-portfolio-grid__link" href="<?php echo esc_url( $sm_card['link'] ); ?>">
						<?php echo esc_html( $sm_card['title'] ); ?>
					</a>
				</h<?php echo (int) $sm_level; ?>>

				<?php if ( '' !== $sm_card['excerpt'] ) : ?>
					<p class="sm-portfolio-grid__excerpt"><?php echo esc_html( $sm_card['excerpt'] ); ?></p>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</div>
