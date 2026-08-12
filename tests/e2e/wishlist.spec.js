/**
 * Wishlist behaviour.
 *
 * The wishlist lives in the visitor's browser, which is the decision these
 * tests exist to protect. Two consequences follow and both are easy to break:
 * the server renders every button as "not saved" and the browser must correct
 * it, and nothing may be written to a cookie — a per-visitor cookie would stop
 * a full-page cache serving one copy of a page to everybody.
 *
 * The product used here is created by global-setup.js.
 */

const { test, expect } = require( '@playwright/test' );

const PRODUCT = process.env.SUITEMART_PRODUCT_URL || '/?post_type=product&p=14';
const BUTTON = '.sm-wishlist-button';

// The stored id is the product's, which differs per install — read it off the
// button rather than hardcoding the one this was written against.
const productId = ( page ) =>
	page
		.locator( BUTTON )
		.first()
		.evaluate( ( el ) => JSON.parse( el.dataset.wpContext ).productId );

const readStored = ( page ) =>
	page.evaluate( () =>
		JSON.parse(
			window.localStorage.getItem( 'suitemart/wishlist' ) ?? '[]'
		)
	);

test.beforeEach( async ( { page } ) => {
	await page.goto( PRODUCT );
	// Each test starts from an empty list, whatever the last one left behind.
	await page.evaluate( () =>
		window.localStorage.removeItem( 'suitemart/wishlist' )
	);
	await page.reload();
} );

test( 'renders unsaved and is a labelled toggle', async ( { page } ) => {
	const button = page.locator( BUTTON ).first();

	// aria-pressed rather than colour: the saved state has to reach a screen
	// reader, and a filled heart does not.
	await expect( button ).toHaveAttribute( 'aria-pressed', 'false' );
	await expect( button ).toHaveAttribute( 'aria-label', /add to wishlist/i );
} );

test( 'saves the product and says so', async ( { page } ) => {
	const button = page.locator( BUTTON ).first();

	await button.click();

	await expect( button ).toHaveAttribute( 'aria-pressed', 'true' );
	await expect( button ).toHaveAttribute(
		'aria-label',
		/remove from wishlist/i
	);
	await expect( button ).toHaveClass( /is-saved/ );

	expect( await readStored( page ) ).toEqual( [ await productId( page ) ] );
} );

test( 'the saved state survives a reload', async ( { page } ) => {
	// The whole point of storing it: the server renders "not saved" and the
	// browser has to correct that on every page load.
	await page.locator( BUTTON ).first().click();
	await page.reload();

	await expect( page.locator( BUTTON ).first() ).toHaveAttribute(
		'aria-pressed',
		'true'
	);
} );

test( 'clicking again removes it', async ( { page } ) => {
	const button = page.locator( BUTTON ).first();

	await button.click();
	await expect( button ).toHaveAttribute( 'aria-pressed', 'true' );

	await button.click();
	await expect( button ).toHaveAttribute( 'aria-pressed', 'false' );

	expect( await readStored( page ) ).toEqual( [] );
} );

test( 'sets no cookie', async ( { page, context } ) => {
	// A cookie that varies per visitor defeats full-page caching, which the
	// caching-plugin integrations depend on. This is the assertion that keeps
	// the storage decision honest.
	await page.locator( BUTTON ).first().click();
	await expect( page.locator( BUTTON ).first() ).toHaveAttribute(
		'aria-pressed',
		'true'
	);

	const cookies = await context.cookies();
	const ours = cookies.filter( ( cookie ) =>
		/suitemart|wishlist/i.test( cookie.name )
	);

	expect( ours ).toEqual( [] );
} );

test( 'works when storage is unavailable', async ( { browser } ) => {
	// Blocked site data throws on access rather than returning null. The button
	// must stay operable rather than taking the page down with it.
	const context = await browser.newContext();
	const page = await context.newPage();

	await page.addInitScript( () => {
		Object.defineProperty( window, 'localStorage', {
			get() {
				throw new Error( 'Storage is disabled' );
			},
		} );
	} );

	const errors = [];
	page.on( 'pageerror', ( error ) => errors.push( error.message ) );

	await page.goto( PRODUCT );

	const button = page.locator( BUTTON ).first();
	await button.click();

	// Nothing can be remembered, so it stays unsaved rather than showing a
	// filled heart the next page load would contradict — and it does not throw.
	await expect( button ).toHaveAttribute( 'aria-pressed', 'false' );
	expect( errors ).toEqual( [] );

	await context.close();
} );
