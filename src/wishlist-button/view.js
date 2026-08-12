/**
 * Wishlist button interactivity.
 *
 * The server rendered every button as "not saved" because it cannot read the
 * visitor's browser. This corrects them on load and keeps them correct as the
 * list changes — including from another button on the same page, or another
 * tab entirely.
 */

import { store, getContext } from '@wordpress/interactivity';
import { readList, toggleInList, onListChange } from '../_shared/product-list';

const LIST = 'wishlist';

const { state } = store( 'suitemart/wishlist', {
	actions: {
		/**
		 * Adds or removes this product.
		 */
		toggle() {
			const context = getContext();
			const { added, stored } = toggleInList( LIST, context.productId );

			// With site data blocked the write silently fails. Showing a filled
			// heart anyway would be a lie the next page load exposes, so the
			// button stays as it was and says why.
			if ( ! stored ) {
				state.notice = state.unavailableNotice;
				return;
			}

			// The button updates itself immediately; every other button for
			// the same product is updated by the change listener below.
			context.isSaved = added;
			context.label = added ? state.removeLabel : state.addLabel;
			state.notice = added ? state.addedNotice : state.removedNotice;
		},
	},

	callbacks: {
		/**
		 * Reflects the stored list, now and whenever it changes.
		 *
		 * @return {() => void} Teardown that removes the change listener.
		 */
		sync() {
			const context = getContext();

			const apply = ( ids ) => {
				const isSaved = ids.includes( context.productId );

				context.isSaved = isSaved;
				context.label = isSaved ? state.removeLabel : state.addLabel;
			};

			apply( readList( LIST ) );

			return onListChange( LIST, apply );
		},
	},
} );
