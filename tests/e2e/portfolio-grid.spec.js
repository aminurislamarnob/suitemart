/**
 * Portfolio grid.
 *
 * Four projects across two categories. What is worth checking in a browser is
 * the round trip: filter, then unfilter, and end up where you started —
 * a filter that only works one way passes any test that only presses it once.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const PAGE =
	process.env.SUITEMART_PORTFOLIO_URL ||
	'/?pagename=suitemart-portfolio-grid';

const ROOT = '.sm-portfolio-grid';
const ITEMS = '.sm-portfolio-grid__item';
const ALL = '.sm-portfolio-grid__filter[data-slug=""]';
const BRANDING = '.sm-portfolio-grid__filter[data-slug="branding"]';
const STATUS = '.sm-portfolio-grid [aria-live="polite"]';

test.describe( 'Portfolio grid', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );
	} );

	test( 'every project is in the page from the start', async ( { page } ) => {
		// All four, none hidden: the filtering is a hide, not a fetch, which is
		// what makes it instant and what makes the grid work with no JavaScript.
		await expect( page.locator( ITEMS ) ).toHaveCount( 4 );
		await expect( page.locator( `${ ITEMS }:visible` ) ).toHaveCount( 4 );
	} );

	test( 'filters to one category and back again', async ( { page } ) => {
		await page.locator( BRANDING ).click();

		await expect( page.locator( `${ ITEMS }:visible` ) ).toHaveCount( 2 );
		await expect(
			page.getByRole( 'link', { name: 'Harbour brand' } )
		).toBeVisible();
		await expect(
			page.getByRole( 'link', { name: 'Ceramic set' } )
		).toBeHidden();

		// The direction that a one-press test never reaches.
		await page.locator( ALL ).click();

		await expect( page.locator( `${ ITEMS }:visible` ) ).toHaveCount( 4 );
	} );

	test( 'the pressed filter says so', async ( { page } ) => {
		await expect( page.locator( ALL ) ).toHaveAttribute(
			'aria-pressed',
			'true'
		);
		await expect( page.locator( BRANDING ) ).toHaveAttribute(
			'aria-pressed',
			'false'
		);

		await page.locator( BRANDING ).click();

		await expect( page.locator( BRANDING ) ).toHaveAttribute(
			'aria-pressed',
			'true'
		);
		await expect( page.locator( ALL ) ).toHaveAttribute(
			'aria-pressed',
			'false'
		);
	} );

	test( 'announces what is being shown', async ( { page } ) => {
		const status = page.locator( STATUS );

		await expect( status ).toHaveText( 'Showing all projects' );

		await page.locator( BRANDING ).click();

		await expect( status ).toHaveText( 'Showing Branding' );

		await page.locator( ALL ).click();

		await expect( status ).toHaveText( 'Showing all projects' );
	} );

	test( 'works from the keyboard', async ( { page } ) => {
		await page.locator( BRANDING ).focus();
		await page.keyboard.press( 'Enter' );

		await expect( page.locator( `${ ITEMS }:visible` ) ).toHaveCount( 2 );

		await page.keyboard.press( 'Space' );

		// Still Branding: pressing the same filter twice is not a toggle back
		// to everything, which would leave the button reading "pressed" over an
		// unfiltered grid.
		await expect( page.locator( `${ ITEMS }:visible` ) ).toHaveCount( 2 );
	} );

	test( 'has no accessibility violations, filtered or not', async ( {
		page,
	} ) => {
		await page.evaluate( () => document.fonts.ready );

		/*
		 * Contrast cannot be measured through a running transition: the filter
		 * buttons fade between their pressed and unpressed fills, and axe
		 * caught the "All" button mid-fade, reading its label against a grey
		 * that exists for 250ms and nowhere in the design. Finishing the
		 * animations inside a poll settles it — a headless browser that never
		 * paints can leave a transition frozen at its first frame forever, so
		 * waiting it out is not an option.
		 */
		const settle = () =>
			expect
				.poll( () =>
					page.evaluate( () => {
						for ( const animation of document.getAnimations() ) {
							try {
								animation.finish();
							} catch {
								// Cancelled or fill-less; nothing to finish.
							}
						}

						return document
							.getAnimations()
							.every( ( a ) => a.playState !== 'running' );
					} )
				)
				.toBe( true );

		await settle();

		const scan = async () =>
			(
				await new AxeBuilder( { page } )
					.include( ROOT )
					.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
					.analyze()
			).violations;

		expect( await scan() ).toEqual( [] );

		await page.locator( BRANDING ).click();
		await settle();

		expect( await scan() ).toEqual( [] );
	} );
} );
