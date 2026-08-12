/**
 * Image hotspots.
 *
 * The fixture page carries two hotspot images rather than one, and most of what
 * is worth asserting here only exists because of that: ids that stay unique
 * across instances, and a marker that closes the panel open on the *other*
 * image. A one-instance fixture would pass every one of these tests while the
 * block was broken on any page that used it twice.
 */

const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const PAGE =
	process.env.SUITEMART_HOTSPOTS_URL || '/?pagename=suitemart-hotspots';

const MARKER = '.sm-hotspots__marker';
const PANEL = '.sm-hotspots__panel';

test.describe( 'Image hotspots', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );
	} );

	test( 'serves every panel closed, and says so', async ( { page } ) => {
		const markers = page.locator( MARKER );
		await expect( markers ).toHaveCount( 4 );

		// Not `toBeHidden` on the panels alone: an element with no `hidden`
		// attribute that happens to be off-screen would satisfy that, and the
		// point is that the server rendered the closed state rather than the
		// browser correcting it after hydration.
		await expect(
			page.locator( `${ MARKER }[aria-expanded="false"]` )
		).toHaveCount( 4 );
		await expect( page.locator( `${ PANEL }[hidden]` ) ).toHaveCount( 4 );
	} );

	test( 'each marker controls its own panel', async ( { page } ) => {
		const pairs = await page
			.locator( '.sm-hotspots__point' )
			.evaluateAll( ( points ) =>
				points.map( ( point ) => ( {
					controls: point
						.querySelector( '.sm-hotspots__marker' )
						.getAttribute( 'aria-controls' ),
					panelId: point.querySelector( '.sm-hotspots__panel' ).id,
				} ) )
			);

		expect( pairs ).toHaveLength( 4 );

		for ( const pair of pairs ) {
			expect( pair.controls ).toBe( pair.panelId );
		}

		// And no id is reused between the two images on this page.
		const ids = pairs.map( ( pair ) => pair.panelId );
		expect( new Set( ids ).size ).toBe( ids.length );
	} );

	test( 'opening a marker closes the one already open', async ( {
		page,
	} ) => {
		const markers = page.locator( MARKER );

		await markers.nth( 0 ).click();
		await expect( markers.nth( 0 ) ).toHaveAttribute(
			'aria-expanded',
			'true'
		);

		await markers.nth( 1 ).click();
		await expect( markers.nth( 1 ) ).toHaveAttribute(
			'aria-expanded',
			'true'
		);
		await expect( markers.nth( 0 ) ).toHaveAttribute(
			'aria-expanded',
			'false'
		);

		// The third marker belongs to the second image on the page. Closing
		// across instances is the part that has no shared state behind it — it
		// works because a click on another marker is, to this one, a click
		// outside.
		await markers.nth( 2 ).click();
		await expect( markers.nth( 2 ) ).toHaveAttribute(
			'aria-expanded',
			'true'
		);
		await expect( markers.nth( 1 ) ).toHaveAttribute(
			'aria-expanded',
			'false'
		);
	} );

	test( 'clicking away closes the panel', async ( { page } ) => {
		const marker = page.locator( MARKER ).first();

		await marker.click();
		await expect( marker ).toHaveAttribute( 'aria-expanded', 'true' );

		await page.locator( 'h1, h2' ).first().click();
		await expect( marker ).toHaveAttribute( 'aria-expanded', 'false' );
	} );

	test( 'Escape closes the panel and gives focus back', async ( {
		page,
	} ) => {
		const marker = page.locator( MARKER ).first();

		await marker.click();
		await expect( marker ).toHaveAttribute( 'aria-expanded', 'true' );

		await page.keyboard.press( 'Escape' );

		await expect( marker ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( marker ).toBeFocused();
	} );

	test( 'the marker is reachable and operable from the keyboard', async ( {
		page,
	} ) => {
		const marker = page.locator( MARKER ).first();

		await marker.focus();
		await page.keyboard.press( 'Enter' );

		await expect( marker ).toHaveAttribute( 'aria-expanded', 'true' );
		await expect( page.locator( PANEL ).first() ).toBeVisible();
	} );

	test( 'has no accessibility violations with a panel open', async ( {
		page,
	} ) => {
		await page.locator( MARKER ).first().click();

		// The pulse ring never settles, so axe is run with animation stopped
		// rather than waiting for animations that have no end.
		await page.emulateMedia( { reducedMotion: 'reduce' } );

		const { violations } = await new AxeBuilder( { page } )
			.withTags( [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ] )
			.analyze();

		expect( violations ).toEqual( [] );
	} );
} );
