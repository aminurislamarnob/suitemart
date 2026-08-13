/**
 * Popup.
 *
 * The fixture page carries two: a delayed one that shows once and closes on a
 * backdrop click, and a scroll-triggered one that does neither. Most of what is
 * asserted here belongs to `<dialog>` rather than to the block — focus moving
 * in and coming back, Escape, the page behind going inert — and it is asserted
 * anyway, because that behaviour is the reason the block is built this way and
 * a later "improvement" to a div would quietly lose all of it.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const PAGE = process.env.SUITEMART_POPUP_URL || '/?pagename=suitemart-popup';

const KEY = 'suitemart:popup:delayedpopup';

const DELAYED = 'dialog[aria-label="Newsletter signup"]';
const SCROLLED = 'dialog[aria-label="Free delivery"]';

const OPEN_TIMEOUT = { timeout: 5000 };

test.describe( 'Popup', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );
		await page.evaluate( ( key ) => {
			window.localStorage.removeItem( key );

			const spacer = document.createElement( 'div' );

			spacer.style.height = '3000px';
			document.body.append( spacer );
		}, KEY );
		await page.reload();
		await page.evaluate( () => {
			const spacer = document.createElement( 'div' );

			spacer.style.height = '3000px';
			document.body.append( spacer );
		} );
	} );

	test( 'opens after its delay, and as a modal', async ( { page } ) => {
		const dialog = page.locator( DELAYED );

		await expect( dialog ).toBeHidden();
		await expect( dialog ).toBeVisible( OPEN_TIMEOUT );

		// Modal, not merely open: showModal() is what puts it in the top layer
		// and makes the rest of the page inert.
		expect(
			await dialog.evaluate( ( el ) => el.matches( ':modal' ) )
		).toBe( true );

		// And focus is inside it.
		expect(
			await page.evaluate(
				() => !! document.activeElement?.closest( 'dialog' )
			)
		).toBe( true );
	} );

	test( 'traps the page behind it', async ( { page } ) => {
		await expect( page.locator( DELAYED ) ).toBeVisible( OPEN_TIMEOUT );

		/*
		 * The header's search button is a real, focusable control outside the
		 * dialog. While a modal is open the browser must refuse to focus it.
		 */
		const reached = await page.evaluate( () => {
			const outside = document.querySelector( 'header button, header a' );

			outside?.focus();

			return outside === document.activeElement;
		} );

		expect( reached ).toBe( false );
	} );

	test( 'closes on Escape and gives focus back', async ( { page } ) => {
		const dialog = page.locator( DELAYED );

		await expect( dialog ).toBeVisible( OPEN_TIMEOUT );

		await page.keyboard.press( 'Escape' );

		await expect( dialog ).toBeHidden();

		/*
		 * Focus is out of the closed dialog and back on the document. It lands
		 * on the body rather than on a control, and that is correct here: the
		 * browser returns focus to whatever had it when showModal() was called,
		 * and this popup opened itself on a timer while nothing was focused.
		 * What matters is that focus is not stranded inside an element that is
		 * now display: none, which is where a hand-rolled modal leaves it.
		 */
		expect(
			await page.evaluate( () =>
				Boolean( document.activeElement?.closest( 'dialog' ) )
			)
		).toBe( false );

		// And Tab moves on from there rather than doing nothing.
		await page.keyboard.press( 'Tab' );

		expect(
			await page.evaluate(
				() => document.activeElement !== document.body
			)
		).toBe( true );
	} );

	test( 'closes on the close button', async ( { page } ) => {
		const dialog = page.locator( DELAYED );

		await expect( dialog ).toBeVisible( OPEN_TIMEOUT );

		await page.getByRole( 'button', { name: 'Close the offer' } ).click();

		await expect( dialog ).toBeHidden();
	} );

	test( 'closes on a backdrop click, but not on one inside it', async ( {
		page,
	} ) => {
		const dialog = page.locator( DELAYED );

		await expect( dialog ).toBeVisible( OPEN_TIMEOUT );

		// Inside the dialog's own box, in its padding rather than on a control.
		// A click there reports the dialog as its target, exactly as a backdrop
		// click does, which is why the handler measures instead of comparing.
		const box = await dialog.boundingBox();

		await page.mouse.click( box.x + 4, box.y + 4 );
		await expect( dialog ).toBeVisible();

		await page.mouse.click( 4, 4 );
		await expect( dialog ).toBeHidden();
	} );

	test( 'shows once per visitor when asked to', async ( { page } ) => {
		await expect( page.locator( DELAYED ) ).toBeVisible( OPEN_TIMEOUT );

		expect(
			await page.evaluate(
				( key ) => window.localStorage.getItem( key ),
				KEY
			)
		).toBe( 'seen' );

		// Recorded on opening, not on closing: someone who ignored it and moved
		// on has still been shown it.
		await page.reload();

		const dialog = page.locator( DELAYED );

		await page.waitForTimeout( 2000 );
		await expect( dialog ).toBeHidden();
	} );

	test( 'a second popup keeps its own trigger and its own rules', async ( {
		page,
	} ) => {
		const scrolled = page.locator( SCROLLED );

		// Not opened by the first one's delay.
		await expect( page.locator( DELAYED ) ).toBeVisible( OPEN_TIMEOUT );
		await expect( scrolled ).toBeHidden();

		await page.keyboard.press( 'Escape' );
		await expect( page.locator( DELAYED ) ).toBeHidden();

		await expect
			.poll( () =>
				page.evaluate( () => {
					window.scrollTo( { top: 900, behavior: 'instant' } );

					return window.scrollY;
				} )
			)
			.toBe( 900 );

		await expect( scrolled ).toBeVisible();

		// This one was told not to close on the backdrop.
		await page.mouse.click( 4, 4 );
		await expect( scrolled ).toBeVisible();

		await page.keyboard.press( 'Escape' );
		await expect( scrolled ).toBeHidden();
	} );

	test( 'the open dialog has no accessibility violations', async ( {
		page,
	} ) => {
		await expect( page.locator( DELAYED ) ).toBeVisible( OPEN_TIMEOUT );

		const { violations } = await new AxeBuilder( { page } )
			.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
			.analyze();

		expect( violations ).toEqual( [] );
	} );
} );
