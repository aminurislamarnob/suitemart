/**
 * Navigation accessibility.
 *
 * Decision 14 replaced core/navigation with our own block family, which means we
 * own the keyboard and ARIA behaviour core would otherwise have provided. This
 * spec is the executable form of that contract — if it passes, the mega menu is
 * operable without a mouse.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const nav = '.sm-nav';
const trigger = '.sm-nav-item__trigger';
const panel = '.sm-nav-item__panel-wrap';

test.describe( 'Mega menu keyboard operation', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( '/' );
		await expect( page.locator( nav ).first() ).toBeVisible();
	} );

	test( 'the trigger is a button and reports its state', async ( { page } ) => {
		const button = page.locator( trigger ).first();

		// A control that opens a panel must be a button, not a link — a link
		// promises navigation that never happens.
		await expect( button ).toHaveJSProperty( 'tagName', 'BUTTON' );
		await expect( button ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( button ).toHaveAttribute( 'aria-controls', /.+/ );
	} );

	test( 'click toggles the panel and updates aria-expanded', async ( {
		page,
	} ) => {
		const button = page.locator( trigger ).first();
		const target = page.locator( panel ).first();

		await button.click();
		await expect( button ).toHaveAttribute( 'aria-expanded', 'true' );
		await expect( target ).toBeVisible();

		await button.click();
		await expect( button ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( target ).toBeHidden();
	} );

	test( 'Escape closes the panel and returns focus to the trigger', async ( {
		page,
	} ) => {
		const button = page.locator( trigger ).first();

		await button.click();
		await expect( button ).toHaveAttribute( 'aria-expanded', 'true' );

		await page.keyboard.press( 'Escape' );

		await expect( button ).toHaveAttribute( 'aria-expanded', 'false' );
		// Returning focus is the step most implementations skip, and the one
		// keyboard users notice immediately when it is missing.
		await expect( button ).toBeFocused();
	} );

	test( 'ArrowDown opens the panel and moves focus into it', async ( {
		page,
	} ) => {
		const button = page.locator( trigger ).first();

		await button.focus();
		await page.keyboard.press( 'ArrowDown' );

		await expect( button ).toHaveAttribute( 'aria-expanded', 'true' );

		const focusedInPanel = await page.evaluate( ( panelSelector ) => {
			const openPanel = document.querySelector(
				`${ panelSelector }:not([hidden])`
			);
			return !! openPanel && openPanel.contains( document.activeElement );
		}, panel );

		expect( focusedInPanel ).toBe( true );
	} );

	test( 'clicking outside closes the panel', async ( { page } ) => {
		const button = page.locator( trigger ).first();

		await button.click();
		await expect( button ).toHaveAttribute( 'aria-expanded', 'true' );

		await page.locator( 'footer' ).first().click( { position: { x: 5, y: 5 } } );

		await expect( button ).toHaveAttribute( 'aria-expanded', 'false' );
	} );

	test( 'the whole menu is reachable by keyboard alone', async ( { page } ) => {
		await page.keyboard.press( 'Tab' );

		// Walk forward until focus lands on a navigation control, proving the
		// menu is in the natural tab order rather than requiring a pointer.
		let reached = false;

		for ( let i = 0; i < 25 && ! reached; i++ ) {
			reached = await page.evaluate(
				( navSelector ) =>
					!! document.activeElement?.closest( navSelector ),
				nav
			);

			if ( ! reached ) {
				await page.keyboard.press( 'Tab' );
			}
		}

		expect( reached ).toBe( true );
	} );

	test( 'has no detectable accessibility violations, open or closed', async ( {
		page,
	} ) => {
		const scan = () =>
			new AxeBuilder( { page } )
				.include( nav )
				.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
				.analyze();

		expect( ( await scan() ).violations ).toEqual( [] );

		await page.locator( trigger ).first().click();

		expect( ( await scan() ).violations ).toEqual( [] );
	} );
} );
