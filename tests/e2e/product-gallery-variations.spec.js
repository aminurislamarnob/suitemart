const { test, expect } = require( '@playwright/test' );

/**
 * The gallery has to follow the variation the shopper picks.
 *
 * WooCommerce keeps the selected variation in a private Interactivity store it
 * explicitly says will break between releases, so the gallery reads the
 * add-to-cart form's `attribute_*` fields instead and matches them itself. That
 * makes this spec the real contract test: it is what will notice if WooCommerce
 * ever changes how the variation selector renders those fields.
 */
test.describe( 'Product gallery variations', () => {
	test.use( { baseURL: process.env.WP_BASE_URL || 'http://localhost:8888' } );

	/**
	 * Clicks a variation option by its visible label.
	 *
	 * @param {import('@playwright/test').Page} page  Page.
	 * @param {string}                          label Option label.
	 */
	const choose = async ( page, label ) => {
		await page
			.locator( '[class*="variation-selector"]' )
			.getByText( label, { exact: true } )
			.first()
			.click();
	};

	const activeSlideId = ( page ) =>
		page
			.locator( '.sm-product-gallery__main-slide.swiper-slide-active' )
			.getAttribute( 'data-sm-image-id' );

	test( 'choosing a colour moves the gallery to that image', async ( {
		page,
	} ) => {
		await page.goto( process.env.SUITEMART_VARIABLE_URL );

		const gallery = page.locator( '.sm-product-gallery' );
		await expect( gallery ).toBeVisible();

		// Read the ids off the block's own context rather than hardcoding them;
		// attachment ids differ between environments.
		const slideIds = await gallery.evaluate(
			( el ) => JSON.parse( el.dataset.wpContext ).slideIds
		);

		expect( slideIds ).toHaveLength( 2 );

		// Nothing is selected on load, so the featured image leads.
		await expect
			.poll( () => activeSlideId( page ) )
			.toBe( String( slideIds[ 0 ] ) );

		// The second colour carries the second image.
		await choose( page, 'Orange' );

		await expect
			.poll( () => activeSlideId( page ) )
			.toBe( String( slideIds[ 1 ] ) );

		await expect( gallery ).toHaveClass( /is-showing-variation/ );

		// And back, so the switch is not one-way.
		await choose( page, 'Blue' );

		await expect
			.poll( () => activeSlideId( page ) )
			.toBe( String( slideIds[ 0 ] ) );
	} );

	test( 'clearing the selection returns to the featured image', async ( {
		page,
	} ) => {
		await page.goto( process.env.SUITEMART_VARIABLE_URL );

		const gallery = page.locator( '.sm-product-gallery' );
		const slideIds = await gallery.evaluate(
			( el ) => JSON.parse( el.dataset.wpContext ).slideIds
		);

		await choose( page, 'Orange' );

		await expect
			.poll( () => activeSlideId( page ) )
			.toBe( String( slideIds[ 1 ] ) );

		// Clicking the chosen option again deselects it.
		await choose( page, 'Orange' );

		await expect
			.poll( () => activeSlideId( page ) )
			.toBe( String( slideIds[ 0 ] ) );

		await expect( gallery ).not.toHaveClass( /is-showing-variation/ );
	} );

	test( 'a simple product seeds no variation data', async ( { page } ) => {
		await page.goto( process.env.SUITEMART_PRODUCT_BLOCKS_URL );

		const variations = await page
			.locator( '.sm-product-gallery' )
			.evaluate(
				( el ) => JSON.parse( el.dataset.wpContext ).variations
			);

		expect( variations ).toEqual( [] );
	} );
} );
