/**
 * Lightbox.
 *
 * PhotoSwipe supplies the viewer; what is worth testing is the seam around it —
 * that the library is not downloaded until someone asks for it, that the
 * captions and control labels come from WordPress rather than from PhotoSwipe's
 * English defaults, and that a keyboard user ends up where they started.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const PAGE =
	process.env.SUITEMART_LIGHTBOX_URL || '/?pagename=suitemart-lightbox';

const ITEM = 'a.sm-lightbox__item';
const VIEWER = '.pswp';

/**
 * Waits for the viewer and settles its opening animation.
 *
 * The fade is finished rather than waited for. A browser that is not painting —
 * a backgrounded tab locally, a headless runner in CI — freezes its animation
 * timeline, so PhotoSwipe's transition is created and never advances:
 * `currentTime` stays at 0 indefinitely and the top bar sits at the
 * `opacity: 0.005` it starts from, which makes every contrast reading inside it
 * a reading through a near-transparent element. Polling for the end state
 * passes on a developer's machine and fails on CI. `finish()` jumps to it
 * without needing the clock.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
const settleViewer = async ( page ) => {
	await expect( page.locator( VIEWER ) ).toBeVisible();

	// Finished inside the poll, not before it: the transition is created a tick
	// or two after the viewer appears, so a single pass can run before there is
	// anything to finish and settle nothing at all.
	await expect
		.poll(
			() =>
				page.evaluate( () => {
					for ( const animation of document.getAnimations() ) {
						if (
							animation.effect?.getComputedTiming()
								?.iterations !== Infinity
						) {
							try {
								animation.finish();
							} catch {
								// A cancelled or fill-less animation cannot be
								// finished, and does not need to be.
							}
						}
					}

					const bar = document.querySelector( '.pswp__top-bar' );

					return bar ? getComputedStyle( bar ).opacity : null;
				} ),
			{ timeout: 10000 }
		)
		.toBe( '1' );
};

/**
 * Waits until focus has moved inside the viewer.
 *
 * Only meaningful for a keyboard-opened lightbox. PhotoSwipe deliberately
 * leaves focus alone when it was opened by pointer, so waiting for this after a
 * click waits forever — which is how this spec went red on CI while passing
 * locally, where the click happened to focus the link first.
 *
 * @param {import('@playwright/test').Page} page Page.
 */
const waitForViewerFocus = ( page ) =>
	expect
		.poll( () =>
			page.evaluate( () =>
				Boolean( document.activeElement?.closest( '.pswp' ) )
			)
		)
		.toBe( true );

