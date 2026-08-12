<?php
/**
 * Test the Product Gallery block.
 *
 * @package Suitemart
 */

namespace Suitemart\Tests\Blocks;

use WP_UnitTestCase;
use WP_Block;

/**
 * Class Test_Product_Gallery
 */
class Test_Product_Gallery extends WP_UnitTestCase {

	/**
	 * Main product ID.
	 *
	 * @var int
	 */
	private int $product_id;

	/**
	 * Main image ID.
	 *
	 * @var int
	 */
	private int $image_id;

	/**
	 * Gallery image IDs.
	 *
	 * @var array<int>
	 */
	private array $gallery_ids = array();

	/**
	 * Setup.
	 */
	public function set_up(): void {
		parent::set_up();

		$this->image_id = self::factory()->attachment->create_object(
			array(
				'file'           => 'image-main.jpg',
				'post_mime_type' => 'image/jpeg',
				'post_type'      => 'attachment',
				'post_title'     => 'Main Image',
			)
		);

		for ( $i = 0; $i < 2; $i++ ) {
			$this->gallery_ids[] = self::factory()->attachment->create_object(
				array(
					'file'           => 'image-gallery-' . $i . '.jpg',
					'post_mime_type' => 'image/jpeg',
					'post_type'      => 'attachment',
					'post_title'     => 'Gallery Image ' . $i,
				)
			);
		}

		$main = new \WC_Product_Simple();
		$main->set_name( 'Gallery Product' );
		$main->set_regular_price( '20' );
		$main->set_status( 'publish' );
		$main->set_image_id( $this->image_id );
		$main->set_gallery_image_ids( $this->gallery_ids );
		$main->save();
		$this->product_id = $main->get_id();
	}

	/**
	 * Tests successful render with horizontal layout.
	 */
	public function test_renders_horizontal(): void {
		$block = array(
			'blockName'    => 'suitemart/product-gallery',
			'attrs'        => array(
				'layout' => 'horizontal',
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		$markup = ( new \WP_Block( $block, array( 'postId' => $this->product_id ) ) )->render();

		$this->assertStringContainsString( 'sm-product-gallery--horizontal', $markup );
		$this->assertStringContainsString( 'sm-product-gallery__main', $markup );
		$this->assertStringContainsString( 'sm-product-gallery__thumbs', $markup );
		$this->assertStringContainsString( 'data-wp-interactive="suitemart/product-gallery"', $markup );
	}

	/**
	 * Tests successful render with grid layout.
	 */
	public function test_renders_grid(): void {
		$block = array(
			'blockName'    => 'suitemart/product-gallery',
			'attrs'        => array(
				'layout' => 'grid',
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		$markup = ( new \WP_Block( $block, array( 'postId' => $this->product_id ) ) )->render();

		$this->assertStringContainsString( 'sm-product-gallery--grid', $markup );
		$this->assertStringContainsString( 'sm-product-gallery__grid', $markup );
		$this->assertStringNotContainsString( 'data-wp-interactive="suitemart/product-gallery"', $markup ); // Grid shouldn't load Swiper.
	}

	/**
	 * Tests empty state.
	 */
	public function test_renders_empty_when_no_images(): void {
		$empty = new \WC_Product_Simple();
		$empty->set_name( 'Empty Product' );
		$empty->set_status( 'publish' );
		$empty->save();

		$block = array(
			'blockName'    => 'suitemart/product-gallery',
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		$markup = ( new \WP_Block( $block, array( 'postId' => $empty->get_id() ) ) )->render();

		$this->assertStringContainsString( 'sm-product-gallery--empty', $markup );
	}
}
