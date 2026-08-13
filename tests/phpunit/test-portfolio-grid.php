<?php
/**
 * Portfolio grid block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/portfolio-grid.
 */
class Test_Portfolio_Grid extends WP_UnitTestCase {

	/**
	 * Project ids, in creation order.
	 *
	 * @var array<int, int>
	 */
	private array $projects = array();

	/**
	 * Term ids by slug.
	 *
	 * @var array<string, int>
	 */
	private array $terms = array();

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/portfolio-grid' ) ) {
			$this->markTestSkipped( 'suitemart/portfolio-grid is not registered here.' );
		}
	}

	/**
	 * Publishes two projects in each of two categories.
	 */
	private function seed(): void {
		foreach ( array( 'branding', 'interiors' ) as $slug ) {
			$this->terms[ $slug ] = (int) self::factory()->term->create(
				array(
					'taxonomy' => 'project-cat',
					'slug'     => $slug,
					'name'     => ucfirst( $slug ),
				)
			);
		}

		$plan = array(
			array( 'Harbour brand', 'branding' ),
			array( 'Loft interior', 'interiors' ),
			array( 'Ceramic set', 'interiors' ),
		);

		foreach ( $plan as $index => $project ) {
			list( $title, $slug ) = $project;

			$id = self::factory()->post->create(
				array(
					'post_type'  => 'portfolio',
					'post_title' => $title,
					'post_date'  => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( $index + 1 ) . ' days' ) ),
				)
			);

			wp_set_object_terms( $id, array( $this->terms[ $slug ] ), 'project-cat' );

			$this->projects[] = $id;
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
			sprintf( '<!-- wp:suitemart/portfolio-grid %s /-->', wp_json_encode( (object) $attrs ) )
		);
	}

	/**
	 * With no projects, nothing is rendered.
	 */
	public function test_no_projects_renders_nothing(): void {
		$this->require_block();

		$this->assertSame( '', trim( $this->render() ) );
	}

	/**
	 * Every project is in the page, and so is a filter for each category.
	 *
	 * The grid filters what is already there rather than fetching a new set,
	 * so "all of them are present" is the load-bearing fact: it is what makes
	 * filtering instant and what makes the block work without JavaScript.
	 */
	public function test_every_project_and_filter_is_served(): void {
		$this->require_block();
		$this->seed();

		$html = $this->render();

		// Quoted, because the container's class is `__items` and an unquoted
		// count of `__item` matches it too.
		$this->assertSame( 3, substr_count( $html, 'sm-portfolio-grid__item"' ) );
		$this->assertStringContainsString( 'Harbour brand', $html );
		$this->assertStringContainsString( 'Ceramic set', $html );

		// All, plus one per category actually used.
		$this->assertSame( 3, substr_count( $html, 'sm-portfolio-grid__filter"' ) );
		$this->assertStringContainsString( 'data-slug="branding"', $html );
		$this->assertStringContainsString( 'data-slug="interiors"', $html );
	}

	/**
	 * Nothing is hidden when the page is served.
	 *
	 * `state.isHidden` is declared in PHP as well as in JavaScript. If the PHP
	 * half went missing the directive would not merely fail to resolve — it
	 * would delete attributes, and the grid would arrive in some other state
	 * than the one the browser then computes.
	 */
	public function test_the_grid_is_served_unfiltered(): void {
		$this->require_block();
		$this->seed();

		$html = $this->render();

		$this->assertDoesNotMatchRegularExpression( '/<article[^>]*\shidden[=\s>]/', $html );
		$this->assertStringContainsString( 'data-wp-bind--hidden="state.isHidden"', $html );
		$this->assertStringContainsString( '"active":""', $html );

		// And "All" is the pressed one, resolved rather than deleted.
		$this->assertSame( 1, substr_count( $html, 'aria-pressed="true"' ) );
		$this->assertSame( 2, substr_count( $html, 'aria-pressed="false"' ) );
	}

	/**
	 * Each project carries the categories it belongs to.
	 */
	public function test_projects_carry_their_categories(): void {
		$this->require_block();
		$this->seed();

		$this->assertStringContainsString( '&quot;terms&quot;:[&quot;branding&quot;]', str_replace( '"', '&quot;', $this->render() ) );
	}

	/**
	 * One category means no filter bar.
	 *
	 * A row of buttons where every one shows the same thing is furniture.
	 */
	public function test_a_single_category_gets_no_filters(): void {
		$this->require_block();
		$this->seed();

		$html = $this->render( array( 'terms' => array( $this->terms['interiors'] ) ) );

		$this->assertStringNotContainsString( 'sm-portfolio-grid__filters', $html );
		$this->assertStringContainsString( 'Loft interior', $html );
		$this->assertStringNotContainsString( 'Harbour brand', $html );
	}

	/**
	 * The filters can be turned off outright.
	 */
	public function test_filters_can_be_turned_off(): void {
		$this->require_block();
		$this->seed();

		$this->assertStringNotContainsString(
			'sm-portfolio-grid__filters',
			$this->render( array( 'showFilters' => false ) )
		);
	}

	/**
	 * The live region says what is being shown, and is not left empty.
	 */
	public function test_the_status_is_announced(): void {
		$this->require_block();
		$this->seed();

		$html = $this->render();

		$this->assertStringContainsString( 'aria-live="polite"', $html );
		$this->assertStringContainsString( 'data-wp-text="state.status"', $html );
		$this->assertStringContainsString( 'Showing all projects', $html );
	}

	/**
	 * Layout attributes reach the markup, and nonsense does not.
	 */
	public function test_layout_is_validated(): void {
		$this->require_block();
		$this->seed();

		$this->assertStringContainsString(
			'--sm-portfolio-columns:4',
			$this->render( array( 'columns' => 4 ) )
		);
		$this->assertStringContainsString(
			'--sm-portfolio-columns:6',
			$this->render( array( 'columns' => 99 ) )
		);
		$this->assertStringContainsString(
			'<h2 class="sm-portfolio-grid__title"',
			$this->render( array( 'headingLevel' => 2 ) )
		);
	}

	/**
	 * Ordering reaches the query.
	 */
	public function test_ordering_is_applied(): void {
		$this->require_block();
		$this->seed();

		$newest = $this->render();
		$oldest = $this->render( array( 'order' => 'asc' ) );

		$this->assertLessThan(
			strpos( $newest, 'Ceramic set' ),
			strpos( $newest, 'Harbour brand' )
		);
		$this->assertLessThan(
			strpos( $oldest, 'Harbour brand' ),
			strpos( $oldest, 'Ceramic set' )
		);
	}

	/**
	 * The count respects the setting.
	 */
	public function test_the_count_is_respected(): void {
		$this->require_block();
		$this->seed();

		$html = $this->render( array( 'postsToShow' => 2 ) );

		$this->assertSame( 2, substr_count( $html, 'sm-portfolio-grid__item"' ) );
	}
}
