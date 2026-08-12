/**
 * Comparison table behaviour.
 *
 * Everything in this table is rendered by the browser, because the list it
 * shows is in the browser. That makes three things worth protecting: the page
 * a search engine or a cache sees contains no visitor's list; the table appears
 * and disappears in step with that list; and a failed fetch says so rather than
 * showing an empty table, which would look exactly like a cleared list.
 *
 * The page and the product come from global-setup.js.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const COMPARE = process.env.SUITEMART_COMPARE_URL || '/?page_id=16';
const PRODUCT = process.env.SUITEMART_PRODUCT_URL || '/?post_type=product&p=14';
const KEY = 'suitemart/compare';

const TABLE = '.sm-compare-table__scroll';
const EMPTY = '.sm-compare-table__empty';
const ROWS = '.sm-compare-table tbody tr';

/**
 * Loads the compare page with a given stored list.
 *
 * @param {import('@playwright/test').Page} page Page under test.
 * @param {number[]}                        ids  Product ids to store.
 */
const visitWith = async ( page, ids ) => {
	await page.goto( COMPARE );
	await page.evaluate(
		( [ key, value ] ) => window.localStorage.setItem( key, value ),
		[ KEY, JSON.stringify( ids ) ]
	);
	await page.reload();
};

/**
 * Reads the id of the product created for the suite.
 *
 * @param {import('@playwright/test').Page} page Page under test.
 * @return {Promise<number>} Product id.
 */
const testProductId = async ( page ) => {
	await page.goto( PRODUCT );

	return page
		.locator( '.sm-compare-button' )
		.first()
		.evaluate( ( el ) => JSON.parse( el.dataset.wpContext ).productId );
};

test( 'shows the empty state and no table before anything is added', async ( {
	page,
} ) => {
	await visitWith( page, [] );

	await expect( page.locator( EMPTY ) ).toBeVisible();
	await expect( page.locator( TABLE ) ).toBeHidden();
} );

test( 'the served HTML contains no products', async ( { page, request } ) => {
	// A cached copy of this page is handed to every visitor, so it must not
	// carry one visitor's comparison. This checks the response itself rather
	// than the rendered DOM, which is what a cache stores.
	const id = await testProductId( page );
	const response = await request.get( COMPARE );
	const html = await response.text();

	expect( html ).not.toContain( `"productId":${ id }` );
	expect( html ).not.toContain( 'Test product' );
	expect( html ).toContain( 'sm-compare-table' );
} );

test( 'renders a row for a stored product', async ( { page } ) => {
	const id = await testProductId( page );

	await visitWith( page, [ id ] );

	await expect( page.locator( TABLE ) ).toBeVisible();
	await expect( page.locator( EMPTY ) ).toBeHidden();
	await expect( page.locator( ROWS ) ).toHaveCount( 1 );

	const row = page.locator( ROWS ).first();

	// The product name is a row header, so a screen reader announces which
	// product each cell belongs to.
	await expect( row.locator( 'th[scope="row"] a' ) ).toHaveAttribute(
		'href',
		/product/
	);

	await expect( row ).toContainText( '$19.99' );

	// One currency symbol, not two. The Store API returns both a
	// `currency_symbol` and a `currency_prefix` that already contains it, and
	// using both produced "$$19.99".
	expect( await row.innerText() ).not.toContain( '$$' );
} );

test( 'removing a row updates the stored list and the table', async ( {
	page,
} ) => {
	const id = await testProductId( page );

	await visitWith( page, [ id ] );
	await expect( page.locator( ROWS ) ).toHaveCount( 1 );

	const remove = page.locator( '.sm-compare-table__remove' ).first();

	// The control has to name the product it removes; "Remove" alone is
	// ambiguous in a table of four.
	await expect( remove ).toHaveAttribute( 'aria-label', /remove.+/i );

	await remove.click();

	await expect( page.locator( ROWS ) ).toHaveCount( 0 );
	await expect( page.locator( EMPTY ) ).toBeVisible();

	expect(
		await page.evaluate(
			( key ) => window.localStorage.getItem( key ),
			KEY
		)
	).toBe( '[]' );
} );

test( 'drops products that no longer exist', async ( { page } ) => {
	// A product deleted since it was added would otherwise occupy one of the
	// four slots forever, with nothing on screen to remove.
	const id = await testProductId( page );

	await visitWith( page, [ id, 99999 ] );

	await expect( page.locator( ROWS ) ).toHaveCount( 1 );

	await expect
		.poll( () =>
			page.evaluate( ( key ) => window.localStorage.getItem( key ), KEY )
		)
		.toBe( JSON.stringify( [ id ] ) );
} );

test( 'says so when the products cannot be loaded', async ( { page } ) => {
	const id = await testProductId( page );

	// Matched by predicate rather than by glob: with plain permalinks the REST
	// route travels in a query parameter, url-encoded, so a path glob never
	// matches it.
	await page.route(
		( url ) =>
			decodeURIComponent( url.href ).includes( 'wc/store/v1/products' ),
		( route ) => route.fulfill( { status: 500, body: 'nope' } )
	);

	await visitWith( page, [ id ] );

	// An empty table here would be indistinguishable from an empty list.
	await expect( page.locator( '.sm-compare-table__status' ) ).toContainText(
		/could not be loaded/i
	);
} );

test( 'the populated table has no accessibility violations', async ( {
	page,
} ) => {
	// Worth running with rows present rather than empty: the header
	// associations, the row headers and the icon-only remove buttons only exist
	// once the browser has built the table.
	const id = await testProductId( page );

	await visitWith( page, [ id ] );
	await expect( page.locator( ROWS ) ).toHaveCount( 1 );

	const { violations } = await new AxeBuilder( { page } )
		.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
		.analyze();

	expect( violations ).toEqual( [] );
} );
