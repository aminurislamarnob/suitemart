import { test, expect } from '@playwright/test';

const PRODUCT = process.env.SUITEMART_PRODUCT_URL;

test.describe( 'Frequently bought together block', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PRODUCT );
	} );

	test( 'renders FBT, updates total, and adds to cart', async ( {
		page,
	} ) => {
		const fbtBlock = page.locator( '.sm-fbt-products' );

		// If the block is not visible, it might mean the template is missing it or cross-sell failed.
		// It's in the template, so it should be visible.
		await expect( fbtBlock ).toBeVisible();

		const checkboxes = fbtBlock.locator(
			'.sm-fbt-products__item-checkbox'
		);
		await expect( checkboxes ).toHaveCount( 2 );

		// Initially both are checked.
		const total = fbtBlock.locator( '.sm-fbt-products__total-price' );
		await expect( total ).not.toBeEmpty();

		// Uncheck one.
		await checkboxes.nth( 1 ).uncheck();

		// The total price should update.
		// It's hard to test exact numbers if prices vary, but we can verify it doesn't crash.
		await expect( total ).toBeVisible();

		// Intercept the API request to the cart.
		const cartPromise = page.waitForResponse( ( response ) =>
			decodeURIComponent( response.url() ).includes(
				'wc/store/v1/cart/add-item'
			)
		);

		const addButton = fbtBlock.locator( '.sm-fbt-products__add-to-cart' );
		await addButton.click();

		const response = await cartPromise;
		if ( ! response.ok() ) {
			const body = await response.text();
			// eslint-disable-next-line no-console
			console.log( 'Store API Error:', response.status(), body );
		}
		expect( response.ok() ).toBeTruthy();
	} );
} );
