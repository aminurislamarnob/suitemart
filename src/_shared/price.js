/**
 * Formatting for WooCommerce Store API prices.
 *
 * Shared by every block that renders products the browser fetched rather than
 * the server rendered — the comparison table and the wishlist grid so far.
 */

/**
 * Formats a Store API price object into a display string.
 *
 * The API returns the amount in minor units as a string ("1999") alongside the
 * store's own separators, symbol and placement, because all of those are shop
 * settings rather than properties of the currency. Intl.NumberFormat is not
 * used for that reason: it would apply the browser's idea of how the currency
 * is written and quietly disagree with every price the server rendered.
 *
 * @param {Object} prices Store API `prices` object.
 * @return {string} Formatted price, or an empty string when unavailable.
 */
export const formatPrice = ( prices ) => {
	if ( ! prices || typeof prices.price !== 'string' ) {
		return '';
	}

	const amount = Number( prices.price );

	if ( ! Number.isFinite( amount ) ) {
		return '';
	}

	const minorUnit = Number( prices.currency_minor_unit ?? 2 );
	const value = amount / 10 ** minorUnit;
	const [ whole, fraction = '' ] = value.toFixed( minorUnit ).split( '.' );

	const grouped = whole.replace(
		/\B(?=(\d{3})+(?!\d))/g,
		prices.currency_thousand_separator ?? ','
	);

	const number =
		minorUnit > 0
			? `${ grouped }${
					prices.currency_decimal_separator ?? '.'
			  }${ fraction }`
			: grouped;

	// The prefix and suffix already carry the symbol, on whichever side the
	// store puts it — adding `currency_symbol` as well produced "$$19.99".
	return `${ prices.currency_prefix ?? '' }${ number }${
		prices.currency_suffix ?? ''
	}`;
};
