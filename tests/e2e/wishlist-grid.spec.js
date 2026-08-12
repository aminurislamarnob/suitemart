/**
 * Wishlist grid behaviour.
 *
 * Like the comparison table, everything here is rendered by the browser from a
 * list the server cannot see, so the same three properties are protected: the
 * cached response carries nobody's list, the grid tracks that list as it
 * changes, and a failed fetch says so rather than showing an empty grid that
 * reads as "you saved nothing".
 *
 * What the grid adds is semantics — it is a real list, so a screen reader
 * announces how many products are in it — and pruning, since a wishlist has no
 * ceiling and can accumulate ids for products that no longer exist.
 *
 * The page and the product come from global-setup.js.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const WISHLIST = process.env.SUITEMART_WISHLIST_URL || '/?page_id=17';
const PRODUCT = process.env.SUITEMART_PRODUCT_URL || '/?post_type=product&p=14';
const KEY = 'suitemart/wishlist';

const LIST = '.sm-wishlist-grid__list';
const EMPTY = '.sm-wishlist-grid__empty';
const ITEMS = '.sm-wishlist-grid__item';

/**
 * Loads the wishlist page with a given stored list.
 *
 * @param {import('@playwright/test').Page} page Page under test.
 * @param {number[]}                        ids  Product ids to store.
 */
const visitWith = async ( page, ids ) => {
	await page.goto( WISHLIST );
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
		.locator( '.sm-wishlist-button' )
		.first()
		.evaluate( ( el ) => JSON.parse( el.dataset.wpContext ).productId );
};

test( 'shows the empty state before anything is saved', async ( { page } ) => {
	await visitWith( page, [] );

	await expect( page.locator( EMPTY ) ).toBeVisible();
	await expect( page.locator( LIST ) ).toBeHidden();
} );

test( 'the served HTML contains no products', async ( { page, request } ) => {
	// A full-page cache hands this response to every visitor, so it must carry
	// nobody's saved list.
	const id = await testProductId( page );
	const response = await request.get( WISHLIST );
	const html = await response.text();

	expect( html ).not.toContain( `"productId":${ id }` );
	expect( html ).toContain( 'sm-wishlist-grid' );
} );

test( 'renders a card for a saved product', async ( { page } ) => {
	const id = await testProductId( page );

	await visitWith( page, [ id ] );

	await expect( page.locator( LIST ) ).toBeVisible();
	await expect( page.locator( ITEMS ) ).toHaveCount( 1 );

	const item = page.locator( ITEMS ).first();

	await expect( item.locator( '.sm-wishlist-grid__link' ) ).toHaveAttribute(
		'href',
		/product/
	);
	await expect( item ).toContainText( '$19.99' );
	expect( await item.innerText() ).not.toContain( '$$' );
} );

test( 'is a list, so its length is announced', async ( { page } ) => {
	// A grid of cards built from divs tells a screen-reader user nothing about
	// how many products they saved.
	const id = await testProductId( page );

	await visitWith( page, [ id ] );

	await expect( page.locator( `${ LIST } > li` ) ).toHaveCount( 1 );
} );

test( 'removing a card updates storage and the grid', async ( { page } ) => {
	const id = await testProductId( page );

	await visitWith( page, [ id ] );
	await expect( page.locator( ITEMS ) ).toHaveCount( 1 );

	const remove = page.locator( '.sm-wishlist-grid__remove' ).first();

	await expect( remove ).toHaveAttribute( 'aria-label', /remove.+/i );
	await remove.click();

	await expect( page.locator( ITEMS ) ).toHaveCount( 0 );
	await expect( page.locator( EMPTY ) ).toBeVisible();

	expect(
		await page.evaluate(
			( key ) => window.localStorage.getItem( key ),
			KEY
		)
	).toBe( '[]' );
} );

test( 'drops products that no longer exist', async ( { page } ) => {
	const id = await testProductId( page );

	await visitWith( page, [ id, 99999 ] );

	await expect( page.locator( ITEMS ) ).toHaveCount( 1 );

	await expect
		.poll( () =>
			page.evaluate( ( key ) => window.localStorage.getItem( key ), KEY )
		)
		.toBe( JSON.stringify( [ id ] ) );
} );

test( 'says so when the products cannot be loaded', async ( { page } ) => {
	const id = await testProductId( page );

	await page.route(
		( url ) =>
			decodeURIComponent( url.href ).includes( 'wc/store/v1/products' ),
		( route ) => route.fulfill( { status: 500, body: 'nope' } )
	);

	await visitWith( page, [ id ] );

	await expect( page.locator( '.sm-wishlist-grid__status' ) ).toContainText(
		/could not be loaded/i
	);
} );

test( 'the populated grid has no accessibility violations', async ( {
	page,
} ) => {
	const id = await testProductId( page );

	await visitWith( page, [ id ] );
	await expect( page.locator( ITEMS ) ).toHaveCount( 1 );

	const { violations } = await new AxeBuilder( { page } )
		.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
		.analyze();

	expect( violations ).toEqual( [] );
} );
