import { test, expect } from '@playwright/test';

const PRODUCT = process.env.SUITEMART_PRODUCT_URL;

test.describe( 'Size guide modal', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PRODUCT );
	} );

	test( 'opens, traps focus, and closes on Escape', async ( { page } ) => {
		const trigger = page.locator( '.sm-size-guide-button' ).first();
		const dialog = page.locator( '.sm-size-guide__dialog' ).first();

		await expect( dialog ).not.toBeVisible();
		await expect( trigger ).toHaveAttribute( 'aria-expanded', 'false' );

		await trigger.click();

		await expect( dialog ).toBeVisible();
		await expect( trigger ).toHaveAttribute( 'aria-expanded', 'true' );
		// Focus must move into the dialog, or a keyboard user is left behind it.
		// It moves on the next animation frame once the panel is visible.
		await expect
			.poll(
				() =>
					page.evaluate(
						( sel ) =>
							!! document
								.querySelector( sel )
								?.contains( document.activeElement ),
						'.sm-size-guide__dialog'
					),
				{ message: 'focus should move inside the dialog' }
			)
			.toBe( true );

		await page.keyboard.press( 'Escape' );

		await expect( dialog ).not.toBeVisible();
		await expect( trigger ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( trigger ).toBeFocused(); // Focus returns to trigger
	} );
} );
