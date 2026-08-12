<?php
/**
 * Visitor counter block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests the visitor counter block.
 */
class Test_Visitor_Counter extends WP_UnitTestCase {

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/visitor-counter' ) ) {
			$this->markTestSkipped( 'suitemart/visitor-counter is not registered here.' );
		}
	}

	/**
	 * Renders the block against a post supplied as context.
	 *
	 * @param int                  $post_id Post to render against.
	 * @param array<string, mixed> $attrs   Block attributes.
	 * @return string Rendered markup.
	 */
	private function render( int $post_id, array $attrs = array() ): string {
		$block = array(
			'blockName'    => 'suitemart/visitor-counter',
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		return ( new WP_Block( $block, array( 'postId' => $post_id ) ) )->render();
	}

	/**
	 * Renders the block successfully.
	 */
	public function test_renders_visitor_counter(): void {
		$this->require_block();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);

		$html = $this->render(
			$product_id,
			array(
				'minVisitors' => 20,
				'maxVisitors' => 50,
			)
		);

		$this->assertStringContainsString( 'sm-visitor-counter', $html );
		$this->assertStringContainsString( 'people are viewing this right now', wp_strip_all_tags( $html ) );
		$this->assertStringContainsString( 'data-wp-interactive="suitemart/visitor-counter"', $html );
	}
}