test.describe( 'Lightbox', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );
	} );

	test( 'marks up only the images that can be enlarged', async ( {
		page,
	} ) => {
		// Three images on the fixture page; the third links nowhere.
		await expect( page.locator( '.sm-lightbox img' ) ).toHaveCount( 3 );
		await expect( page.locator( ITEM ) ).toHaveCount( 2 );

		for ( const item of await page.locator( ITEM ).all() ) {
			await expect( item ).toHaveAttribute( 'data-pswp-width', /^\d+$/ );
			await expect( item ).toHaveAttribute( 'data-pswp-height', /^\d+$/ );
		}
	} );

	test( 'does not download the viewer until it is opened', async ( {
		page,
	} ) => {
		// webpack names split chunks by number, so there is nothing in the URL
		// saying "photoswipe" to match on. Counting them is the honest measure
		// anyway: what matters is that opening the lightbox costs a request
		// that merely having one on the page does not.
		const chunks = new Set();

		page.on( 'request', ( request ) => {
			if ( /\/build\/\d+\.js/.test( request.url() ) ) {
				chunks.add( request.url() );
			}
		} );

		await page.goto( PAGE );
		await expect(
			page.locator( '.sm-lightbox.is-enhanced' )
		).toBeVisible();

		const beforeOpening = chunks.size;

		await page.locator( ITEM ).first().click();
		await expect( page.locator( VIEWER ) ).toBeVisible();

		expect( chunks.size ).toBeGreaterThan( beforeOpening );
	} );

	test( 'opens as one gallery with the images in order', async ( {
		page,
	} ) => {
		await page.locator( ITEM ).first().click();

		const viewer = page.locator( VIEWER );
		await expect( viewer ).toBeVisible();

		// PhotoSwipe supplies the role and traps focus; the name and the modal
		// flag are added by this block, because without them a screen reader
		// announces an unnamed dialog and does not know the page behind it is
		// inert.
		await expect( viewer ).toHaveAttribute( 'role', 'dialog' );
		await expect( viewer ).toHaveAttribute( 'aria-modal', 'true' );
		await expect( viewer ).toHaveAttribute( 'aria-label', 'Image viewer' );

		// Both images, not one: this is what core's own image lightbox does
		// not do, and the reason the block exists.
		await expect( page.locator( '.pswp__counter' ) ).toHaveText( '1 / 2' );

		await page.locator( '.pswp__button--arrow--next' ).click();
		await expect( page.locator( '.pswp__counter' ) ).toHaveText( '2 / 2' );
	} );

	test( 'the controls are labelled from WordPress, not PhotoSwipe', async ( {
		page,
	} ) => {
		await page.locator( ITEM ).first().click();
		await settleViewer( page );

		// PhotoSwipe's own defaults are "Close", "Next" and "Previous". These
		// come through wp_interactivity_config(), so a translated site gets
		// translated controls rather than English ones.
		await expect(
			page.locator( '.pswp__button--arrow--next' )
		).toHaveAttribute( 'title', 'Next image' );
		await expect(
			page.locator( '.pswp__button--arrow--prev' )
		).toHaveAttribute( 'title', 'Previous image' );
	} );

	test( 'shows the caption the editor wrote', async ( { page } ) => {
		await page.locator( ITEM ).first().click();

		const caption = page.locator( '.sm-lightbox__caption' );
		await expect( caption ).toHaveText( 'The room in April' );

		// The second image has no caption, so its alt text stands in rather
		// than the first image's caption staying on screen.
		await page.locator( '.pswp__button--arrow--next' ).click();
		await expect( caption ).toHaveText( 'A room after' );
	} );

	test( 'Escape closes it and returns focus to the image', async ( {
		page,
	} ) => {
		const first = page.locator( ITEM ).first();

		await first.focus();
		await page.keyboard.press( 'Enter' );
		await settleViewer( page );

		// Opened from the keyboard, so PhotoSwipe takes focus — and Escape
		// before it has is the race this avoids.
		await waitForViewerFocus( page );

		await page.keyboard.press( 'Escape' );

		await expect( page.locator( VIEWER ) ).toBeHidden();
		await expect( first ).toBeFocused();
	} );

	test( 'the image still opens without the view module', async ( {
		page,
	} ) => {
		// Progressive enhancement is the whole reason the markup is a link:
		// with the module blocked the image still opens, in a new page rather
		// than in a viewer.
		// Matched with a predicate, not a glob: WordPress appends `?ver=` to
		// the module URL, which `**/lightbox/view.js` does not cover.
		await page.route(
			( url ) => url.pathname.endsWith( '/lightbox/view.js' ),
			( route ) => route.abort()
		);
		await page.goto( PAGE );

		const first = page.locator( ITEM ).first();

		await expect( first ).toHaveAttribute( 'href', /\.(png|jpe?g)$/ );
		await expect( page.locator( '.sm-lightbox.is-enhanced' ) ).toHaveCount(
			0
		);
	} );

	test( 'has no accessibility violations, closed or open', async ( {
		page,
	} ) => {
		const scan = async () =>
			(
				await new AxeBuilder( { page } )
					.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
					.analyze()
			).violations;

		expect( await scan() ).toEqual( [] );

		await page.locator( ITEM ).first().click();
		await settleViewer( page );

		expect( await scan() ).toEqual( [] );
	} );
} );
