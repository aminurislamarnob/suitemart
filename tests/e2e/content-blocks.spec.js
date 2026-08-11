/**
 * Behaviour of the P2 content blocks.
 *
 * These assert the two things that are invisible to the PHP suite: that the
 * enhancement actually runs in a browser, and that what the server rendered
 * survives when it does not.
 *
 * Requires the fixture page created from tests/e2e/fixtures/blocks-page.html —
 * see tests/README.md.
 */

const { test, expect } = require( '@playwright/test' );

const PAGE = '/?page_id=10';

test.describe( 'Counter', () => {
	const number = '.sm-counter__number';

	test( 'shows the final value before any script runs', async ( {
		browser,
	} ) => {
		// The counter's whole no-JS story is that the server printed the real
		// figure. This block once shipped blank because a directive the server
		// could not resolve erased it, so the check is worth its own test.
		const context = await browser.newContext( {
			javaScriptEnabled: false,
		} );
		const noJs = await context.newPage();

		await noJs.goto( PAGE );
		await expect( noJs.locator( number ) ).toHaveText( '5,000' );

		await context.close();
	} );

	test( 'counts up once it scrolls into view', async ( { page } ) => {
		await page.goto( PAGE );

		const counter = page.locator( number );

		// Sample continuously from before the block is visible, because the
		// count is over in four seconds and polling after the fact would only
		// ever see the final value.
		const samples = await page.evaluate( async () => {
			const node = document.querySelector( '.sm-counter__number' );
			const seen = new Set();

			node.scrollIntoView();

			const started = performance.now();

			while ( performance.now() - started < 5000 ) {
				seen.add( node.textContent.trim() );
				await new Promise( ( resolve ) =>
					requestAnimationFrame( resolve )
				);
			}

			return [ ...seen ];
		} );

		// More than one distinct value means it genuinely animated rather than
		// jumping straight to the end.
		expect( samples.length ).toBeGreaterThan( 5 );
		await expect( counter ).toHaveText( '5,000' );
	} );

	test( 'jumps straight to the final value under reduced motion', async ( {
		browser,
	} ) => {
		const context = await browser.newContext( {
			reducedMotion: 'reduce',
		} );
		const page = await context.newPage();

		await page.goto( PAGE );

		const counter = page.locator( '.sm-counter__number' );
		await counter.scrollIntoViewIfNeeded();

		// Never anything but the final figure, at any point.
		for ( let i = 0; i < 5; i++ ) {
			await expect( counter ).toHaveText( '5,000' );
			await page.waitForTimeout( 100 );
		}

		await context.close();
	} );
} );

test.describe( 'Marquee', () => {
	const root = '.sm-marquee';
	const toggle = '.sm-marquee__toggle';

	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );
		await page.locator( root ).scrollIntoViewIfNeeded();
	} );

	test( 'clones the track and starts scrolling', async ( { page } ) => {
		await expect( page.locator( root ) ).toHaveClass( /is-animating/ );

		// Two copies: the loop is seamless only because the second one is
		// identical and the animation travels exactly half the lane.
		await expect( page.locator( '.sm-marquee__track' ) ).toHaveCount( 2 );

		const duration = await page
			.locator( root )
			.evaluate( ( el ) =>
				getComputedStyle( el )
					.getPropertyValue( '--sm-marquee-duration' )
					.trim()
			);

		expect( duration ).toMatch( /^[\d.]+s$/ );
	} );

	test( 'hides the clone from assistive technology and from Tab', async ( {
		page,
	} ) => {
		const clone = page.locator( '.sm-marquee__track' ).nth( 1 );

		await expect( clone ).toHaveAttribute( 'aria-hidden', 'true' );
		expect( await clone.evaluate( ( el ) => el.inert ) ).toBe( true );

		// The clone carries duplicates of everything, so any id it kept would
		// break the original's label and anchor references.
		expect( await clone.locator( '[id]' ).count() ).toBe( 0 );
	} );

	test( 'the pause control is labelled and works both ways', async ( {
		page,
	} ) => {
		const button = page.locator( toggle );
		const lane = page.locator( '.sm-marquee__lane' );

		await expect( button ).toBeVisible();
		await expect( button ).toHaveAttribute( 'aria-label', /pause/i );

		await button.click();

		await expect( page.locator( root ) ).toHaveClass( /is-paused/ );
		await expect( button ).toHaveAttribute( 'aria-label', /resume/i );
		expect(
			await lane.evaluate(
				( el ) => getComputedStyle( el ).animationPlayState
			)
		).toBe( 'paused' );

		await button.click();

		await expect( page.locator( root ) ).not.toHaveClass( /is-paused/ );
		await expect( button ).toHaveAttribute( 'aria-label', /pause/i );
	} );

	test( 'does not animate under reduced motion', async ( { browser } ) => {
		const context = await browser.newContext( { reducedMotion: 'reduce' } );
		const page = await context.newPage();

		await page.goto( PAGE );
		await page.locator( root ).scrollIntoViewIfNeeded();

		// No clone, no animation class, and therefore no pause button to need.
		await expect( page.locator( '.sm-marquee__track' ) ).toHaveCount( 1 );
		await expect( page.locator( root ) ).not.toHaveClass( /is-animating/ );
		await expect( page.locator( toggle ) ).toBeHidden();

		await context.close();
	} );
} );

test.describe( 'Testimonial', () => {
	test( 'states its rating in text, not only in stars', async ( {
		page,
	} ) => {
		await page.goto( PAGE );

		// Five star icons would be announced as five separate images; the
		// rating has to reach a screen reader as a sentence.
		await expect(
			page.locator( '.sm-testimonial__rating-text' )
		).toHaveText( /4 out of 5/ );

		await expect(
			page.locator( '.sm-testimonial__stars' )
		).toHaveAttribute( 'aria-hidden', 'true' );
	} );
} );

test.describe( 'Timeline', () => {
	test( 'is a real ordered list', async ( { page } ) => {
		await page.goto( PAGE );

		// The order carries the meaning, so it has to be in the markup: a
		// screen reader then announces the position of each entry.
		const list = page.locator( 'ol.sm-timeline' );
		await expect( list ).toHaveCount( 1 );
		await expect( list.locator( '> li.sm-timeline__item' ) ).toHaveCount(
			2
		);
	} );
} );
