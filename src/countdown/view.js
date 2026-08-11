/**
 * Countdown interactivity.
 *
 * The server already rendered correct values, so this only keeps them ticking.
 * One interval per block, cleaned up when the block unmounts — a countdown left
 * running after client-side navigation is a slow leak on a shop that uses one
 * on every product card.
 */

import { store, getContext } from '@wordpress/interactivity';

/**
 * Splits a remaining duration into whole units.
 *
 * @param {number} ms Milliseconds remaining.
 * @return {{days:number,hours:number,minutes:number,seconds:number}} Unit values.
 */
const toUnits = ( ms ) => {
	const total = Math.max( 0, Math.floor( ms / 1000 ) );

	return {
		days: Math.floor( total / 86400 ),
		hours: Math.floor( ( total % 86400 ) / 3600 ),
		minutes: Math.floor( ( total % 3600 ) / 60 ),
		seconds: total % 60,
	};
};

/**
 * Formats a unit value for display.
 *
 * Days are not padded — "07 Days" reads as a mistake — but the time units are,
 * so the digits do not jump width as they count down.
 *
 * @param {number}  value Unit value.
 * @param {boolean} pad   Whether to pad to two digits.
 * @return {string} Display string.
 */
const format = ( value, pad ) =>
	pad ? String( value ).padStart( 2, '0' ) : String( value );

/**
 * Formats a whole set of units the way render.php does.
 *
 * The two must agree: the server prints the first frame and this takes over
 * from it, so any difference in padding shows as a flicker on load.
 *
 * @param {{days:number,hours:number,minutes:number,seconds:number}} values Unit values.
 * @return {{days:string,hours:string,minutes:string,seconds:string}} Display strings.
 */
const toDisplay = ( values ) => ( {
	days: format( values.days, false ),
	hours: format( values.hours, true ),
	minutes: format( values.minutes, true ),
	seconds: format( values.seconds, true ),
} );

store(
	'suitemart/countdown',
	{
		callbacks: {
			/**
			 * Starts ticking, and stops when the deadline passes.
			 *
			 * @return {() => void} Teardown that clears the interval.
			 */
			start() {
				const context = getContext();

				const tick = () => {
					const remaining = context.endTimestamp - Date.now();

					if ( remaining <= 0 ) {
						context.values = toUnits( 0 );
						context.display = toDisplay( context.values );
						context.isExpired = true;
						// Nothing left to count; stop rather than keep firing.
						clearInterval( timer );
						return;
					}

					context.values = toUnits( remaining );
					context.display = toDisplay( context.values );
				};

				tick();
				const timer = setInterval( tick, 1000 );

				return () => clearInterval( timer );
			},
		},
	},
	{ lock: true }
);
