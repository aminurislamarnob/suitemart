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

	/**
	 * Builds a variable product whose variations carry their own images.
	 *
	 * @return array{0: int, 1: int} Product id and the second variation's image id.
	 */
	private function variable_product(): array {
		$attribute = new \WC_Product_Attribute();
		$attribute->set_name( 'Color' );
		$attribute->set_options( array( 'Blue', 'Orange' ) );
		$attribute->set_visible( true );
		$attribute->set_variation( true );

		$product = new \WC_Product_Variable();
		$product->set_name( 'Variable Gallery Product' );
		$product->set_status( 'publish' );
		$product->set_image_id( $this->image_id );
		$product->set_gallery_image_ids( $this->gallery_ids );
		$product->set_attributes( array( $attribute ) );
		$product_id = $product->save();

		$second_image = 0;

		foreach ( array(
			'Blue'   => $this->image_id,
			'Orange' => $this->gallery_ids[0],
		) as $value => $image_id ) {
			$variation = new \WC_Product_Variation();
			$variation->set_parent_id( $product_id );
			$variation->set_attributes( array( 'color' => $value ) );
			$variation->set_regular_price( '25' );
			$variation->set_image_id( $image_id );
			$variation->save();

			$second_image = $image_id;
		}

		\WC_Product_Variable::sync( $product_id );

		return array( $product_id, $second_image );
	}

	/**
	 * Renders the gallery against a product.
	 *
	 * @param int                  $product_id Product to render for.
	 * @param array<string, mixed> $attrs      Block attributes.
	 * @return string Rendered markup.
	 */
	private function render_for( int $product_id, array $attrs = array() ): string {
		$block = array(
			'blockName'    => 'suitemart/product-gallery',
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		return ( new \WP_Block( $block, array( 'postId' => $product_id ) ) )->render();
	}

	/**
	 * Reads the block's seeded Interactivity context back out of the markup.
	 *
	 * @param string $markup Rendered block.
	 * @return array<string, mixed> Decoded context.
	 */
	private function context_from( string $markup ): array {
		$this->assertSame(
			1,
			preg_match( '/data-wp-context=\'([^\']*)\'/', $markup, $matches ),
			'The gallery seeded no context at all.'
		);

		return (array) json_decode( html_entity_decode( $matches[1], ENT_QUOTES ), true );
	}

	/**
	 * A variable product seeds each variation's image, keyed by its attributes.
	 *
	 * The browser matches on these rather than reading WooCommerce's own
	 * variation store, which is locked private and documented as unstable.
	 */
	public function test_variable_product_seeds_variation_images(): void {
		list( $product_id, $second_image ) = $this->variable_product();

		$context = $this->context_from( $this->render_for( $product_id ) );

		$this->assertCount( 2, $context['variations'] );

		$orange = null;

		foreach ( $context['variations'] as $variation ) {
			if ( 'orange' === ( $variation['attributes']['attribute_color'] ?? '' ) ) {
				$orange = $variation;
			}
		}

		$this->assertNotNull( $orange, 'No variation was seeded for the second colour.' );
		$this->assertSame( $second_image, $orange['imageId'] );
		$this->assertNotEmpty( $orange['image']['src'], 'The variation image has no source to swap in.' );

		// Values are lower-cased because the form reports whatever case the
		// shopper's selection was rendered in, and the two have to compare.
		$this->assertSame( 'orange', $orange['attributes']['attribute_color'] );
	}

	/**
	 * The slide ids let the browser move to an image already in the gallery.
	 */
	public function test_seeds_the_slide_order(): void {
		list( $product_id ) = $this->variable_product();

		$context = $this->context_from( $this->render_for( $product_id ) );

		$expected = array_merge( array( $this->image_id ), $this->gallery_ids );

		$this->assertSame( $expected, $context['slideIds'] );

		foreach ( $expected as $image_id ) {
			$this->assertStringContainsString(
				sprintf( 'data-sm-image-id="%d"', $image_id ),
				$this->render_for( $product_id ),
				'A slide is missing the id the variation switcher matches on.'
			);
		}
	}

	/**
	 * A simple product has nothing to switch between.
	 */
	public function test_simple_product_seeds_no_variations(): void {
		$context = $this->context_from( $this->render_for( $this->product_id ) );

		$this->assertSame( array(), $context['variations'] );
	}

	/**
	 * A variation without its own image falls through to the product's.
	 */
	public function test_variations_without_an_image_are_skipped(): void {
		$attribute = new \WC_Product_Attribute();
		$attribute->set_name( 'Color' );
		$attribute->set_options( array( 'Blue' ) );
		$attribute->set_visible( true );
		$attribute->set_variation( true );

		$product = new \WC_Product_Variable();
		$product->set_name( 'Imageless Variations' );
		$product->set_status( 'publish' );
		$product->set_image_id( $this->image_id );
		$product->set_attributes( array( $attribute ) );
		$product_id = $product->save();

		$variation = new \WC_Product_Variation();
		$variation->set_parent_id( $product_id );
		$variation->set_attributes( array( 'color' => 'Blue' ) );
		$variation->set_regular_price( '25' );
		$variation->save();

		\WC_Product_Variable::sync( $product_id );

		$context = $this->context_from( $this->render_for( $product_id ) );

		$this->assertSame( array(), $context['variations'] );
	}
}
