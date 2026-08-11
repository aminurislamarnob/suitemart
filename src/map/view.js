/**
 * Map interactivity.
 *
 * Only used when the block is set to ask before loading. Without that, the
 * server renders a complete iframe and this module does nothing.
 */

import { store, getContext } from '@wordpress/interactivity';

store( 'suitemart/map', {
	actions: {
		/**
		 * Loads the map after the reader asks for it.
		 *
		 * Moving the held URL into `src` is what makes the first request to
		 * Google — until this runs, nothing has been sent.
		 */
		load() {
			const context = getContext();

			context.src = context.pendingSrc;
			context.isLoaded = true;
		},
	},
} );
