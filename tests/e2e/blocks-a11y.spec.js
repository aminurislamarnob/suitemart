/**
 * Keyboard and ARIA behaviour for the interactive blocks.
 *
 * Tabs and the off-canvas panel implement two different WAI-ARIA patterns, and
 * both are the kind of thing that looks fine in a screenshot while being
 * unusable without a mouse. These assertions are the contract.
 *
 * Requires the fixture page created from tests/e2e/fixtures/blocks-page.html —
 * see tests/README.md.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

// Addressed by slug: the id is whatever the install assigned, and the query
// form works whatever the permalink structure is set to.
const PAGE = '/?pagename=suitemart-block-test';

test.beforeEach( async ( { page } ) => {
	await page.goto( PAGE );
} );

test.describe( 'Tabs', () => {
	const tab = '.sm-tabs__tab';
	const panel = '.sm-tabs__panel';

	test( 'exposes the tablist pattern', async ( { page } ) => {
		await expect(
			page.locator( '[role="tablist"]' ).first()
		).toBeVisible();

		const tabs = page.locator( tab );
		await expect( tabs ).toHaveCount( 3 );

		// Exactly one tab selected, exactly one panel visible.
		await expect(
			page.locator( `${ tab }[aria-selected="true"]` )
		).toHaveCount( 1 );
		await expect( page.locator( `${ panel }:not([hidden])` ) ).toHaveCount(
			1
		);
	} );

	test( 'only the selected tab is in the tab order', async ( { page } ) => {
		// Roving tabindex: without it, Tab walks through every tab before
		// reaching the panel content.
		await expect( page.locator( `${ tab }[tabindex="0"]` ) ).toHaveCount(
			1
		);
		await expect( page.locator( `${ tab }[tabindex="-1"]` ) ).toHaveCount(
			2
		);
	} );

	test( 'arrow keys move between tabs and wrap', async ( { page } ) => {
		const tabs = page.locator( tab );
		await tabs.first().focus();

		await page.keyboard.press( 'ArrowRight' );
		await expect( tabs.nth( 1 ) ).toBeFocused();
		await expect( tabs.nth( 1 ) ).toHaveAttribute(
			'aria-selected',
			'true'
		);

		await page.keyboard.press( 'ArrowRight' );
		await expect( tabs.nth( 2 ) ).toBeFocused();

		// Wraps back to the first.
		await page.keyboard.press( 'ArrowRight' );
		await expect( tabs.first() ).toBeFocused();

		// And backwards from the first to the last.
		await page.keyboard.press( 'ArrowLeft' );
		await expect( tabs.nth( 2 ) ).toBeFocused();
	} );

	test( 'Home and End jump to the ends', async ( { page } ) => {
		const tabs = page.locator( tab );
		await tabs.nth( 1 ).focus();

		await page.keyboard.press( 'End' );
		await expect( tabs.nth( 2 ) ).toBeFocused();

		await page.keyboard.press( 'Home' );
		await expect( tabs.first() ).toBeFocused();
	} );

	test( 'each tab points at the panel it controls', async ( { page } ) => {
		const tabs = await page.locator( tab ).all();

		for ( const button of tabs ) {
			const controls = await button.getAttribute( 'aria-controls' );
			const target = page.locator( `#${ controls }` );

			await expect( target ).toHaveCount( 1 );
			await expect( target ).toHaveAttribute( 'role', 'tabpanel' );
			await expect( target ).toHaveAttribute(
				'aria-labelledby',
				( await button.getAttribute( 'id' ) ) ?? ''
			);
		}
	} );
} );

test.describe( 'Off-canvas panel', () => {
	const trigger = '.sm-off-canvas-trigger';
	const panel = '.sm-off-canvas__panel';
	const root = '.sm-off-canvas';

	test( 'opens, traps focus, and closes on Escape', async ( { page } ) => {
		const button = page.locator( trigger ).first();

		await expect( button ).toHaveAttribute( 'aria-expanded', 'false' );

		await button.click();
		await expect( page.locator( root ) ).toHaveClass( /is-open/ );
		await expect( button ).toHaveAttribute( 'aria-expanded', 'true' );

		// Focus must move into the dialog, or a keyboard user is left behind it.
		// It moves on the next animation frame once the panel is visible, so
		// poll rather than checking once — an immediate assertion races the
		// frame and fails intermittently.
		await expect
			.poll(
				() =>
					page.evaluate(
						( sel ) =>
							!! document
								.querySelector( sel )
								?.contains( document.activeElement ),
						panel
					),
				{ message: 'focus should move inside the dialog' }
			)
			.toBe( true );

		await page.keyboard.press( 'Escape' );

		await expect( page.locator( root ) ).not.toHaveClass( /is-open/ );
		// Focus returns to the control that opened it.
		await expect( button ).toBeFocused();
	} );

	test( 'is a labelled modal dialog', async ( { page } ) => {
		await page.locator( trigger ).first().click();

		const dialog = page.locator( panel );
		await expect( dialog ).toHaveAttribute( 'role', 'dialog' );
		await expect( dialog ).toHaveAttribute( 'aria-modal', 'true' );

		const labelledBy = await dialog.getAttribute( 'aria-labelledby' );
		await expect( page.locator( `#${ labelledBy }` ) ).toHaveText(
			'Filters'
		);
	} );

	test( 'is inert while closed', async ( { page } ) => {
		// Content behind a closed overlay must not be reachable by Tab.
		await expect( page.locator( panel ) ).toHaveAttribute( 'inert', '' );
	} );

	test( 'locks page scroll while open', async ( { page } ) => {
		await page.locator( trigger ).first().click();

		await expect( page.locator( 'html' ) ).toHaveClass(
			/sm-has-overlay-open/
		);

		await page.keyboard.press( 'Escape' );

		await expect( page.locator( 'html' ) ).not.toHaveClass(
			/sm-has-overlay-open/
		);
	} );
} );

test.describe( 'Live search', () => {
	test( 'is a combobox that works as a plain form', async ( { page } ) => {
		const input = page.locator( '.sm-search__input' );

		await expect( input ).toHaveAttribute( 'role', 'combobox' );
		await expect( input ).toHaveAttribute( 'aria-expanded', 'false' );

		// The enhancement sits on a real form, so search still works when the
		// module has not loaded.
		const form = page.locator( '.sm-search__form' );
		await expect( form ).toHaveAttribute( 'method', 'get' );
		await expect( input ).toHaveAttribute( 'name', 's' );
	} );
} );

test.describe( 'Slider', () => {
	test( 'is a labelled carousel that works before enhancement', async ( {
		page,
	} ) => {
		const slider = page.locator( '.sm-slider' );

		await expect( slider ).toHaveAttribute(
			'aria-roledescription',
			'carousel'
		);
		await expect( slider ).toHaveAttribute( 'aria-label', 'Test carousel' );

		// Every slide is in the DOM regardless of whether Swiper loaded.
		await expect( page.locator( '.sm-slider__slide' ) ).toHaveCount( 3 );
	} );

	test( 'arrow buttons are labelled', async ( { page } ) => {
		for ( const dir of [ 'prev', 'next' ] ) {
			const svg = page.locator( `.sm-slider__arrow--${ dir } svg` );
			await expect( svg ).toHaveAttribute( 'role', 'img' );
			await expect( svg ).toHaveAttribute( 'aria-label', /slide/i );
		}
	} );
} );

test( 'the whole block page has no accessibility violations', async ( {
	page,
} ) => {
	// Colour contrast cannot be measured while the page is still settling: a
	// fading element reads as its colour blended with whatever is behind it,
	// and text measured before its webfont arrives is a different size. Wait
	// for fonts and every running animation before scanning.
	await page.evaluate( () => document.fonts.ready );
	await page.evaluate( () =>
		Promise.all(
			document
				.getAnimations()
				// An infinite animation — the marquee's loop — never resolves
				// `finished`, so awaiting it hangs the whole test rather than
				// reporting anything useful. A looping animation has no settled
				// state to wait for, so skip it.
				.filter(
					( animation ) =>
						animation.effect?.getComputedTiming()?.iterations !==
						Infinity
				)
				.map( ( animation ) => animation.finished.catch( () => {} ) )
		)
	);

	const { violations } = await new AxeBuilder( { page } )
		.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
		.analyze();

	expect( violations ).toEqual( [] );
} );
