/**
 * A named list of product ids kept in localStorage.
 *
 * Backs both the wishlist and the compare list. Two things follow from storing
 * it in the browser rather than on the server, and both were the point:
 *
 *   - No cookie is set, so a full-page cache still serves one copy of a page to
 *     everybody. A cookie that varies per visitor defeats WP Rocket and
 *     LiteSpeed, which the theme has to work with (decision 24).
 *   - Nothing is recorded about a visitor server-side, so a guest wishlist
 *     needs no account and no personal data.
 *
 * The cost is that the server cannot know the list when it renders. Blocks
 * therefore ship a neutral state and correct themselves on hydration, and the
 * list contents are fetched from the Store API in the browser.
 */

const PREFIX = 'suitemart/';

/**
 * Reads a list.
 *
 * localStorage throws rather than returning null when it is unavailable —
 * Safari's private mode historically, and any browser with site data blocked —
 * so every access is guarded. A reader who has disabled storage gets a list
 * that is always empty rather than a broken page.
 *
 * @param {string} name List name, e.g. 'wishlist'.
 * @return {number[]} Product ids, oldest first.
 */
export const readList = ( name ) => {
	try {
		const raw = window.localStorage.getItem( PREFIX + name );
		const parsed = raw ? JSON.parse( raw ) : [];

		if ( ! Array.isArray( parsed ) ) {
			return [];
		}

		// Anything can end up in localStorage — another script, a hand edit, an
		// older version of this code — so the contents are never trusted.
		return parsed
			.map( ( id ) => Number( id ) )
			.filter( ( id ) => Number.isInteger( id ) && id > 0 );
	} catch {
		return [];
	}
};

/**
 * Writes a list and tells this tab about it.
 *
 * The `storage` event only fires in *other* tabs, so a same-tab listener would
 * never hear its own change. A custom event covers that case, which is what
 * keeps a header counter and a button on the same page agreeing.
 *
 * @param {string}   name List name.
 * @param {number[]} ids  Product ids.
 * @return {boolean} Whether the write succeeded.
 */
export const writeList = ( name, ids ) => {
	try {
		window.localStorage.setItem( PREFIX + name, JSON.stringify( ids ) );
	} catch {
		return false;
	}

	window.dispatchEvent(
		new CustomEvent( 'suitemart-list-change', { detail: { name, ids } } )
	);

	return true;
};

/**
 * Adds or removes an id.
 *
 * `stored` reports whether the change actually reached storage. It is not
 * decoration: with site data blocked, `writeList` fails and a caller that
 * assumed success showed a filled heart for a wishlist that would be empty on
 * the next page load. Callers must not claim a change that did not happen.
 *
 * @param {string} name    List name.
 * @param {number} id      Product id.
 * @param {number} [limit] Maximum length; the oldest entry is dropped when
 *                         adding beyond it. Zero means no limit.
 * @return {{ids: number[], added: boolean, evicted: boolean, stored: boolean}} The new state.
 */
export const toggleInList = ( name, id, limit = 0 ) => {
	const current = readList( name );
	const has = current.includes( id );

	if ( has ) {
		const ids = current.filter( ( entry ) => entry !== id );
		const stored = writeList( name, ids );

		return { ids, added: false, evicted: false, stored };
	}

	let ids = [ ...current, id ];
	let evicted = false;

	// A compare table has a fixed number of columns, so the list has a ceiling.
	// Dropping the oldest entry is friendlier than refusing the new one: the
	// reader asked for this product, and the alternative is a dead control.
	if ( limit > 0 && ids.length > limit ) {
		ids = ids.slice( ids.length - limit );
		evicted = true;
	}

	const stored = writeList( name, ids );

	return { ids, added: true, evicted, stored };
};

/**
 * Calls back whenever a list changes, in this tab or another.
 *
 * @param {string}                    name     List name.
 * @param {( ids: number[] ) => void} callback Receives the new contents.
 * @return {() => void} Teardown that removes both listeners.
 */
export const onListChange = ( name, callback ) => {
	const onCustom = ( event ) => {
		if ( event.detail?.name === name ) {
			callback( event.detail.ids );
		}
	};

	const onStorage = ( event ) => {
		if ( event.key === PREFIX + name ) {
			callback( readList( name ) );
		}
	};

	window.addEventListener( 'suitemart-list-change', onCustom );
	window.addEventListener( 'storage', onStorage );

	return () => {
		window.removeEventListener( 'suitemart-list-change', onCustom );
		window.removeEventListener( 'storage', onStorage );
	};
};
