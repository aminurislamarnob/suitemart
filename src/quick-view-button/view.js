import { store, getContext, getElement } from '@wordpress/interactivity';
import {
	focusableWithin,
	focusFirstWhenReady,
	trapFocus,
	lockScroll,
} from '../_shared/focus';
import { formatPrice } from '../_shared/price';

const returnFocusTo = new Map();
let abortController = null;

const parseHtml = ( html ) => {
	if ( ! html ) {
		return '';
	}
	const doc = new DOMParser().parseFromString( html, 'text/html' );
	return doc.body.textContent || '';
};

const { state } = store( 'suitemart/quick-view', {
	actions: {
		open() {
			const { productId } = getContext();
			state.activeProductId = productId;
			state.isOpen = true;

			if (
				! state.product ||
				state.product.id !== state.activeProductId
			) {
				state.isLoading = true;
				state.product = null;
				state.statusText = '';

				if ( abortController ) {
					abortController.abort();
				}
				abortController = new AbortController();

				const fetchProduct = async () => {
					try {
						// Store API URL logic, safe for query params
						let urlStr = state.productsUrl;
						if ( urlStr.includes( 'rest_route' ) ) {
							urlStr += `/${ state.activeProductId }`;
						} else {
							urlStr = urlStr.replace(
								/\/?$/,
								`/${ state.activeProductId }`
							);
						}

						// Need ref.baseURI for the URL constructor
						const url = new URL( urlStr, window.location.href );

						const response = await fetch( url, {
							headers: { Accept: 'application/json' },
							signal: abortController.signal,
						} );

						if ( ! response.ok ) {
							throw new Error( `HTTP ${ response.status }` );
						}

						const product = await response.json();
						const image = product.images?.[ 0 ];

						state.product = {
							id: product.id,
							name: product.name,
							permalink: product.permalink,
							price: formatPrice( product.prices ),
							shortDescription: parseHtml(
								product.short_description
							),
							image: image?.src ?? '', // Full size image for modal
							imageAlt: image?.alt ?? '',
							addToCartUrl:
								product.add_to_cart?.url ?? product.permalink,
							addToCartText:
								product.add_to_cart?.text ?? 'Add to cart',
						};
					} catch ( error ) {
						if ( error.name !== 'AbortError' ) {
							state.statusText = state.errorText;
						}
					} finally {
						state.isLoading = false;
					}
				};

				fetchProduct();
			}
		},

		close() {
			state.isOpen = false;
			if ( abortController ) {
				abortController.abort();
				abortController = null;
			}
		},

		handleKeydown( event ) {
			if ( event.key === 'Escape' && state.isOpen ) {
				event.preventDefault();
				state.isOpen = false;
				if ( abortController ) {
					abortController.abort();
					abortController = null;
				}
			}
		},
	},

	callbacks: {
		onToggle() {
			const { ref } = getElement();
			if ( ! ref ) {
				return;
			}

			const doc = ref.ownerDocument;

			if ( ! state.isOpen ) {
				const opener = returnFocusTo.get( 'quick-view' );
				if ( opener && doc.contains( opener ) ) {
					opener.focus();
				}
				returnFocusTo.delete( 'quick-view' );
				return;
			}

			returnFocusTo.set( 'quick-view', doc.activeElement );

			const panel = ref.querySelector( '.sm-quick-view-modal__dialog' );
			const releaseScroll = lockScroll( doc, true );
			const releaseFocus = trapFocus( ref, () =>
				focusableWithin( panel )
			);
			const cancelFocus = focusFirstWhenReady( () => panel );

			return () => {
				cancelFocus();
				releaseFocus();
				releaseScroll();
			};
		},
	},
} );
