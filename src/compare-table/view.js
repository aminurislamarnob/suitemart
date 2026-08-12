/**
 * Compare table interactivity.
 *
 * The server rendered an empty table because the comparison list is in the
 * visitor's browser. This reads the list, fetches those products from the
 * WooCommerce Store API, and keeps the table in step with the buttons on the
 * page — and with other tabs.
 *
 * Everything is bound as text, never as markup. The Store API returns rendered
 * HTML for some fields (`price_html`, `description`), and putting that into the
 * page would mean trusting whatever a shop's product data happens to contain;
 * the price is formatted here from the numeric fields instead.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';
import { readList, writeList, onListChange } from '../_shared/product-list';
import { formatPrice } from '../_shared/price';

const LIST = 'compare';

const { state } = store( 'suitemart/compare', {
	actions: {
		/**
		 * Removes the product in this row.
		 *
		 * The row disappears immediately rather than waiting for a refetch:
		 * the data for the remaining products is already here.
		 */
		removeRow() {
			const { product } = getContext();
			const ids = readList( LIST ).filter( ( id ) => id !== product.id );

			if ( ! writeList( LIST, ids ) ) {
				state.statusText = state.unavailableNotice ?? state.errorText;
				return;
			}

			// onListChange re-renders the table, including from the buttons on
			// the page, so the row is not removed here as well.
			state.statusText = state.removedNotice ?? '';
		},
	},

	callbacks: {
		/**
		 * Loads the table, and reloads it whenever the list changes.
		 *
		 * @return {() => void} Teardown that removes the change listener.
		 */
		load() {
			const { ref } = getElement();

			const render = async ( ids ) => {
				if ( ids.length === 0 ) {
					state.products = [];
					state.hasProducts = false;
					state.isEmpty = true;
					return;
				}

				state.isLoading = true;

				try {
					const url = new URL( state.productsUrl, ref.baseURI );
					url.searchParams.set( 'include', ids.join( ',' ) );
					url.searchParams.set( 'per_page', String( ids.length ) );

					const response = await fetch( url, {
						headers: { Accept: 'application/json' },
					} );

					if ( ! response.ok ) {
						throw new Error( `HTTP ${ response.status }` );
					}

					const products = await response.json();

					// The API returns products in its own order and omits
					// anything deleted or unpublished since it was added, so
					// the list is re-ordered to match the reader's and the
					// gaps are dropped.
					const byId = new Map(
						products.map( ( product ) => [ product.id, product ] )
					);

					state.products = ids
						.filter( ( id ) => byId.has( id ) )
						.map( ( id ) => {
							const product = byId.get( id );
							const image = product.images?.[ 0 ];

							return {
								id: product.id,
								name: product.name,
								permalink: product.permalink,
								price: formatPrice( product.prices ),
								rating:
									product.review_count > 0
										? `${ product.average_rating } / 5`
										: state.noRatingText,
								stock: product.is_in_stock
									? state.inStockText
									: state.outStockText,
								sku: product.sku || '—',
								image: image?.thumbnail ?? '',
								imageAlt: image?.alt ?? '',
								removeLabel: `${ state.removeLabel }: ${ product.name }`,
							};
						} );

					state.hasProducts = state.products.length > 0;
					state.isEmpty = ! state.hasProducts;
					state.statusText = '';

					// Products that no longer exist are pruned, so a deleted
					// product does not sit in storage forever taking up one of
					// the four slots.
					if ( state.products.length !== ids.length ) {
						writeList(
							LIST,
							state.products.map( ( product ) => product.id )
						);
					}
				} catch {
					// The list is intact; only this fetch failed. Saying so
					// beats an empty table that looks like a cleared list.
					state.statusText = state.errorText;
					state.isEmpty = ! state.hasProducts;
				} finally {
					state.isLoading = false;
				}
			};

			render( readList( LIST ) );

			return onListChange( LIST, render );
		},
	},
} );
