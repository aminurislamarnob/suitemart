/**
 * Add to cart button interactivity.
 *
 * One product, one request. The interesting parts are both about what is *not*
 * kept here: the nonce, which is read fresh at interaction time rather than
 * rendered into cacheable markup, and the button's state, which lives in
 * context because a shop page carries a dozen of these and global state would
 * be shared by all of them.
 */

import { store, getContext } from '@wordpress/interactivity';

const { state } = store( 'suitemart/add-to-cart', {
	actions: {
		/**
		 * Adds this product to the cart.
		 */
		async add() {
			const context = getContext();

			if ( context.isAdding ) {
				return;
			}

			const idle = context.label;

			context.isAdding = true;
			context.notice = '';
			context.label = state.addingLabel;

			try {
				/*
				 * A nonce rendered into the page is served stale from behind a
				 * full-page cache and every add then fails with a 403, so one
				 * is read off the Store API here instead.
				 */
				const cart = await fetch( state.cartApiUrl, {
					headers: { Accept: 'application/json' },
					credentials: 'same-origin',
				} );

				const nonce = cart.headers.get( 'Nonce' ) ?? '';

				const response = await fetch( state.addItemUrl, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						Accept: 'application/json',
						Nonce: nonce,
					},
					credentials: 'same-origin',
					body: JSON.stringify( {
						id: context.productId,
						quantity: 1,
					} ),
				} );

				if ( ! response.ok ) {
					const data = await response.json().catch( () => ( {} ) );
					throw new Error( data.message || state.errorMsg );
				}

				context.isAdding = false;
				context.isAdded = true;
				context.label = state.addedLabel;
				context.notice = state.addedNotice;

				/*
				 * Tell the rest of the page. Woo's own mini cart listens for
				 * this, so the count in the header follows an add made from a
				 * card without the page being reloaded.
				 */
				window.dispatchEvent(
					new CustomEvent( 'wc-blocks_added_to_cart', {
						bubbles: true,
					} )
				);
			} catch ( error ) {
				context.isAdding = false;
				context.label = idle;
				context.notice = error.message || state.errorMsg;
			}
		},
	},
} );
