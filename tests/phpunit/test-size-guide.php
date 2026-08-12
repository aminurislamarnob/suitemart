<?php
/**
 * Size guide block tests.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests the size guide blocks.
 */
class Test_Size_Guide extends WP_UnitTestCase {

	/**
	 * Skips when the blocks are absent.
	 */
	private function require_blocks(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/size-guide' ) ) {
			$this->markTestSkipped( 'suitemart/size-guide is not registered here.' );
		}
	}

	/**
	 * Renders a block against a post supplied as context.
	 *
	 * @param string               $block_name Block name.
	 * @param int                  $post_id    Post to render against.
	 * @param array<string, mixed> $attrs      Block attributes.
	 * @param string               $inner_html Inner HTML.
	 * @return string Rendered markup.
	 */
	private function render( string $block_name, int $post_id, array $attrs = array(), string $inner_html = '' ): string {
		$block = array(
			'blockName'    => $block_name,
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => $inner_html,
			'innerContent' => array( $inner_html ),
		);

		return ( new WP_Block( $block, array( 'postId' => $post_id ) ) )->render();
	}

	/**
	 * Renders the button successfully.
	 */
	public function test_renders_size_guide_button(): void {
		$this->require_blocks();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);

		$html = $this->render( 'suitemart/size-guide-button', $product_id, array( 'label' => 'View Size Guide' ) );

		$this->assertStringContainsString( 'sm-size-guide-button', $html );
		$this->assertStringContainsString( 'View Size Guide', $html );
		$this->assertStringContainsString( 'data-wp-interactive="suitemart/size-guide"', $html );
	}

	/**
	 * Renders the modal successfully.
	 */
	public function test_renders_size_guide_modal(): void {
		$this->require_blocks();

		$product_id = self::factory()->post->create(
			array(
				'post_type'   => 'product',
				'post_status' => 'publish',
			)
		);

		$html = $this->render( 'suitemart/size-guide', $product_id, array( 'title' => 'Shirt Size Guide' ), '<p>Size chart content.</p>' );

		$this->assertStringContainsString( 'sm-size-guide', $html );
		$this->assertStringContainsString( 'Shirt Size Guide', $html );
		$this->assertStringContainsString( 'Size chart content.', $html );
		$this->assertStringContainsString( 'data-wp-interactive="suitemart/size-guide"', $html );
	}
}
