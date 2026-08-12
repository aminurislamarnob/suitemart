import { store, getContext } from '@wordpress/interactivity';

/**
 * Adds working days to a date, skipping Saturday and Sunday.
 *
 * Mirrors the same arithmetic in render.php so a recomputed window matches the
 * one the server would have produced today.
 *
 * @param {Date}   from Starting date.
 * @param {number} days Working days to add.
 * @return {Date} The resulting date.
 */
const addWorkingDays = ( from, days ) => {
	const date = new Date( from.getTime() );
	let remaining = days;

	while ( remaining > 0 ) {
		date.setDate( date.getDate() + 1 );
		const day = date.getDay();
		if ( day !== 0 && day !== 6 ) {
			remaining -= 1;
		}
	}

	return date;
};

const formatter = new Intl.DateTimeFormat( undefined, {
	month: 'short',
	day: 'numeric',
} );

/** @return {string} Today as YYYY-MM-DD in the visitor's own timezone. */
const today = () => {
	const now = new Date();
	const pad = ( n ) => String( n ).padStart( 2, '0' );
	return `${ now.getFullYear() }-${ pad( now.getMonth() + 1 ) }-${ pad(
		now.getDate()
	) }`;
};

store( 'suitemart/estimated-delivery', {
	callbacks: {
		refresh() {
			const context = getContext();

			// Rendered today, so the server's text — which is formatted with
			// the site's own date format and translations — is already right.
			if ( context.renderedOn === today() ) {
				return;
			}

			const now = new Date();
			const min = formatter.format(
				addWorkingDays( now, context.minDays )
			);
			const max = formatter.format(
				addWorkingDays( now, context.maxDays )
			);

			context.text =
				min === max
					? context.singleLabel.replace( '%s', min )
					: context.rangeLabel
							.replace( '%1$s', min )
							.replace( '%2$s', max );
		},
	},
} );
