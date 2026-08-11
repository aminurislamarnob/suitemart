/**
 * Share links interactivity.
 *
 * The network links are ordinary anchors and need nothing from this module.
 * Only the clipboard control does, which is why it stays hidden until this
 * runs and confirms the API is actually usable.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

store( 'suitemart/share', {
	callbacks: {
		/**
		 * Reveals the copy control only where the clipboard is available.
		 *
		 * `navigator.clipboard` is undefined outside a secure context, so on a
		 * plain-HTTP site the button would be present and permanently broken.
		 * Checking here means it is never shown in that case rather than
		 * failing when pressed.
		 */
		detectClipboard() {
			const { ref } = getElement();
			const view = ref.ownerDocument.defaultView;

			getContext().canCopy = !! view.navigator?.clipboard?.writeText;
		},
	},

	actions: {
		/**
		 * Copies the page URL and confirms it in the live region.
		 */
		*copy() {
			const context = getContext();
			const { ref } = getElement();
			const view = ref.ownerDocument.defaultView;

			try {
				yield view.navigator.clipboard.writeText( context.url );
			} catch {
				// Permission can be refused even in a secure context. Say
				// nothing rather than claim a copy that did not happen.
				return;
			}

			context.didCopy = true;
		},
	},
} );
