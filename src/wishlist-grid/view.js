/**
 * Wishlist grid interactivity.
 *
 * Shares the `suitemart/wishlist` store with the buttons, so saving a product
 * anywhere on the page updates this grid and removing one here unfills every
 * heart pointing at it.
 *
 * The wishlist has no ceiling — a reader can save as many products as they
 * like — so unlike the comparison table this cannot assume one request covers
 * the list. The Store API caps `per_page` at 100, and a URL with a few thousand
 * ids in it would be refused by the server long before that mattered, so the
 * ids are fetched in chunks.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';
import { readList, writeList, onListChange } from '../_shared/product-list';
import { formatPrice } from '../_shared/price';

const LIST = 'wishlist';

// The Store API's own maximum. Requesting more is an error, not a truncation.
const PER_REQUEST = 100;

/**
 * Splits a list into fixed-size chunks.
 *
 * @param {number[]} ids  Product ids.
 * @param {number}   size Chunk size.
 * @return {number[][]} Chunks, in order.
 */
const chunk = ( ids, size ) => {
	const chunks = [];

	for ( let index = 0; index < ids.length; index += size ) {
		chunks.push( ids.slice( index, index + size ) );
	}

	return chunks;
};

const { state } = store( 'suitemart/wishlist', {
	actions: {
		/**
		 * Removes the product in this card.
		 */
		removeSaved() {
			const { product } = getContext();
			const ids = readList( LIST ).filter( ( id ) => id !== product.id );

			if ( ! writeList( LIST, ids ) ) {
				state.statusText = state.unavailableNotice ?? state.errorText;
				return;
			}

			// The change listener re-renders the grid and corrects every
			// button on the page, so nothing is removed by hand here.
			state.statusText = state.removedNotice ?? '';
		},
	},

	callbacks: {
		/**
		 * Loads the grid, and reloads it whenever the list changes.
		 *
		 * @return {() => void} Teardown that removes the change listener.
		 */
		loadGrid() {
			const { ref } = getElement();

			const fetchChunk = async ( ids ) => {
				const url = new URL( state.productsUrl, ref.baseURI );
				url.searchParams.set( 'include', ids.join( ',' ) );
				url.searchParams.set( 'per_page', String( ids.length ) );

				const response = await fetch( url, {
					headers: { Accept: 'application/json' },
				} );

				if ( ! response.ok ) {
					throw new Error( `HTTP ${ response.status }` );
				}

				return response.json();
			};

			const render = async ( ids ) => {
				if ( ids.length === 0 ) {
					state.products = [];
					state.hasProducts = false;
					state.isEmpty = true;
					return;
				}

				try {
					const pages = await Promise.all(
						chunk( ids, PER_REQUEST ).map( fetchChunk )
					);

					const byId = new Map(
						pages
							.flat()
							.map( ( product ) => [ product.id, product ] )
					);

					// Newest first: a wishlist is a record of what caught the
					// reader's eye, and the thing they just saved should not be
					// at the bottom of a long page.
					state.products = ids
						.filter( ( id ) => byId.has( id ) )
						.reverse()
						.map( ( id ) => {
							const product = byId.get( id );
							const image = product.images?.[ 0 ];

							return {
								id: product.id,
								name: product.name,
								permalink: product.permalink,
								price: formatPrice( product.prices ),
								stock: product.is_in_stock
									? state.inStockText
									: state.outStockText,
								image: image?.thumbnail ?? '',
								// The name is already the link text, so an
								// image alt repeating it would be announced
								// twice. Woo's own alt is used when it has one.
								imageAlt: image?.alt ?? '',
								removeLabel: `${ state.removeLabel }: ${ product.name }`,
							};
						} );

					state.hasProducts = state.products.length > 0;
					state.isEmpty = ! state.hasProducts;
					state.statusText = '';

					// Products deleted since they were saved are pruned rather
					// than left in storage as ids nothing on screen can remove.
					if ( state.products.length !== ids.length ) {
						writeList(
							LIST,
							ids.filter( ( id ) => byId.has( id ) )
						);
					}
				} catch {
					// The saved list is intact; only this fetch failed. An
					// empty grid here would read as "you saved nothing".
					state.statusText = state.errorText;
					state.isEmpty = ! state.hasProducts;
				}
			};

			render( readList( LIST ) );

			return onListChange( LIST, render );
		},
	},
} );
