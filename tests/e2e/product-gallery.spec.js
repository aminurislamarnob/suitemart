const { test, expect } = require( '@playwright/test' );

test.describe( 'Product Gallery block', () => {
	test.use( { baseURL: process.env.WP_BASE_URL || 'http://localhost:8888' } );

	test( 'renders main gallery and thumbnails and handles interaction', async ( {
		page,
	} ) => {
		// Go to the test product which has fake image IDs
		await page.goto( process.env.SUITEMART_PRODUCT_BLOCKS_URL );

		// The block should exist
		const galleryBlock = page.locator( '.sm-product-gallery' );
		await expect( galleryBlock ).toBeVisible();

		// Should have horizontal layout by default
		await expect( galleryBlock ).toHaveClass(
			/sm-product-gallery--horizontal/
		);

		// Should have main and thumbs
		const mainTrack = galleryBlock.locator( '.sm-product-gallery__main' );
		const thumbsTrack = galleryBlock.locator(
			'.sm-product-gallery__thumbs'
		);

		await expect( mainTrack ).toBeVisible();
		await expect( thumbsTrack ).toBeVisible();

		// Should have multiple slides
		const mainSlides = mainTrack.locator(
			'.sm-product-gallery__main-slide'
		);
		const thumbSlides = thumbsTrack.locator(
			'.sm-product-gallery__thumbs-slide'
		);

		// The fixture product carries a featured image plus one gallery image,
		// and the block leads with the featured one — so two distinct slides.
		await expect( mainSlides ).toHaveCount( 2 );
		await expect( thumbSlides ).toHaveCount( 2 );

		// Click the second thumbnail
		await thumbSlides.nth( 1 ).click();

		// The second main slide should become active.
		// Swiper adds .swiper-slide-active to the active slide.
		await expect( mainSlides.nth( 1 ) ).toHaveClass(
			/swiper-slide-active/
		);
	} );
} );
