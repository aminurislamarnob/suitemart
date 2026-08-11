/**
 * Off-canvas trigger interactivity.
 *
 * Extends the same store the panel defines. Both blocks pass the shared lock
 * token so they can extend one another, and the store works whichever of the
 * two happens to hydrate first.
 */

import { store, getContext } from '@wordpress/interactivity';
import { OFF_CANVAS_LOCK } from '../_shared/off-canvas-lock';

const { state } = store(
	'suitemart/off-canvas',
	{
		state: {
			openPanel: '',

			/**
			 * Whether the panel this button controls is open.
			 *
			 * @return {boolean} True when open.
			 */
			get isOpen() {
				return state.openPanel === getContext().panelId;
			},
		},

		actions: {
			/**
			 * Opens this button's panel, or closes it if it is already open.
			 */
			toggle() {
				const { panelId } = getContext();
				state.openPanel = state.openPanel === panelId ? '' : panelId;
			},
		},
	},
	{ lock: OFF_CANVAS_LOCK }
);
