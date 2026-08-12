import { store, getElement, getContext } from '@wordpress/interactivity';
import {
	focusableWithin,
	focusFirstWhenReady,
	trapFocus,
	lockScroll,
} from '../_shared/focus';

// Keyed by modal id, because a product grid renders one size guide per card and
// each has to send focus back to its own trigger.
const returnFocusTo = new Map();

const { state } = store( 'suitemart/size-guide', {
	state: {
		// The id of the one open modal, or '' when none is. Storing the id
		// rather than a boolean is what stops every guide on the page opening
		// at once, and gives "only one modal at a time" for free.
		openId: '',

		get isOpen() {
			const { modalId } = getContext();
			return state.openId !== '' && state.openId === modalId;
		},
	},

	actions: {
		open() {
			state.openId = getContext().modalId;
		},

		close() {
			state.openId = '';
		},

		handleKeydown( event ) {
			// Only the open modal reacts, or every closed instance on the page
			// would also handle the keystroke.
			if ( event.key === 'Escape' && state.isOpen ) {
				event.preventDefault();
				state.openId = '';
			}
		},
	},

	callbacks: {
		onToggle() {
			const { ref } = getElement();
			if ( ! ref ) {
				return;
			}

			const { modalId } = getContext();
			const doc = ref.ownerDocument;

			if ( ! state.isOpen ) {
				const opener = returnFocusTo.get( modalId );
				if ( opener && doc.contains( opener ) ) {
					opener.focus();
				}
				returnFocusTo.delete( modalId );
				return;
			}

			returnFocusTo.set( modalId, doc.activeElement );

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
