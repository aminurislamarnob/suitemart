/**
 * Compare behaviour.
 *
 * The comparison list shares the wishlist's storage model, so the same two
 * properties are protected here: the server renders "not added" and the browser
 * corrects it, and nothing is written to a cookie.
 *
 * What is specific to compare is the ceiling. A table has a fixed number of
 * columns, so adding past the limit drops the oldest product — deliberately,
 * rather than refusing the click.
 *
 * The product used here is created by global-setup.js.
 */

const { test, expect } = require( '@playwright/test' );

const PRODUCT = process.env.SUITEMART_PRODUCT_URL || '/?post_type=product&p=14';
const BUTTON = '.sm-compare-button';
const KEY = 'suitemart/compare';

const readStored = ( page ) =>
	page.evaluate(
		( key ) => JSON.parse( window.localStorage.getItem( key ) ?? '[]' ),
		KEY
	);

const productId = ( page ) =>
	page
		.locator( BUTTON )
		.first()
		.evaluate( ( el ) => JSON.parse( el.dataset.wpContext ).productId );

test.beforeEach( async ( { page } ) => {
	await page.goto( PRODUCT );
	await page.evaluate(
		( key ) => window.localStorage.removeItem( key ),
		KEY
	);
	await page.reload();
} );

test( 'renders unadded and is a labelled toggle', async ( { page } ) => {
	const button = page.locator( BUTTON ).first();

	await expect( button ).toHaveAttribute( 'aria-pressed', 'false' );
	await expect( button ).toHaveAttribute( 'aria-label', /add to compare/i );
} );

test( 'adds the product and survives a reload', async ( { page } ) => {
	const button = page.locator( BUTTON ).first();
	const id = await productId( page );

	await button.click();

	await expect( button ).toHaveAttribute( 'aria-pressed', 'true' );
	await expect( button ).toHaveAttribute(
		'aria-label',
		/remove from compare/i
	);
	expect( await readStored( page ) ).toEqual( [ id ] );

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

test( 'adding past the limit drops the oldest product', async ( { page } ) => {
	// The limit is four, so a full list plus this product must come back four
	// long with the first entry gone — not five long, and not unchanged.
	await page.evaluate(
		( key ) =>
			window.localStorage.setItem(
				key,
				JSON.stringify( [ 901, 902, 903, 904 ] )
			),
		KEY
	);
	await page.reload();

	const id = await productId( page );
	await page.locator( BUTTON ).first().click();

	await expect( page.locator( BUTTON ).first() ).toHaveAttribute(
		'aria-pressed',
		'true'
	);

	expect( await readStored( page ) ).toEqual( [ 902, 903, 904, id ] );
} );

test( 'sets no cookie', async ( { page, context } ) => {
	await page.locator( BUTTON ).first().click();
	await expect( page.locator( BUTTON ).first() ).toHaveAttribute(
		'aria-pressed',
		'true'
	);

	const cookies = await context.cookies();
	const ours = cookies.filter( ( cookie ) =>
		/suitemart|compare/i.test( cookie.name )
	);

	expect( ours ).toEqual( [] );
} );
