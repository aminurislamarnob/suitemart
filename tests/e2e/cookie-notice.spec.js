/**
 * Cookie notice.
 *
 * Both directions are asserted throughout: shown when nothing is stored, and
 * gone on the next load after a choice. Half of this block is the second visit,
 * and a spec that only pressed a button would pass on a notice that came back
 * to nag every page.
 */

const { test, expect } = require( '@playwright/test' );

const PAGE =
	process.env.SUITEMART_COOKIE_NOTICE_URL ||
	'/?pagename=suitemart-cookie-notice';

const KEY = 'suitemart:cookie-consent';

const BLOCK = '.sm-cookie-notice';
const ACCEPT = '.sm-cookie-notice__button--accept';
const DECLINE = '.sm-cookie-notice__button--decline';

/**
 * Reads what the page has recorded, from all three places it publishes to.
 *
 * @param {import('@playwright/test').Page} page Page.
 * @return {Promise<{stored: string|null, attr: string|undefined}>} Recorded choice.
 */
const recorded = ( page ) =>
	page.evaluate(
		( key ) => ( {
			stored: window.localStorage.getItem( key ),
			attr: document.documentElement.dataset.smConsent,
		} ),
		KEY
	);

test.describe( 'Cookie notice', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( PAGE );
		await page.evaluate(
			( key ) => window.localStorage.removeItem( key ),
			KEY
		);
		await page.reload();
	} );

	test( 'is shown to a visitor who has not answered', async ( { page } ) => {
		await expect( page.locator( BLOCK ) ).toBeVisible();
		await expect( page.locator( ACCEPT ) ).toBeVisible();
		await expect( page.locator( DECLINE ) ).toBeVisible();
	} );

	test( 'names itself as a region', async ( { page } ) => {
		await expect(
			page.getByRole( 'region', { name: 'Cookie notice' } )
		).toBeVisible();
	} );

	test( 'records acceptance and stays away afterwards', async ( {
		page,
	} ) => {
		const events = [];

		await page.exposeFunction( 'smConsentSeen', ( choice ) =>
			events.push( choice )
		);
		await page.evaluate( () =>
			document.addEventListener( 'suitemart-cookie-consent', ( event ) =>
				window.smConsentSeen( event.detail.choice )
			)
		);

		await page.locator( ACCEPT ).click();

		await expect( page.locator( BLOCK ) ).toBeHidden();
		await expect.poll( () => events ).toEqual( [ 'accepted' ] );
		expect( await recorded( page ) ).toEqual( {
			stored: 'accepted',
			attr: 'accepted',
		} );

		// The half that matters: a second page load.
		await page.reload();

		await expect( page.locator( BLOCK ) ).toBeHidden();

		/*
		 * And the choice is republished, because anything listening for the
		 * event was not on the page when the button was pressed.
		 */
		await expect
			.poll( async () => ( await recorded( page ) ).attr )
			.toBe( 'accepted' );
	} );

	test( 'records refusal just as readily', async ( { page } ) => {
		await page.locator( DECLINE ).click();

		await expect( page.locator( BLOCK ) ).toBeHidden();
		expect( await recorded( page ) ).toEqual( {
			stored: 'declined',
			attr: 'declined',
		} );

		await page.reload();

		await expect( page.locator( BLOCK ) ).toBeHidden();
	} );

	test( 'gives both answers the same weight', async ( { page } ) => {
		/*
		 * Declining has to be as easy as accepting. Same box, same type — the
		 * two buttons may differ in fill and nothing else, because a quieter
		 * "Decline" is the specific nudge regulators have been fining sites
		 * for. Measured rather than asserted about the class list, since the
		 * dark pattern is a visual one.
		 */
		const box = async ( selector ) => {
			const rect = await page.locator( selector ).boundingBox();
			const type = await page.locator( selector ).evaluate( ( el ) => {
				const style = window.getComputedStyle( el );

				return {
					fontSize: style.fontSize,
					fontWeight: style.fontWeight,
					textTransform: style.textTransform,
					opacity: style.opacity,
				};
			} );

			return { ...type, height: Math.round( rect.height ) };
		};

		const accept = await box( ACCEPT );
		const decline = await box( DECLINE );

		expect( decline ).toEqual( accept );

		// Neither is pre-selected, and neither is a link dressed as a button.
		await expect( page.locator( ACCEPT ) ).toHaveJSProperty(
			'tagName',
			'BUTTON'
		);
		await expect( page.locator( DECLINE ) ).toHaveJSProperty(
			'tagName',
			'BUTTON'
		);
	} );

	test( 'is not covered by the back-to-top button', async ( { page } ) => {
		/*
		 * Both are pinned to the bottom of every page, and the button is drawn
		 * on top of the bar's Accept — where it silently eats the click. The
		 * button stands down while the notice is up; this scrolls far enough
		 * for it to have appeared, then presses Accept for real.
		 */
		await page.evaluate( () => {
			const spacer = document.createElement( 'div' );

			spacer.style.height = '3000px';
			document.body.append( spacer );
			window.scrollTo( { top: 1500, behavior: 'instant' } );
		} );

		await expect( page.locator( '.sm-back-to-top' ) ).toBeHidden();

		await page.locator( ACCEPT ).click();

		await expect( page.locator( BLOCK ) ).toBeHidden();

		// And it comes back once the notice has been answered.
		await expect( page.locator( '.sm-back-to-top' ) ).toBeVisible();
	} );

	test( 'is reachable and operable from the keyboard', async ( { page } ) => {
		await page.locator( DECLINE ).focus();

		// Decline comes first in the tab order as well as in the source.
		await expect( page.locator( DECLINE ) ).toBeFocused();
		await page.keyboard.press( 'Tab' );
		await expect( page.locator( ACCEPT ) ).toBeFocused();

		await page.keyboard.press( 'Enter' );

		await expect( page.locator( BLOCK ) ).toBeHidden();
		expect( ( await recorded( page ) ).stored ).toBe( 'accepted' );
	} );
} );
