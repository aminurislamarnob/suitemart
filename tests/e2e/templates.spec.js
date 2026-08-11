/**
 * Template smoke tests.
 *
 * Loads every front-end route the theme provides and asserts it renders without
 * PHP notices, console errors, or a missing header and footer. This is the
 * cheapest test that would have caught most of the breakages a theme ships with.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const routes = [
	{ name: 'home', path: '/' },
	{ name: 'blog', path: '/?page_id=0' },
	{ name: 'search', path: '/?s=test' },
	// Query a post id that cannot exist, so WordPress serves the 404 template.
	// A pretty-permalink path would be handled by the web server instead on
	// installs that have not enabled rewrites.
	{ name: '404', path: '/?p=99999999' },
	{ name: 'shop', path: '/?post_type=product' },
];

test.describe( 'Templates render', () => {
	for ( const route of routes ) {
		test( `${ route.name } renders cleanly`, async ( { page } ) => {
			const consoleErrors = [];
			page.on( 'console', ( message ) => {
				if ( message.type() !== 'error' ) {
					return;
				}

				// The 404 route is *meant* to return 404, and the browser reports
				// the document's own status as a console error. Everything else
				// on that page still has to be clean.
				if (
					route.name === '404' &&
					message.text().includes( 'status of 404' )
				) {
					return;
				}

				consoleErrors.push( message.text() );
			} );

			const response = await page.goto( route.path );
			const body = await page.content();

			// 404 is the expected status for the missing-route case; every other
			// route must resolve.
			if ( route.name !== '404' ) {
				expect( response?.status() ).toBeLessThan( 400 );
			}

			// WordPress prints these inline when something is wrong, and they are
			// invisible unless you look for them.
			expect( body ).not.toContain( 'Fatal error' );
			expect( body ).not.toContain( 'Warning:' );
			expect( body ).not.toContain( 'Notice:' );
			expect( body ).not.toContain( 'Deprecated:' );

			// Scope to the theme's own landmarks. A bare `footer` locator also
			// matches WooCommerce's filter-overlay footer, which is legitimately
			// hidden until the overlay opens.
			await expect(
				page.locator( 'header.wp-block-template-part' )
			).toBeVisible();
			await expect(
				page.locator( 'footer.wp-block-template-part' )
			).toBeVisible();

			expect( consoleErrors ).toEqual( [] );
		} );
	}

	test( 'the page never scrolls horizontally', async ( { page } ) => {
		await page.goto( '/' );

		// A full-bleed mega panel is the usual culprit; catching it here is much
		// cheaper than a bug report about "the page wobbles on mobile".
		const overflows = await page.evaluate(
			() =>
				document.documentElement.scrollWidth >
				document.documentElement.clientWidth
		);

		expect( overflows ).toBe( false );
	} );

	test( 'the home page has no accessibility violations', async ( {
		page,
	} ) => {
		await page.goto( '/' );

		const { violations } = await new AxeBuilder( { page } )
			.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
			.analyze();

		expect( violations ).toEqual( [] );
	} );
} );
