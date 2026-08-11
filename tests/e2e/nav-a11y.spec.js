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

/**
 * Opens the mobile drawer when the nav is in its overlay layout.
 *
 * Below the navigation breakpoint every menu item lives inside a drawer that
 * starts closed, so the submenu triggers these tests operate are not on screen
 * until the hamburger is pressed. This spec runs on both viewports and the
 * contract is the same on each; only the route to the trigger differs.
 *
 * CSS owns the breakpoint — `--sm-nav-is-overlay` is set per breakpoint in
 * src/navigation/style.scss — so the layout is read back from it rather than
 * duplicating the pixel value here, where it would silently drift.
 *
 * @param {import('@playwright/test').Page} page Page under test.
 */
const openDrawerIfOverlay = async ( page ) => {
	const isOverlay = await page
		.locator( nav )
		.first()
		.evaluate(
			( el ) =>
				getComputedStyle( el )
					.getPropertyValue( '--sm-nav-is-overlay' )
					.trim() === '1'
		);

	if ( ! isOverlay ) {
		return;
	}

	await page.locator( '.sm-nav__toggle' ).first().click();
	await expect( page.locator( trigger ).first() ).toBeVisible();
};

test.describe( 'Mega menu keyboard operation', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( '/' );
		await expect( page.locator( nav ).first() ).toBeVisible();
		await openDrawerIfOverlay( page );
	} );

	test( 'the trigger is a button and reports its state', async ( {
		page,
	} ) => {
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
		// Desktop only, and deliberately so. In the overlay layout the drawer
		// covers the page, so there is no page content to click outside onto —
		// that is what a modal drawer is. Escape is the mobile equivalent and
		// has its own test above.
		const isOverlay = await page
			.locator( nav )
			.first()
			.evaluate(
				( el ) =>
					getComputedStyle( el )
						.getPropertyValue( '--sm-nav-is-overlay' )
						.trim() === '1'
			);

		test.skip(
			isOverlay,
			'The drawer covers the page in the overlay layout; Escape closes it instead.'
		);

		const button = page.locator( trigger ).first();

		await button.click();
		await expect( button ).toHaveAttribute( 'aria-expanded', 'true' );

		await page
			.locator( 'footer' )
			.first()
			.click( { position: { x: 5, y: 5 } } );

		await expect( button ).toHaveAttribute( 'aria-expanded', 'false' );
	} );

	test( 'the whole menu is reachable by keyboard alone', async ( {
		page,
	} ) => {
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

	test( 'nothing on the page shows through the open panel', async ( {
		page,
	} ) => {
		// What matters is that page content cannot be read through the menu —
		// not which element paints the background. In the desktop layout the
		// panel paints its own; inside the mobile drawer it deliberately does
		// not, because the drawer behind it already has one. Asserting on the
		// panel alone therefore fails on mobile for a design that is correct.
		//
		// So walk outwards from the panel and require an opaque backdrop
		// somewhere at or above it, still within the nav.
		await page.locator( trigger ).first().click();
		await settle( page );

		const opaqueAncestor = await page
			.locator( '.sm-mega-panel' )
			.first()
			.evaluate( ( el, navSelector ) => {
				const isOpaque = ( colour ) => {
					const match = colour.match( /rgba?\(([^)]+)\)/ );

					if ( ! match ) {
						return false;
					}

					const parts = match[ 1 ].split( ',' ).map( Number );

					// A missing alpha channel means fully opaque.
					return ( parts[ 3 ] ?? 1 ) === 1;
				};

				let node = el;

				while ( node ) {
					if (
						isOpaque( getComputedStyle( node ).backgroundColor )
					) {
						return node.className || node.tagName;
					}

					if ( node.matches( navSelector ) ) {
						break;
					}

					node = node.parentElement;
				}

				return null;
			}, nav );

		expect(
			opaqueAncestor,
			'the open panel has no opaque backdrop, so page content reads through it'
		).not.toBeNull();
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
		await settle( page );

		expect( ( await scan() ).violations ).toEqual( [] );
	} );
} );

/**
 * Waits for every animation inside the navigation to finish.
 *
 * Colour-contrast results are meaningless mid-transition: a panel fading in is
 * measured as its colour blended with whatever is behind it, which fails a
 * check that the settled state passes comfortably.
 *
 * @param {import('@playwright/test').Page} page Page under test.
 */
async function settle( page ) {
	await page
		.locator( nav )
		.first()
		.evaluate( ( el ) =>
			Promise.all(
				el
					.getAnimations( { subtree: true } )
					// An infinite animation — the marquee's loop, for one —
					// never resolves `finished`, so awaiting it hangs until the
					// test times out. Only finite ones are worth waiting for;
					// a looping animation has no settled state to wait for.
					.filter(
						( animation ) =>
							animation.effect?.getComputedTiming()
								?.iterations !== Infinity
					)
					.map( ( animation ) =>
						animation.finished.catch( () => {} )
					)
			)
		);
}
