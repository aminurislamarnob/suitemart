/**
 * Off-canvas panel interactivity.
 *
 * The panel and its trigger are separate blocks anywhere on the page, so which
 * panel is open lives in global state keyed by panel id rather than in either
 * block's local context.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';
import {
	focusableWithin,
	focusFirstWhenReady,
	trapFocus,
	lockScroll,
} from '../_shared/focus';
import { OFF_CANVAS_LOCK } from '../_shared/off-canvas-lock';

// Remembers what had focus before each panel opened, so it can be restored.
const returnFocusTo = new Map();

const { state } = store(
	'suitemart/off-canvas',
	{
		state: {
			// Id of the panel currently open, or '' when none is.
			openPanel: '',

			/**
			 * Whether this panel is the open one.
			 *
			 * @return {boolean} True when open.
			 */
			get isOpen() {
				return state.openPanel === getContext().panelId;
			},
		},

		actions: {
			/**
			 * Closes whichever panel is open.
			 */
			close() {
				state.openPanel = '';
			},

			/**
			 * Escape closes the panel, as required of a modal dialog.
			 *
			 * @param {KeyboardEvent} event Key event.
			 */
			handleKeydown( event ) {
				if ( event.key === 'Escape' && state.isOpen ) {
					event.preventDefault();
					state.openPanel = '';
				}
			},
		},

		callbacks: {
			/**
			 * Applies the behaviours that make this a dialog rather than a div.
			 *
			 * @return {(() => void)|undefined} Teardown when open.
			 */
			onToggle() {
				const { ref } = getElement();

				if ( ! ref ) {
					return;
				}

				const context = getContext();
				const doc = ref.ownerDocument;

				if ( state.openPanel !== context.panelId ) {
					// Return focus to whatever opened the panel. Leaving focus
					// on a now-hidden element strands keyboard users at the top
					// of the document.
					const opener = returnFocusTo.get( context.panelId );

					if ( opener && doc.contains( opener ) ) {
						opener.focus();
					}

					returnFocusTo.delete( context.panelId );
					return;
				}

				returnFocusTo.set( context.panelId, doc.activeElement );

				const panel = ref.querySelector( '.sm-off-canvas__panel' );
				const releaseScroll = lockScroll( doc, true );
				const releaseFocus = trapFocus( ref, () =>
					focusableWithin( panel )
				);

				// Move focus into the dialog once it is genuinely focusable.
				const cancelFocus = focusFirstWhenReady( () => panel );

				return () => {
					cancelFocus();
					releaseFocus();
					releaseScroll();
				};
			},
		},
	},
	{ lock: OFF_CANVAS_LOCK }
);
