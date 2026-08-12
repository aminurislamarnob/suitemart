import { store, getElement } from '@wordpress/interactivity';
import {
	focusableWithin,
	focusFirstWhenReady,
	trapFocus,
	lockScroll,
} from '../_shared/focus';

const returnFocusTo = new Map();

const { state } = store( 'suitemart/size-guide', {
	state: {
		isOpen: false,
	},

	actions: {
		close() {
			state.isOpen = false;
		},

		handleKeydown( event ) {
			if ( event.key === 'Escape' && state.isOpen ) {
				event.preventDefault();
				state.isOpen = false;
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
				const opener = returnFocusTo.get( 'size-guide' );
				if ( opener && doc.contains( opener ) ) {
					opener.focus();
				}
				returnFocusTo.delete( 'size-guide' );
				return;
			}

			returnFocusTo.set( 'size-guide', doc.activeElement );

			const panel = ref.querySelector( '.sm-size-guide__dialog' );
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
