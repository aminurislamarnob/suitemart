/**
 * Hotspot interactivity.
 *
 * Each marker keeps its own open flag in context, which is what lets the server
 * render `aria-expanded` and `hidden` correctly before any script loads. The
 * cost is that no marker can reach into another's state, so "only one panel at
 * a time" falls out of the outside-click handler instead: a click on a second
 * marker is, from the first marker's point of view, a click outside it.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

store(
	'suitemart/hotspots',
	{
		actions: {
			/**
			 * Opens or closes this marker's panel.
			 */
			toggle() {
				const context = getContext();
				context.isOpen = ! context.isOpen;
			},
		},

		callbacks: {
			/**
			 * Closes the panel when the click landed outside this marker.
			 *
			 * @param {MouseEvent} event Document click.
			 */
			closeOnOutsideClick( event ) {
				const context = getContext();

				if ( ! context.isOpen ) {
					return;
				}

				const { ref } = getElement();

				if ( ! ref.contains( event.target ) ) {
					context.isOpen = false;
				}
			},

			/**
			 * Closes on Escape and returns focus to the marker.
			 *
			 * Focus has to move back deliberately: the panel can hold links, and
			 * hiding the element focus is inside drops it to the top of the
			 * document, which loses a keyboard user's place entirely.
			 *
			 * @param {KeyboardEvent} event Document keydown.
			 */
			closeOnEscape( event ) {
				const context = getContext();

				if ( ! context.isOpen || event.key !== 'Escape' ) {
					return;
				}

				const { ref } = getElement();

				context.isOpen = false;

				if ( ref.contains( ref.ownerDocument.activeElement ) ) {
					ref.querySelector( '.sm-hotspots__marker' )?.focus();
				}
			},
		},
	},
	{ lock: true }
);
