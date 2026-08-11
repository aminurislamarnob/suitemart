/**
 * Live search interactivity.
 *
 * Implements the ARIA combobox pattern: focus stays in the input while the
 * arrow keys move a virtual cursor through the listbox, tracked with
 * aria-activedescendant. Enter follows the highlighted result; with none
 * highlighted the form submits normally, so the block never traps a user who
 * just wants the full results page.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

// Long enough that a typist does not fire a request per keystroke, short
// enough that results feel immediate once they pause.
const DEBOUNCE_MS = 250;
const MIN_QUERY_LENGTH = 2;

const timers = new WeakMap();
const controllers = new WeakMap();

store(
	'suitemart/ajax-search',
	{
		state: {
			/**
			 * DOM id for the option currently rendered by wp-each.
			 *
			 * @return {string} Option id.
			 */
			get optionId() {
				const { listId, result } = getContext();
				return `${ listId }-option-${ result.id }`;
			},

			/**
			 * Whether the option rendered by wp-each is highlighted.
			 *
			 * @return {boolean} True when active.
			 */
			get isActiveOption() {
				const { results, activeIndex, result } = getContext();
				return results[ activeIndex ]?.id === result.id;
			},

			/**
			 * The highlighted option's id, for aria-activedescendant.
			 *
			 * @return {string} Option id, or '' when nothing is highlighted.
			 */
			get activeOptionId() {
				const { listId, results, activeIndex } = getContext();
				const active = results[ activeIndex ];
				return active ? `${ listId }-option-${ active.id }` : '';
			},

			/**
			 * Whether a finished search returned nothing.
			 *
			 * @return {boolean} True when the empty message should show.
			 */
			get isEmpty() {
				const { isLoading, query, results } = getContext();
				return (
					! isLoading &&
					query.length >= MIN_QUERY_LENGTH &&
					results.length === 0
				);
			},

			/**
			 * Politely announced result count.
			 *
			 * @return {string} Status text.
			 */
			get statusMessage() {
				const { isLoading, results, query } = getContext();

				if ( isLoading || query.length < MIN_QUERY_LENGTH ) {
					return '';
				}

				if ( results.length === 0 ) {
					return 'No matches found.';
				}

				return `${ results.length } suggestion${
					results.length === 1 ? '' : 's'
				} available.`;
			},
		},

		actions: {
			/**
			 * Debounces a search as the user types.
			 */
			handleInput() {
				const { ref } = getElement();
				const context = getContext();
				const root = ref.closest( '.sm-search' );

				context.query = ref.value.trim();
				context.activeIndex = -1;

				clearTimeout( timers.get( root ) );

				if ( context.query.length < MIN_QUERY_LENGTH ) {
					context.results = [];
					context.isOpen = false;
					context.isLoading = false;
					return;
				}

				context.isLoading = true;

				timers.set(
					root,
					setTimeout( () => runSearch( root, context ), DEBOUNCE_MS )
				);
			},

			/**
			 * Reopens the list when returning to a field that already has results.
			 */
			handleFocus() {
				const context = getContext();

				if ( context.results.length > 0 ) {
					context.isOpen = true;
				}
			},

			/**
			 * Keyboard handling for the combobox.
			 *
			 * @param {KeyboardEvent} event Key event.
			 */
			handleKeydown( event ) {
				const context = getContext();
				const count = context.results.length;

				if ( event.key === 'Escape' ) {
					context.isOpen = false;
					context.activeIndex = -1;
					return;
				}

				if ( count === 0 || ! context.isOpen ) {
					return;
				}

				if ( event.key === 'ArrowDown' ) {
					event.preventDefault();
					context.activeIndex = ( context.activeIndex + 1 ) % count;
				} else if ( event.key === 'ArrowUp' ) {
					event.preventDefault();
					context.activeIndex =
						( context.activeIndex - 1 + count ) % count;
				} else if ( event.key === 'Home' ) {
					event.preventDefault();
					context.activeIndex = 0;
				} else if ( event.key === 'End' ) {
					event.preventDefault();
					context.activeIndex = count - 1;
				} else if ( event.key === 'Enter' ) {
					const active = context.results[ context.activeIndex ];

					// With nothing highlighted the form submits as usual and
					// the visitor gets the full results page.
					if ( active ) {
						event.preventDefault();
						window.location.assign( active.url );
					}
				}
			},

			/**
			 * Highlights the hovered option so pointer and keyboard agree.
			 */
			hoverOption() {
				const context = getContext();
				context.activeIndex = context.results.findIndex(
					( r ) => r.id === context.result.id
				);
			},

			/**
			 * Closes the panel when the user clicks elsewhere.
			 *
			 * @param {MouseEvent} event Click event.
			 */
			handleDocumentClick( event ) {
				const { ref } = getElement();

				if ( ref && ! ref.contains( event.target ) ) {
					getContext().isOpen = false;
				}
			},
		},
	},
	{ lock: true }
);

/**
 * Fetches suggestions for the current query.
 *
 * @param {HTMLElement} root    The search block element.
 * @param {Object}      context Interactivity context for this block.
 */
async function runSearch( root, context ) {
	// Abandon a request that is still in flight; its results are already stale.
	controllers.get( root )?.abort();

	const controller = new AbortController();
	controllers.set( root, controller );

	const url = new URL( context.searchUrl );
	url.searchParams.set( 'search', context.query );
	url.searchParams.set( 'per_page', String( context.limit ) );
	url.searchParams.set( '_embed', 'true' );

	if ( context.postType !== 'any' ) {
		url.searchParams.set( 'subtype', context.postType );
	}

	try {
		const response = await fetch( url, {
			signal: controller.signal,
			headers: { Accept: 'application/json' },
		} );

		if ( ! response.ok ) {
			throw new Error( `Search failed: ${ response.status }` );
		}

		const found = await response.json();

		context.results = found.map( ( item ) => ( {
			id: item.id,
			title: item.title,
			url: item.url,
			image:
				item._embedded?.self?.[ 0 ]?.featured_media_src_url ??
				item._embedded?.[ 'wp:featuredmedia' ]?.[ 0 ]?.source_url ??
				'',
		} ) );

		context.isOpen = true;
	} catch ( error ) {
		if ( error.name === 'AbortError' ) {
			return;
		}

		// A failed suggestion lookup must not break the form: leaving the list
		// closed lets the visitor submit the search normally.
		context.results = [];
		context.isOpen = false;
	} finally {
		if ( controllers.get( root ) === controller ) {
			context.isLoading = false;
			controllers.delete( root );
		}
	}
}
