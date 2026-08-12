import { test, expect } from '@playwright/test';

test.describe( 'Quick view button', () => {
	test.beforeEach( async ( { page } ) => {
		page.on( 'console', ( msg ) =>
			// eslint-disable-next-line no-console
			console.log( 'BROWSER CONSOLE:', msg.text() )
		);
		// eslint-disable-next-line no-console
		page.on( 'pageerror', ( err ) => console.log( 'BROWSER ERROR:', err ) );
		await page.goto( process.env.SUITEMART_PRODUCT_BLOCKS_URL );
	} );

	test( 'opens the modal, fetches product data, and traps focus', async ( {
		page,
	} ) => {
		const button = page.locator( '.sm-quick-view-button' ).first();
		await expect( button ).toBeVisible();

		// Intercept the API request to ensure it actually fetches data.
		const apiPromise = page.waitForResponse( ( response ) =>
			decodeURIComponent( response.url() ).includes(
				'wc/store/v1/products'
			)
		);

		await button.click();

		const response = await apiPromise;
		expect( response.ok() ).toBeTruthy();

		const dialog = page.locator( '.sm-quick-view-modal__dialog' );
		await expect( dialog ).toBeVisible();

		// Wait for the loading state to disappear.
		const loading = page.locator( '.sm-quick-view-modal__loading' );
		await expect( loading ).toBeHidden();

		// Wait for the product content to appear.
		const title = page.locator( '.sm-quick-view-modal__product-title' );
		await expect( title ).toBeVisible();
		await expect( title ).not.toBeEmpty();

		// Focus must move into the dialog.
		await expect
			.poll(
				() =>
					page.evaluate(
						( sel ) =>
							!! document
								.querySelector( sel )
								?.contains( document.activeElement ),
						'.sm-quick-view-modal__dialog'
					),
				{ message: 'focus should move inside the dialog' }
			)
			.toBe( true );

		// Close via close button.
		await page.locator( '.sm-quick-view-modal__close' ).click();
		await expect( page.locator( '.sm-quick-view-modal' ) ).not.toHaveClass(
			/is-open/
		);

		// Focus returns to the button.
		await expect( button ).toBeFocused();
	} );
} );
