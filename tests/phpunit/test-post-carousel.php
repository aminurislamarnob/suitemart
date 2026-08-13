<?php
/**
 * Post carousel block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/post-carousel.
 */
class Test_Post_Carousel extends WP_UnitTestCase {

	/**
	 * Ids of the posts created for a test, newest last.
	 *
	 * @var array<int, int>
	 */
	private array $post_ids = array();

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/post-carousel' ) ) {
			$this->markTestSkipped( 'suitemart/post-carousel is not registered here.' );
		}
	}

	/**
	 * Publishes posts a day apart, so ordering by date is deterministic.
	 *
	 * @param int $count How many.
	 */
	private function seed_posts( int $count = 3 ): void {
		for ( $i = 1; $i <= $count; $i++ ) {
			$this->post_ids[] = self::factory()->post->create(
				array(
					'post_title'   => "Carousel post {$i}",
					'post_excerpt' => "Excerpt {$i}.",
					'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( "-{$i} days" ) ),
				)
			);
		}
	}

	/**
	 * Renders the block through do_blocks(), so directive processing runs.
	 *
	 * @param array<string, mixed> $attrs Block attributes.
	 * @return string Rendered markup.
	 */
	private function render( array $attrs = array() ): string {
		return do_blocks(
			sprintf( '<!-- wp:suitemart/post-carousel %s /-->', wp_json_encode( (object) $attrs ) )
		);
	}

	/**
	 * A query with nothing in it renders nothing.
	 *
	 * Not an empty carousel with working arrows over blank space.
	 */
	public function test_no_posts_renders_nothing(): void {
		$this->require_block();

		$this->assertSame( '', trim( $this->render( array( 'postType' => 'post' ) ) ) );
	}

	/**
	 * One card per post, and the query respects the count.
	 */
	public function test_it_renders_a_card_per_post(): void {
		$this->require_block();
		$this->seed_posts( 5 );

		$html = $this->render( array( 'postsToShow' => 3 ) );

		$this->assertSame( 3, substr_count( $html, 'sm-post-carousel__slide' ) );
		$this->assertStringContainsString( 'Carousel post 1', $html );
		$this->assertStringNotContainsString( 'Carousel post 4', $html );
	}

	/**
	 * The carousel names itself, and says it is one.
	 */
	public function test_it_is_a_labelled_carousel(): void {
		$this->require_block();
		$this->seed_posts( 2 );

		$html = $this->render( array( 'label' => 'From the blog' ) );

		$this->assertStringContainsString( 'aria-roledescription="carousel"', $html );
		$this->assertStringContainsString( 'aria-label="From the blog"', $html );
		$this->assertStringContainsString( 'aria-label="Posts"', $this->render() );
	}

	/**
	 * Ordering reaches the query rather than only the markup.
	 */
	public function test_ordering_is_applied(): void {
		$this->require_block();
		$this->seed_posts( 3 );

		$newest_first = $this->render();
		$oldest_first = $this->render( array( 'order' => 'asc' ) );

		// Seeded one, two and three days ago, so post 1 is the newest.
		$this->assertLessThan(
			strpos( $newest_first, 'Carousel post 3' ),
			strpos( $newest_first, 'Carousel post 1' )
		);
		$this->assertLessThan(
			strpos( $oldest_first, 'Carousel post 1' ),
			strpos( $oldest_first, 'Carousel post 3' )
		);
	}

	/**
	 * Card parts appear only when asked for.
	 */
	public function test_card_parts_are_optional(): void {
		$this->require_block();
		$this->seed_posts( 1 );

		$full = $this->render();

		$this->assertStringContainsString( 'sm-post-carousel__date', $full );
		$this->assertStringContainsString( 'sm-post-carousel__excerpt', $full );

		$bare = $this->render(
			array(
				'showDate'    => false,
				'showExcerpt' => false,
				'showImage'   => false,
			)
		);

		$this->assertStringNotContainsString( 'sm-post-carousel__date', $bare );
		$this->assertStringNotContainsString( 'sm-post-carousel__excerpt', $bare );
		$this->assertStringNotContainsString( 'sm-post-carousel__media', $bare );

		// The title is not optional: a card with no title is a link to nowhere
		// in particular.
		$this->assertStringContainsString( 'sm-post-carousel__title', $bare );
	}

	/**
	 * The heading level is the editor's choice, and is clamped to a heading.
	 */
	public function test_heading_level_is_clamped(): void {
		$this->require_block();
		$this->seed_posts( 1 );

		$this->assertStringContainsString(
			'<h2 class="sm-post-carousel__title"',
			$this->render( array( 'headingLevel' => 2 ) )
		);
		$this->assertStringContainsString(
			'<h6 class="sm-post-carousel__title"',
			$this->render( array( 'headingLevel' => 9 ) )
		);
	}

	/**
	 * Nonsense in the carousel options falls back rather than reaching Swiper.
	 */
	public function test_carousel_options_are_validated(): void {
		$this->require_block();
		$this->seed_posts( 1 );

		$html = $this->render(
			array(
				'slidesPerViewDesktop' => 99,
				'spaceBetween'         => -20,
				'autoplayDelay'        => 10,
				'autoplay'             => true,
			)
		);

		$this->assertStringContainsString( '--sm-post-carousel-per-view-lg:8', $html );
		$this->assertStringContainsString( '--sm-post-carousel-gap:0px', $html );
		$this->assertStringContainsString( '"autoplayDelay":1000', $html );
	}

	/**
	 * Autoplay comes with a way to stop it.
	 *
	 * WCAG 2.2.2. The button is part of the block rather than something the
	 * editor can forget to add.
	 */
	public function test_autoplay_ships_with_a_pause_button(): void {
		$this->require_block();
		$this->seed_posts( 2 );

		$this->assertStringNotContainsString(
			'sm-post-carousel__autoplay-toggle',
			$this->render()
		);
		$this->assertStringContainsString(
			'sm-post-carousel__autoplay-toggle',
			$this->render( array( 'autoplay' => true ) )
		);
	}

	/**
	 * An unknown post type falls back to posts rather than querying nothing.
	 */
	public function test_unknown_post_type_falls_back(): void {
		$this->require_block();
		$this->seed_posts( 1 );

		$this->assertStringContainsString(
			'Carousel post 1',
			$this->render( array( 'postType' => 'not_a_type' ) )
		);
	}

	/**
	 * A term filter narrows the query, and a broken one does not empty it.
	 */
	public function test_term_filtering(): void {
		$this->require_block();
		$this->seed_posts( 3 );

		$term = self::factory()->term->create(
			array(
				'taxonomy' => 'category',
				'name'     => 'Carousel category',
			)
		);

		wp_set_object_terms( $this->post_ids[0], array( $term ), 'category' );

		$filtered = $this->render(
			array(
				'taxonomy' => 'category',
				'terms'    => array( $term ),
			)
		);

		$this->assertStringContainsString( 'Carousel post 1', $filtered );
		$this->assertStringNotContainsString( 'Carousel post 2', $filtered );

		// A taxonomy that no longer exists is ignored rather than applied,
		// which would return nothing and look like the block was broken.
		$this->assertStringContainsString(
			'Carousel post 2',
			$this->render(
				array(
					'taxonomy' => 'deleted_taxonomy',
					'terms'    => array( $term ),
				)
			)
		);
	}

	/**
	 * The image link is kept out of the way of the title link.
	 *
	 * Two links to the same post is two tab stops and two identical
	 * announcements; the image one is decorative once the title is there.
	 */
	public function test_the_image_link_is_not_a_second_stop(): void {
		$this->require_block();
		$this->seed_posts( 1 );

		$attachment = self::factory()->attachment->create_upload_object(
			DIR_TESTDATA . '/images/canola.jpg'
		);

		set_post_thumbnail( $this->post_ids[0], $attachment );

		$html = $this->render();

		$this->assertStringContainsString( 'sm-post-carousel__media', $html );
		$this->assertMatchesRegularExpression(
			'/<a class="sm-post-carousel__media"[^>]*tabindex="-1"[^>]*aria-hidden="true"/',
			$html
		);
	}
}
