/**
 * Image comparison interactivity.
 *
 * Almost nothing, and that is the point: the `<input type="range">` in the
 * markup already handles pointer drags, touch, arrow keys, Home and End, and
 * announcing its own value. All that is left is turning its value into the
 * custom property the stylesheet clips against.
 *
 * The finished string goes into context rather than the number, so the server
 * can render the same binding before this module ever loads.
 */

import { store, getContext } from '@wordpress/interactivity';

store(
	'suitemart/compare-images',
	{
		actions: {
			/**
			 * Moves the wipe to the slider's value.
			 *
			 * @param {InputEvent} event Input event from the range control.
			 */
			reveal( event ) {
				const value = Number( event.target.value );

				if ( Number.isNaN( value ) ) {
					return;
				}

				getContext().frameStyle = `--sm-compare-position:${ value }%;`;
			},
		},
	},
	{ lock: true }
);
