<?php
/**
 * Post carousel block.
 *
 * A carousel whose slides come from a query rather than from inner blocks. The
 * markup is the same shape as `suitemart/slider` — a scroll-snap row of cards
 * that works with no JavaScript, which Swiper then upgrades — and the Swiper
 * configuration is literally the same code, shared through
 * `src/_shared/carousel.js`.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup (unused).
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_post_type = suitemart_enum(
	$attributes['postType'] ?? 'post',
	array_values( get_post_types( array( 'public' => true ) ) ),
	'post'
);

$sm_count = suitemart_clamp_int( $attributes['postsToShow'] ?? 9, 9, 1, 24 );
$sm_order = 'asc' === ( $attributes['order'] ?? 'desc' ) ? 'ASC' : 'DESC';

$sm_order_by = suitemart_enum(
	$attributes['orderBy'] ?? 'date',
	array( 'date', 'title', 'menu_order', 'rand' ),
	'date'
);

$sm_query_args = array(
	'post_type'           => $sm_post_type,
	'post_status'         => 'publish',
	'posts_per_page'      => $sm_count,
	'orderby'             => $sm_order_by,
	'order'               => $sm_order,
	'ignore_sticky_posts' => true,
	// The carousel never paginates, so counting the rest of the archive is
	// work nobody reads.
	'no_found_rows'       => true,
);

$sm_taxonomy = isset( $attributes['taxonomy'] ) && is_string( $attributes['taxonomy'] )
	? sanitize_key( $attributes['taxonomy'] )
	: '';

$sm_terms = array();

if ( isset( $attributes['terms'] ) && is_array( $attributes['terms'] ) ) {
	$sm_terms = array_values( array_filter( array_map( 'absint', $attributes['terms'] ) ) );
}

if ( '' !== $sm_taxonomy && array() !== $sm_terms && taxonomy_exists( $sm_taxonomy ) ) {
	$sm_query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- A term filter is the point of the control.
		array(
			'taxonomy' => $sm_taxonomy,
			'field'    => 'term_id',
			'terms'    => $sm_terms,
		),
	);
}

$sm_posts = new WP_Query( $sm_query_args );

/*
 * Nothing at all rather than an empty carousel with working arrows. A query
 * that matches no posts is usually a term that was deleted or a post type with
 * nothing published yet, and a row of nav buttons over blank space says less
 * than the absence does.
 */
if ( ! $sm_posts->have_posts() ) {
	return '';
}

$sm_per_view = array(
	'mobile'  => suitemart_clamp_int( $attributes['slidesPerView'] ?? 1, 1, 1, 8 ),
	'tablet'  => suitemart_clamp_int( $attributes['slidesPerViewTablet'] ?? 2, 2, 1, 8 ),
	'desktop' => suitemart_clamp_int( $attributes['slidesPerViewDesktop'] ?? 3, 3, 1, 8 ),
);

$sm_gap      = suitemart_clamp_int( $attributes['spaceBetween'] ?? 24, 24, 0, 96 );
$sm_delay    = suitemart_clamp_int( $attributes['autoplayDelay'] ?? 5000, 5000, 1000, 30000 );
$sm_words    = suitemart_clamp_int( $attributes['excerptLength'] ?? 20, 20, 5, 100 );
$sm_level    = suitemart_clamp_int( $attributes['headingLevel'] ?? 3, 3, 2, 6 );
$sm_loop     = ! empty( $attributes['loop'] );
$sm_autoplay = ! empty( $attributes['autoplay'] );
$sm_arrows   = ! isset( $attributes['showArrows'] ) || (bool) $attributes['showArrows'];
$sm_dots     = ! isset( $attributes['showPagination'] ) || (bool) $attributes['showPagination'];
$sm_image    = ! isset( $attributes['showImage'] ) || (bool) $attributes['showImage'];
$sm_date     = ! isset( $attributes['showDate'] ) || (bool) $attributes['showDate'];
$sm_excerpt  = ! isset( $attributes['showExcerpt'] ) || (bool) $attributes['showExcerpt'];

$sm_label = isset( $attributes['label'] ) && is_string( $attributes['label'] ) && '' !== trim( $attributes['label'] )
	? trim( $attributes['label'] )
	: __( 'Posts', 'suitemart' );

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'sm-post-carousel',
		'style' => sprintf(
			'--sm-post-carousel-gap:%1$dpx;--sm-post-carousel-per-view:%2$d;--sm-post-carousel-per-view-md:%3$d;--sm-post-carousel-per-view-lg:%4$d;',
			$sm_gap,
			$sm_per_view['mobile'],
			$sm_per_view['tablet'],
			$sm_per_view['desktop']
		),
	)
);

