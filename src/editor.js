/**
 * Editor-only bootstrap.
 *
 * Puts the icon sprite inside the editor canvas, which is the only reason any
 * icon renders there at all.
 *
 * The front end inlines the sprite into the page (`suitemart_print_icon_sprite()`)
 * because `<use href="sprite.svg#id">` does not resolve across documents —
 * Chrome and Safari have never supported an external file reference from `<use>`,
 * same origin or not. The editor had no equivalent, so every icon in the canvas
 * pointed at a sprite the browser declined to fetch and drew nothing: the quick
 * view control previewed as an empty circle while the front end showed a
 * magnifier.
 *
 * It has to go into the canvas *document*, not the admin page. The canvas is an
 * iframe, a `<use>` reference cannot cross into it, and that iframe is replaced
 * whenever the device preview changes — so the injection is repeated for each
 * canvas document rather than done once at load.
 *
 * This covers both routes an icon reaches the canvas by: `_shared/Icon`, and the
 * markup a server-rendered block returns, which carries the same `<use>` the
 * front end emits.
 */

import './editor.scss';

const HOLDER_ID = 'sm-icon-sprite';

// Rescan cost is one querySelectorAll and one getElementById per document, so a
// coarse debounce is plenty — the sprite is not needed within the frame.
const RESCAN_DELAY = 250;

const settings = window.suitemartEditor || {};

const hooked = new WeakSet();

let spriteRequest;
let rescanTimer;

/**
 * Fetches the sprite once, whatever the number of canvas documents.
 *
 * @return {Promise<string>} Sprite markup, or an empty string if unavailable.
 */
function loadSprite() {
	if ( ! spriteRequest ) {
		spriteRequest = window
			.fetch( settings.spriteUrl )
			.then( ( response ) => ( response.ok ? response.text() : '' ) )
			.catch( () => '' );
	}

	return spriteRequest;
}

/**
 * Inlines the sprite into one document, once.
 *
 * @param {Document} doc    Target document.
 * @param {string}   markup Sprite markup.
 * @return {void}
 */
function inject( doc, markup ) {
	if ( ! doc || ! doc.body || doc.getElementById( HOLDER_ID ) ) {
		return;
	}

	const holder = doc.createElement( 'div' );

	holder.id = HOLDER_ID;
	holder.className = 'sm-icon-sprite';
	holder.setAttribute( 'aria-hidden', 'true' );
	holder.hidden = true;
	holder.innerHTML = markup;

	doc.body.prepend( holder );
}

/**
 * Injects into the admin page and every canvas iframe currently mounted.
 *
 * An iframe that has not finished loading has no body to prepend to, so it is
 * hooked once and revisited when it fires `load`.
 *
 * @param {string} markup Sprite markup.
 * @return {void}
 */
function sync( markup ) {
	inject( document, markup );

	document.querySelectorAll( 'iframe' ).forEach( ( frame ) => {
		let doc = null;

		// A cross-origin frame — an embed preview, say — throws on access.
		try {
			doc = frame.contentDocument;
		} catch ( error ) {
			return;
		}

		if ( doc && doc.body ) {
			inject( doc, markup );
			return;
		}

		if ( ! hooked.has( frame ) ) {
			hooked.add( frame );
			frame.addEventListener( 'load', () => sync( markup ) );
		}
	} );
}

loadSprite().then( ( markup ) => {
	if ( ! markup ) {
		return;
	}

	const rescan = () => {
		window.clearTimeout( rescanTimer );
		rescanTimer = window.setTimeout( () => sync( markup ), RESCAN_DELAY );
	};

	sync( markup );

	// The canvas iframe is mounted after this script runs and remounted on every
	// device preview change, so one pass at load is not enough.
	new window.MutationObserver( ( mutations ) => {
		for ( const mutation of mutations ) {
			for ( const node of mutation.addedNodes ) {
				if ( node.nodeType === 1 && 'IFRAME' === node.tagName ) {
					rescan();
					return;
				}
			}
		}
	} ).observe( document.documentElement, {
		childList: true,
		subtree: true,
	} );
} );
