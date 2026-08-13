/**
 * Post carousel.
 *
 * Two things are worth testing in a browser and nowhere else: that the cards
 * are usable before Swiper arrives, and that they are still usable after it
 * does. The fixture shows two cards at a time out of five, so there is always
 * somewhere to move to.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const PAGE =
	process.env.SUITEMART_POST_CAROUSEL_URL ||
	'/?pagename=suitemart-post-carousel';

const ROOT = '.sm-post-carousel';
const SLIDES = '.sm-post-carousel__slide';
const NEXT = '.sm-post-carousel__arrow--next';
const PREV = '.sm-post-carousel__arrow--prev';

/**
 * How far along the track Swiper has moved, in pixels.
 *
 * Read from the rendered box rather than from the inline transform, which is an
 * empty string until the first slide and `translate3d(0px, …)` after coming
 * back to the start — two spellings of the same position, and comparing the
 * strings makes a returning carousel look like a stuck one.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @return {Promise<number>} Offset of the first card from the viewport's edge.
 */
const offset = ( page ) =>
	page.evaluate( () => {
		const viewport = document.querySelector(
			'.sm-post-carousel__viewport'
		);
		const first = document.querySelector( '.sm-post-carousel__slide' );

		return Math.round(
			first.getBoundingClientRect().left -
				viewport.getBoundingClientRect().left
		);
	} );

test.describe( 'Post carousel', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );
	} );

	test( 'every card is in the page, enhanced or not', async ( { page } ) => {
		// Not "however many fit": all five are served, so the content is there
		// for a crawler, for reader mode, and for a browser that never runs the
		// module.
		await expect( page.locator( SLIDES ) ).toHaveCount( 5 );

		const first = page.locator( SLIDES ).first();

		await expect(
			first.locator( '.sm-post-carousel__title' )
		).toBeVisible();
		await expect(
			first.locator( '.sm-post-carousel__excerpt' )
		).toBeVisible();
	} );

	test( 'is a named carousel', async ( { page } ) => {
		const root = page.locator( ROOT );

		await expect( root ).toHaveAttribute(
			'aria-roledescription',
			'carousel'
		);
		await expect( root ).toHaveAttribute( 'aria-label', 'From the blog' );
	} );

	test( 'Swiper takes it over and the arrows move it', async ( { page } ) => {
		await expect( page.locator( ROOT ) ).toHaveClass( /is-enhanced/ );

		const start = await offset( page );

		await page.locator( NEXT ).click();

		await expect.poll( () => offset( page ) ).not.toBe( start );

		await page.locator( PREV ).click();

		await expect.poll( () => offset( page ) ).toBe( start );
	} );

	test( 'the arrows are named and reachable by keyboard', async ( {
		page,
	} ) => {
		for ( const [ selector, name ] of [
			[ PREV, /previous posts/i ],
			[ NEXT, /more posts/i ],
		] ) {
			const svg = page.locator( `${ selector } svg` );

			await expect( svg ).toHaveAttribute( 'role', 'img' );
			await expect( svg ).toHaveAttribute( 'aria-label', name );
		}

		const start = await offset( page );

		await page.locator( NEXT ).focus();
		await page.keyboard.press( 'Enter' );

		await expect.poll( () => offset( page ) ).not.toBe( start );
	} );

	test( 'the image and the title are one stop, not two', async ( {
		page,
	} ) => {
		/*
		 * Where a card has a featured image its link goes to the same post as
		 * the title beneath it. Two links to one place is two tab stops and two
		 * identical announcements, so the image one is hidden and skipped.
		 */
		const media = page.locator( '.sm-post-carousel__media' );

		for ( let i = 0; i < ( await media.count() ); i++ ) {
			await expect( media.nth( i ) ).toHaveAttribute( 'tabindex', '-1' );
			await expect( media.nth( i ) ).toHaveAttribute(
				'aria-hidden',
				'true'
			);
		}
	} );

	test( 'has no accessibility violations', async ( { page } ) => {
		await expect( page.locator( ROOT ) ).toHaveClass( /is-enhanced/ );
		await page.evaluate( () => document.fonts.ready );

		const { violations } = await new AxeBuilder( { page } )
			.include( ROOT )
			.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
			.analyze();

		expect( violations ).toEqual( [] );
	} );
} );
