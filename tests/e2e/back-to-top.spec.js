/**
 * Back to top.
 *
 * The block is in `parts/footer.html`, so it is on every page and these run
 * against the shared fixture page.
 *
 * Both directions are asserted, deliberately. The first version of this block
 * only worked one way: it closed over a context proxy and read it back from a
 * scroll listener the store had not invoked, where reads go stale, so the
 * button appeared once and never left. A test that only scrolled down would
 * have passed.
 */

const { test, expect } = require( '@playwright/test' );

const PAGE = process.env.SUITEMART_FIXTURE_URL || '/?pagename=suitemart-block-test';

const BLOCK = '.sm-back-to-top';
const BUTTON = '.sm-back-to-top__button';

/**
 * Scrolls to an absolute offset and waits for the page to settle there.
 *
 * `instant` on purpose: the theme sets `scroll-behavior: smooth`, so an
 * ordinary scrollTo returns long before the position it asked for is reached
 * and every assertion after it reads the old offset.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @param {number}                          top  Offset in pixels.
 */
const scrollTo = async ( page, top ) => {
	await page.evaluate(
		( to ) => window.scrollTo( { top: to, behavior: 'instant' } ),
		top
	);

	await expect.poll( () => page.evaluate( () => window.scrollY ) ).toBe( top );
};

test.describe( 'Back to top', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );

		// The fixture page has to be taller than the viewport for any of this
		// to mean anything.
		await page.evaluate( () => {
			const spacer = document.createElement( 'div' );
			spacer.style.height = '3000px';
			document.body.append( spacer );
		} );
	} );

	test( 'is served out of the tab order', async ( { page } ) => {
		const button = page.locator( BUTTON );

		await expect( button ).toHaveAttribute( 'inert', '' );
		await expect( page.locator( BLOCK ) ).not.toHaveClass( /is-visible/ );
	} );

	test( 'appears past the threshold and goes away again', async ( {
		page,
	} ) => {
		const block = page.locator( BLOCK );
		const button = page.locator( BUTTON );

		await scrollTo( page, 1200 );

		await expect( block ).toHaveClass( /is-visible/ );
		await expect( button ).not.toHaveAttribute( 'inert', '' );

		// Back up above the threshold. This is the direction that was broken.
		await scrollTo( page, 100 );

		await expect( block ).not.toHaveClass( /is-visible/ );
		await expect( button ).toHaveAttribute( 'inert', '' );

		// And down again, so it is not one-way in the other sense either.
		await scrollTo( page, 1200 );
		await expect( block ).toHaveClass( /is-visible/ );
	} );

	test( 'returns the page to the top when pressed', async ( { page } ) => {
		await scrollTo( page, 1200 );

		await page.locator( BUTTON ).click();

		await expect.poll( () => page.evaluate( () => window.scrollY ) ).toBe(
			0
		);
	} );

	test( 'takes focus back to the top of the content', async ( { page } ) => {
		await scrollTo( page, 1200 );

		await page.locator( BUTTON ).focus();
		await page.keyboard.press( 'Enter' );

		await expect.poll( () => page.evaluate( () => window.scrollY ) ).toBe(
			0
		);

		/*
		 * The point of the whole block for a keyboard user. Without moving
		 * focus, the page scrolls to the top while the caret stays on a button
		 * at the bottom, and the next Tab jumps straight back down — the
		 * button looks like it did nothing.
		 */
		const focused = await page.evaluate( () => {
			const active = document.activeElement;

			return {
				id: active?.id,
				tag: active?.tagName,
				insideButton: Boolean( active?.closest( '.sm-back-to-top' ) ),
			};
		} );

		expect( focused.insideButton ).toBe( false );
		expect( [ 'wp--skip-link--target', undefined ] ).toContain(
			focused.id
		);
	} );
} );
