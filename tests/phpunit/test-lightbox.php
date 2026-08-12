<?php
/**
 * Lightbox block tests.
 *
 * The block's real work is done in PHP — finding which attachment each link
 * points at and writing its dimensions onto the anchor — so most of what
 * matters is testable here rather than in a browser.
 *
 * @package Suitemart
 */

declare( strict_types=1 );

/**
 * Tests suitemart/lightbox.
 */
class Test_Lightbox extends WP_UnitTestCase {

	/**
	 * Attachment id used across the tests.
	 *
	 * @var int
	 */
	private int $attachment = 0;

	/**
	 * Creates an attachment with real dimensions.
	 */
	public function set_up(): void {
		parent::set_up();

		$this->attachment = self::factory()->attachment->create_object(
			'photo.jpg',
			0,
			array( 'post_mime_type' => 'image/jpeg' )
		);

		wp_update_attachment_metadata(
			$this->attachment,
			array(
				'width'  => 1600,
				'height' => 1200,
				'file'   => 'photo.jpg',
				'sizes'  => array(),
			)
		);
	}

	/**
	 * Skips when the block is absent.
	 */
	private function require_block(): void {
		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'suitemart/lightbox' ) ) {
			$this->markTestSkipped( 'suitemart/lightbox is not registered here.' );
		}
	}

	/**
	 * The URL of the attachment's full-size file.
	 *
	 * @return string URL.
	 */
	private function url(): string {
		return (string) wp_get_attachment_url( $this->attachment );
	}

	/**
	 * Renders the block around some inner markup.
	 *
	 * @param string               $inner Inner HTML.
	 * @param array<string, mixed> $attrs Block attributes.
	 * @return string Rendered markup.
	 */
	private function render( string $inner, array $attrs = array() ): string {
		return do_blocks(
			sprintf(
				'<!-- wp:suitemart/lightbox %s -->%s<!-- /wp:suitemart/lightbox -->',
				wp_json_encode( (object) $attrs ),
				$inner
			)
		);
	}

	/**
	 * A linked image gets the dimensions PhotoSwipe needs.
	 */
	public function test_a_linked_image_becomes_a_gallery_item(): void {
		$this->require_block();

		$html = $this->render(
			sprintf(
				'<figure><a href="%s"><img class="wp-image-%d" src="%s" alt="A photo" /></a></figure>',
				$this->url(),
				$this->attachment,
				$this->url()
			)
		);

		$this->assertStringContainsString( 'sm-lightbox__item', $html );
		$this->assertStringContainsString( 'data-pswp-width="1600"', $html );
		$this->assertStringContainsString( 'data-pswp-height="1200"', $html );
	}

	/**
	 * An image that links nowhere is left exactly as it was.
	 *
	 * Marking every image as a gallery item would put a zoom cursor on things
	 * that cannot be zoomed, and hand PhotoSwipe items with no full-size file
	 * to open.
	 */
	public function test_an_unlinked_image_is_left_alone(): void {
		$this->require_block();

		$html = $this->render(
			sprintf(
				'<figure><img class="wp-image-%d" src="%s" alt="A photo" /></figure>',
				$this->attachment,
				$this->url()
			)
		);

		$this->assertStringNotContainsString( 'sm-lightbox__item', $html );
		$this->assertStringNotContainsString( 'data-pswp-width', $html );
	}

	/**
	 * A page with nothing to enlarge does not get the wrapper.
	 *
	 * Which means it does not load PhotoSwipe either — the module is attached
	 * to the wrapper's init directive and nothing else.
	 */
	public function test_content_with_no_image_links_is_returned_unwrapped(): void {
		$this->require_block();

		$html = $this->render( '<p>Just words.</p>' );

		$this->assertStringNotContainsString( 'sm-lightbox', $html );
		$this->assertStringNotContainsString( 'data-wp-interactive', $html );
		$this->assertStringContainsString( 'Just words.', $html );
	}

	/**
	 * Links to things that are not images are ignored.
	 */
	public function test_ordinary_links_are_ignored(): void {
		$this->require_block();

		$html = $this->render(
			sprintf(
				'<p><a href="https://example.org/about">About</a></p>'
				. '<figure><a href="%s"><img class="wp-image-%d" src="%s" alt="" /></a></figure>',
				$this->url(),
				$this->attachment,
				$this->url()
			)
		);

		preg_match_all( '/class="[^"]*sm-lightbox__item/', $html, $matches );

		$this->assertCount( 1, $matches[0] );
	}

	/**
	 * A link with no `wp-image-<id>` class is resolved from its URL instead.
	 *
	 * The class is the cheap path and covers everything WordPress inserts
	 * itself; this is the fallback for hand-written markup and importers.
	 */
	public function test_an_image_without_its_id_class_is_looked_up(): void {
		$this->require_block();

		$html = $this->render(
			sprintf(
				'<figure><a href="%s"><img src="%s" alt="" /></a></figure>',
				$this->url(),
				$this->url()
			)
		);

		$this->assertStringContainsString( 'data-pswp-width="1600"', $html );
	}

	/**
	 * Several images in one wrapper become one gallery.
	 */
	public function test_every_linked_image_is_prepared(): void {
		$this->require_block();

		$item = sprintf(
			'<figure><a href="%s"><img class="wp-image-%d" src="%s" alt="" /></a></figure>',
			$this->url(),
			$this->attachment,
			$this->url()
		);

		$html = $this->render( $item . $item . $item );

		preg_match_all( '/data-pswp-width="1600"/', $html, $matches );

		$this->assertCount( 3, $matches[0] );
	}

	/**
	 * The block's settings reach the browser as context, not as global state.
	 *
	 * Two lightboxes on one page must be able to disagree about looping.
	 */
	public function test_settings_travel_in_context(): void {
		$this->require_block();

		$html = $this->render(
			sprintf(
				'<figure><a href="%s"><img class="wp-image-%d" src="%s" alt="" /></a></figure>',
				$this->url(),
				$this->attachment,
				$this->url()
			),
			array(
				'loop'         => false,
				'showCaptions' => false,
			)
		);

		$this->assertStringContainsString( '"loop":false', $html );
		$this->assertStringContainsString( '"showCaptions":false', $html );
	}
}
