/**
 * Image comparison.
 *
 * The block is deliberately almost empty of JavaScript — a native range input
 * does the work — so these assertions are mostly about that bet paying off:
 * that arrow keys, Home and End move the wipe, and that the divider tracks the
 * value rather than drifting away from it the way a native thumb would.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const PAGE =
	process.env.SUITEMART_COMPARE_IMAGES_URL ||
	'/?pagename=suitemart-compare-images';

const BLOCK = '.sm-compare-images';
const RANGE = '.sm-compare-images__range';

/**
 * Reads the wipe position off the frame, as a number.
 *
 * @param {import('@playwright/test').Locator} block The block root.
 * @return {Promise<number>} Position, 0–100.
 */
const positionOf = ( block ) =>
	block
		.locator( '.sm-compare-images__frame' )
		.evaluate( ( frame ) =>
			parseFloat(
				getComputedStyle( frame ).getPropertyValue(
					'--sm-compare-position'
				)
			)
		);

test.describe( 'Image comparison', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );
	} );

	test( 'serves the starting position without waiting for script', async ( {
		page,
	} ) => {
		const blocks = page.locator( BLOCK );
		await expect( blocks ).toHaveCount( 2 );

		// 40 is what the fixture asks for. A stripped style attribute would
		// leave this NaN, and the second photograph covering the first.
		expect( await positionOf( blocks.first() ) ).toBe( 40 );
		expect( await positionOf( blocks.nth( 1 ) ) ).toBe( 40 );
	} );

	test( 'the arrow keys move the wipe', async ( { page } ) => {
		const block = page.locator( BLOCK ).first();
		const range = block.locator( RANGE );

		await range.focus();
		await page.keyboard.press( 'ArrowRight' );

		await expect.poll( () => positionOf( block ) ).toBe( 41 );

		await page.keyboard.press( 'ArrowLeft' );
		await page.keyboard.press( 'ArrowLeft' );

		await expect.poll( () => positionOf( block ) ).toBe( 39 );
	} );

	test( 'Home and End reach both extremes', async ( { page } ) => {
		const block = page.locator( BLOCK ).first();

		await block.locator( RANGE ).focus();

		await page.keyboard.press( 'End' );
		await expect.poll( () => positionOf( block ) ).toBe( 100 );

		await page.keyboard.press( 'Home' );
		await expect.poll( () => positionOf( block ) ).toBe( 0 );
	} );

	test( 'the divider sits exactly on the wipe', async ( { page } ) => {
		const block = page.locator( BLOCK ).first();

		await block.locator( RANGE ).focus();
		await page.keyboard.press( 'End' );
		await expect.poll( () => positionOf( block ) ).toBe( 100 );

		const { frame, divider } = await block.evaluate( ( el ) => ( {
			frame: el
				.querySelector( '.sm-compare-images__frame' )
				.getBoundingClientRect().right,
			divider: el
				.querySelector( '.sm-compare-images__divider' )
				.getBoundingClientRect().right,
		} ) );

		// Within the divider's own width. A native slider thumb would be half
		// a thumb short of the end here, which is why it is hidden and this is
		// drawn separately.
		expect( Math.abs( frame - divider ) ).toBeLessThanOrEqual( 2 );
	} );

	test( 'the two blocks move independently', async ( { page } ) => {
		const first = page.locator( BLOCK ).first();
		const second = page.locator( BLOCK ).nth( 1 );

		await first.locator( RANGE ).focus();
		await page.keyboard.press( 'End' );

		await expect.poll( () => positionOf( first ) ).toBe( 100 );
		expect( await positionOf( second ) ).toBe( 40 );
	} );

	test( 'the vertical block clips top to bottom', async ( { page } ) => {
		const vertical = page.locator( '.sm-compare-images--vertical' );

		const clip = await vertical
			.locator( '.sm-compare-images__reveal' )
			.evaluate( ( el ) => getComputedStyle( el ).clipPath );

		// Insets read top / right / bottom / left, so a vertical wipe leaves
		// the bottom trimmed and the sides alone.
		expect( clip ).toContain( '60%' );
		expect( clip.startsWith( 'inset(0px 0px' ) ).toBe( true );
	} );

	test( 'has no accessibility violations', async ( { page } ) => {
		const { violations } = await new AxeBuilder( { page } )
			.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
			.analyze();

		expect( violations ).toEqual( [] );
	} );
} );
