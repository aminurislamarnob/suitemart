/**
 * Compare button interactivity.
 *
 * The server rendered every button as "not added" because it cannot read the
 * visitor's browser. This corrects them on load and keeps them correct as the
 * list changes — including from another button on the same page, another tab,
 * or the comparison table removing a product.
 */

import { store, getContext } from '@wordpress/interactivity';
import { readList, toggleInList, onListChange } from '../_shared/product-list';

const LIST = 'compare';

const { state } = store( 'suitemart/compare', {
	actions: {
		/**
		 * Adds or removes this product.
		 */
		toggle() {
			const context = getContext();
			const { added, evicted, stored } = toggleInList(
				LIST,
				context.productId,
				state.limit
			);

			// With site data blocked the write fails. Claiming success would be
			// a lie the next page load exposes.
			if ( ! stored ) {
				state.notice = state.unavailableNotice;
				return;
			}

			context.isAdded = added;
			context.label = added ? state.removeLabel : state.addLabel;

			// Silently dropping a product the reader chose earlier would leave
			// them wondering where it went, so the eviction is announced.
			if ( evicted ) {
				state.notice = state.evictedNotice;
			} else {
				state.notice = added ? state.addedNotice : state.removedNotice;
			}
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
				const isAdded = ids.includes( context.productId );

				context.isAdded = isAdded;
				context.label = isAdded ? state.removeLabel : state.addLabel;
			};

			apply( readList( LIST ) );

			return onListChange( LIST, apply );
		},
	},
} );
