/**
 * 360° view.
 *
 * The block went through one wrong implementation before this one: the visible
 * frame was a class moved by a `data-wp-watch` callback, and Preact restored
 * the class it had rendered on the next update, so the viewer skipped frames
 * unpredictably. Nothing in a PHPUnit run could have caught that — it only
 * happens once hydration is live — which is what most of this file is for.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const PAGE =
	process.env.SUITEMART_VIEW_360_URL || '/?pagename=suitemart-view-360';

const BLOCK = '.sm-view-360';
const FRAME = '.sm-view-360__frame';

/**
 * Index of the frame currently on show.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @return {Promise<number>} Zero-based index, or -1 if none.
 */
const visibleFrame = ( page ) =>
	page
		.locator( BLOCK )
		.evaluate( ( root ) =>
			[ ...root.querySelectorAll( '.sm-view-360__frame' ) ].findIndex(
				( frame ) => ! frame.hasAttribute( 'hidden' )
			)
		);

test.describe( '360 view', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );
	} );

	test( 'serves the first frame, and only the first', async ( { page } ) => {
		await expect( page.locator( FRAME ) ).toHaveCount( 4 );
		await expect( page.locator( `${ FRAME }:not([hidden])` ) ).toHaveCount(
			1
		);

		expect( await visibleFrame( page ) ).toBe( 0 );
	} );

	test( 'the buttons step through the sequence and wrap', async ( {
		page,
	} ) => {
		const buttons = page.locator( '.sm-view-360__button' );

		await buttons.nth( 1 ).click();
		await expect.poll( () => visibleFrame( page ) ).toBe( 1 );

		await buttons.nth( 1 ).click();
		await expect.poll( () => visibleFrame( page ) ).toBe( 2 );

		// Backwards past the start wraps to the end rather than sticking.
		await buttons.nth( 0 ).click();
		await buttons.nth( 0 ).click();
		await buttons.nth( 0 ).click();
		await expect.poll( () => visibleFrame( page ) ).toBe( 3 );
	} );

	test( 'stepping never leaves two frames showing', async ( { page } ) => {
		const next = page.locator( '.sm-view-360__button' ).nth( 1 );

		// Six steps around a four-frame sequence, so it goes round more than
		// once. This is the assertion the class-based version failed.
		for ( let step = 0; step < 6; step++ ) {
			await next.click();
			await expect(
				page.locator( `${ FRAME }:not([hidden])` )
			).toHaveCount( 1 );
		}

		expect( await visibleFrame( page ) ).toBe( 2 );
	} );

	test( 'dragging across the viewer rotates it', async ( { page } ) => {
		const stack = page.locator( '.sm-view-360__frames' );
		const box = await stack.boundingBox();

		await page.mouse.move( box.x + 10, box.y + box.height / 2 );
		await page.mouse.down();

		// A quarter of the width is a quarter of a rotation, which over four
		// frames is exactly one frame.
		await page.mouse.move(
			box.x + 10 + box.width * 0.25,
			box.y + box.height / 2,
			{ steps: 8 }
		);

		await expect.poll( () => visibleFrame( page ) ).toBe( 1 );

		await page.mouse.move(
			box.x + 10 + box.width * 0.5,
			box.y + box.height / 2,
			{ steps: 8 }
		);

		await expect.poll( () => visibleFrame( page ) ).toBe( 2 );

		await page.mouse.up();

		// And the rotation stops when the pointer is released, rather than
		// following the mouse around the page.
		await page.mouse.move( box.x + 10, box.y + box.height / 2 );
		expect( await visibleFrame( page ) ).toBe( 2 );
	} );

	test( 'the whole stack is one image to assistive technology', async ( {
		page,
	} ) => {
		const stack = page.locator( '.sm-view-360__frames' );

		await expect( stack ).toHaveAttribute( 'role', 'img' );
		await expect( stack ).toHaveAttribute(
			'aria-label',
			'A walnut chair, seen from every side'
		);

		// Not four images announced one after another.
		const alts = await page
			.locator( FRAME )
			.evaluateAll( ( frames ) =>
				frames.map( ( frame ) => frame.getAttribute( 'alt' ) )
			);

		expect( alts ).toEqual( [ '', '', '', '' ] );
	} );

	test( 'has no accessibility violations', async ( { page } ) => {
		const { violations } = await new AxeBuilder( { page } )
			.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
			.analyze();

		expect( violations ).toEqual( [] );
	} );
} );