$sm_options = array(
	'slidesPerView' => $sm_per_view['mobile'],
	'spaceBetween'  => $sm_gap,
	'loop'          => $sm_loop,
	'autoplay'      => $sm_autoplay,
	'autoplayDelay' => $sm_delay,
	'breakpoints'   => array(
		'768'  => $sm_per_view['tablet'],
		'1024' => $sm_per_view['desktop'],
	),
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	role="group"
	aria-roledescription="carousel"
	aria-label="<?php echo esc_attr( $sm_label ); ?>"
	data-wp-interactive="suitemart/post-carousel"
	<?php
	echo wp_interactivity_data_wp_context(
		array(
			'options'  => $sm_options,
			// Seeded because the autoplay toggle binds to it: an unresolved
			// `state.x` or `context.x` does not leave a directive alone, it
			// deletes the attribute written beside it.
			'isPaused' => false,
		)
	);
	?>
	data-wp-init="callbacks.mount"
>
	<?php
	/*
	 * Divs rather than a list. Swiper's a11y module puts role="group" on every
	 * slide, and a role="list" may only contain list items — axe reports it as
	 * a critical violation, and it is a real one: the list semantics are gone
	 * either way, and only the broken combination is left behind.
	 */
	?>
	<div class="sm-post-carousel__viewport swiper">
		<div class="sm-post-carousel__track swiper-wrapper">
			<?php
			while ( $sm_posts->have_posts() ) :
				$sm_posts->the_post();

				$sm_thumb = get_post_thumbnail_id();
				?>
				<div class="sm-post-carousel__slide swiper-slide">
					<article class="sm-post-carousel__card">
						<?php if ( $sm_image && $sm_thumb ) : ?>
							<a class="sm-post-carousel__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php
								/*
								 * The image link is hidden from assistive
								 * technology and taken out of the tab order: it
								 * goes to the same place as the title beneath
								 * it, and two links to one post is two stops
								 * for a keyboard user and two identical
								 * announcements for a screen reader.
								 */
								echo suitemart_block_image( (int) $sm_thumb, '', '', 'sm-post-carousel__image', 'medium_large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
								?>
							</a>
						<?php endif; ?>

						<div class="sm-post-carousel__body">
							<?php if ( $sm_date ) : ?>
								<time class="sm-post-carousel__date" datetime="<?php echo esc_attr( (string) get_the_date( 'c' ) ); ?>">
									<?php echo esc_html( (string) get_the_date() ); ?>
								</time>
							<?php endif; ?>

							<h<?php echo (int) $sm_level; ?> class="sm-post-carousel__title">
								<a class="sm-post-carousel__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h<?php echo (int) $sm_level; ?>>

							<?php if ( $sm_excerpt ) : ?>
								<p class="sm-post-carousel__excerpt">
									<?php echo esc_html( wp_trim_words( get_the_excerpt(), $sm_words ) ); ?>
								</p>
							<?php endif; ?>
						</div>
					</article>
				</div>
				<?php
			endwhile;

			wp_reset_postdata();
			?>
		</div>
	</div>

	<?php if ( $sm_arrows ) : ?>
		<div class="sm-post-carousel__nav">
			<button type="button" class="sm-post-carousel__arrow sm-post-carousel__arrow--prev" data-wp-on--click="actions.previous">
				<?php echo suitemart_get_icon( 'chevron-left', array( 'label' => __( 'Previous posts', 'suitemart' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			</button>
			<button type="button" class="sm-post-carousel__arrow sm-post-carousel__arrow--next" data-wp-on--click="actions.next">
				<?php echo suitemart_get_icon( 'chevron-right', array( 'label' => __( 'More posts', 'suitemart' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
			</button>
		</div>
	<?php endif; ?>

	<?php if ( $sm_dots ) : ?>
		<div class="sm-post-carousel__pagination swiper-pagination"></div>
	<?php endif; ?>

	<?php if ( $sm_autoplay ) : ?>
		<?php
		/*
		 * WCAG 2.2.2: anything that moves by itself for more than five seconds
		 * needs a visible, keyboard-reachable way to stop it.
		 */
		?>
		<button type="button" class="sm-post-carousel__autoplay-toggle" data-wp-on--click="actions.toggleAutoplay">
			<span data-wp-bind--hidden="context.isPaused"><?php esc_html_e( 'Pause', 'suitemart' ); ?></span>
			<span data-wp-bind--hidden="!context.isPaused" hidden><?php esc_html_e( 'Play', 'suitemart' ); ?></span>
		</button>
	<?php endif; ?>
</div>
