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

store(
	'suitemart/countdown',
	{
		state: {
			get days() {
				return format( getContext().values.days, false );
			},
			get hours() {
				return format( getContext().values.hours, true );
			},
			get minutes() {
				return format( getContext().values.minutes, true );
			},
			get seconds() {
				return format( getContext().values.seconds, true );
			},
		},

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
						context.isExpired = true;
						// Nothing left to count; stop rather than keep firing.
						clearInterval( timer );
						return;
					}

					context.values = toUnits( remaining );
				};

				tick();
				const timer = setInterval( tick, 1000 );

				return () => clearInterval( timer );
			},
		},
	},
	{ lock: true }
);
