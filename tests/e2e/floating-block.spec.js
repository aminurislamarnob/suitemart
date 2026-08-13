/**
 * Floating block.
 *
 * The fixture page carries three panels: scroll-triggered (and remembering),
 * delayed, and always-on. Each test names the one it is about, because a store
 * shared by three instances is exactly where a context bug hides.
 */

const { test, expect } = require( '@playwright/test' );

const PAGE =
	process.env.SUITEMART_FLOATING_URL || '/?pagename=suitemart-floating-block';

const KEY = 'suitemart:floating:scrollpanel';

const SCROLLED = '.sm-floating-block--bottom-end';
const DELAYED = '.sm-floating-block--top-start';
const ALWAYS = '.sm-floating-block--top-end';

/**
 * Scrolls to an absolute offset and waits for the page to settle there.
 *
 * The scroll is re-issued on every poll: one sent before the page has grown to
 * its full height is clamped to the document as it stood, and waiting
 * afterwards never recovers it.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @param {number}                          top  Offset in pixels.
 */
const scrollTo = async ( page, top ) => {
	await expect
		.poll( () =>
			page.evaluate( ( to ) => {
				window.scrollTo( { top: to, behavior: 'instant' } );

				return window.scrollY;
			}, top )
		)
		.toBe( top );
};

test.describe( 'Floating block', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );
		await page.evaluate( ( key ) => {
			window.localStorage.removeItem( key );

			const spacer = document.createElement( 'div' );

			spacer.style.height = '3000px';
			document.body.append( spacer );
		}, KEY );
	} );

	test( 'an untriggered panel is there on arrival', async ( { page } ) => {
		await expect( page.locator( ALWAYS ) ).toBeVisible();

		// And has no close button, because it was told not to have one.
		await expect(
			page.locator( `${ ALWAYS } .sm-floating-block__dismiss` )
		).toHaveCount( 0 );
	} );

	test( 'waits for the scroll it was given', async ( { page } ) => {
		const panel = page.locator( SCROLLED );

		await expect( panel ).toBeHidden();

		// Short of the threshold, it stays away.
		await scrollTo( page, 200 );
		await expect( panel ).toBeHidden();

		await scrollTo( page, 900 );
		await expect( panel ).toBeVisible();

		/*
		 * And stays. Unlike back-to-top this is not a scroll indicator: a panel
		 * that appeared and vanished as the visitor moved up and down the page
		 * would be impossible to click.
		 */
		await scrollTo( page, 0 );
		await expect( panel ).toBeVisible();
	} );

	test( 'waits out its delay', async ( { page } ) => {
		const panel = page.locator( DELAYED );

		await expect( panel ).toBeHidden();
		await expect( panel ).toBeVisible( { timeout: 5000 } );
	} );

	test( 'closes, and stays closed when told to remember', async ( {
		page,
	} ) => {
		await scrollTo( page, 900 );

		const panel = page.locator( SCROLLED );

		await expect( panel ).toBeVisible();

		await panel.locator( '.sm-floating-block__dismiss' ).click();

		await expect( panel ).toBeHidden();
		expect(
			await page.evaluate(
				( key ) => window.localStorage.getItem( key ),
				KEY
			)
		).toBe( 'dismissed' );

		// The half that matters: a second page load, scrolled past the point
		// that would otherwise open it.
		await page.reload();
		await page.evaluate( () => {
			const spacer = document.createElement( 'div' );

			spacer.style.height = '3000px';
			document.body.append( spacer );
		} );
		await scrollTo( page, 900 );

		await expect( page.locator( SCROLLED ) ).toBeHidden();
	} );

	test( 'forgets a panel that was not asked to remember', async ( {
		page,
	} ) => {
		const panel = page.locator( DELAYED );

		await expect( panel ).toBeVisible( { timeout: 5000 } );
		await panel.locator( '.sm-floating-block__dismiss' ).click();
		await expect( panel ).toBeHidden();

		await page.reload();

		await expect( page.locator( DELAYED ) ).toBeVisible( {
			timeout: 5000,
		} );
	} );

	test( 'closing one panel leaves the others alone', async ( { page } ) => {
		await scrollTo( page, 900 );

		await expect( page.locator( SCROLLED ) ).toBeVisible();
		await expect( page.locator( DELAYED ) ).toBeVisible( {
			timeout: 5000,
		} );

		await page
			.locator( `${ SCROLLED } .sm-floating-block__dismiss` )
			.click();

		await expect( page.locator( SCROLLED ) ).toBeHidden();
		await expect( page.locator( DELAYED ) ).toBeVisible();
		await expect( page.locator( ALWAYS ) ).toBeVisible();
	} );

	test( 'the close button is a named, keyboard-operable button', async ( {
		page,
	} ) => {
		const panel = page.locator( DELAYED );

		await expect( panel ).toBeVisible( { timeout: 5000 } );

		const close = page.getByRole( 'button', { name: 'Close the notice' } );

		await close.focus();
		await page.keyboard.press( 'Enter' );

		await expect( panel ).toBeHidden();
	} );
} );
