import { store, getContext } from '@wordpress/interactivity';
import { formatPrice } from '../_shared/price';

/**
 * Adds one product to the cart, surfacing the API's own message on failure.
 *
 * @param {string} url      Store API add-item route.
 * @param {number} id       Product id.
 * @param {string} nonce    Store API nonce.
 * @param {string} fallback Message to use when the API sends none.
 */
const addItem = async ( url, id, nonce, fallback ) => {
	const response = await fetch( url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			Accept: 'application/json',
			Nonce: nonce,
		},
		credentials: 'same-origin',
		body: JSON.stringify( { id, quantity: 1 } ),
	} );

	if ( ! response.ok ) {
		const data = await response.json().catch( () => ( {} ) );
		throw new Error( data.message || fallback );
	}

	return response;
};

const { state } = store( 'suitemart/fbt-products', {
	state: {
		get formattedTotal() {
			const { products, currencySettings } = getContext();

			if ( state.selectedIds.length === 0 ) {
				return formatPrice( {
					...currencySettings,
					price: '0',
				} );
			}

			let totalMinorUnits = 0;
			for ( const id of state.selectedIds ) {
				if ( products[ id ] !== undefined ) {
					totalMinorUnits += products[ id ];
				}
			}

			return formatPrice( {
				...currencySettings,
				price: String( totalMinorUnits ),
			} );
		},
	},
	actions: {
		toggleProduct( event ) {
			const { productId } = getContext();
			if ( event.target.checked ) {
				if ( ! state.selectedIds.includes( productId ) ) {
					state.selectedIds.push( productId );
				}
			} else {
				state.selectedIds = state.selectedIds.filter(
					( id ) => id !== productId
				);
			}
		},
		async addToCart() {
			if ( state.selectedIds.length === 0 || state.isAdding ) {
				return;
			}

			state.isAdding = true;
			state.error = '';

			try {
				// Read the nonce at interaction time rather than from the
				// markup. A nonce rendered into the page is served stale from
				// behind a full-page cache, and every add then fails with 403.
				const cart = await fetch( state.cartApiUrl, {
					headers: { Accept: 'application/json' },
					credentials: 'same-origin',
				} );
				const nonce = cart.headers.get( 'Nonce' ) ?? '';

				// Store API cart writes all mutate one session. Sent
				// concurrently they race, and a guest with no session yet gets
				// a separate cart per request — so items go missing. One at a
				// time is the only safe order.
				for ( const id of state.selectedIds ) {
					await addItem(
						state.addItemUrl,
						id,
						nonce,
						state.errorMsg
					);
				}

				window.location.href = state.cartUrl;
			} catch ( error ) {
				state.isAdding = false;
				state.error = error.message || state.errorMsg;
			}
		},
	},
	callbacks: {
		isSelected() {
			const { productId } = getContext();
			return state.selectedIds.includes( productId );
		},
	},
} );
