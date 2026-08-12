/**
 * Back to top interactivity.
 *
 * Scroll position is read from a passive listener rather than an
 * IntersectionObserver on a sentinel element, because the threshold is a
 * distance the editor chooses and a sentinel would have to be injected at that
 * distance into markup the block does not own.
 */

import {
	store,
	getContext,
	getElement,
	withScope,
} from '@wordpress/interactivity';

store(
	'suitemart/back-to-top',
	{
		actions: {
			/**
			 * Returns to the top of the page.
			 */
			toTop() {
				const { ref } = getElement();
				const view = ref.ownerDocument.defaultView;
				const doc = ref.ownerDocument;

				const still = view.matchMedia(
					'(prefers-reduced-motion: reduce)'
				).matches;

				view.scrollTo( {
					top: 0,
					behavior: still ? 'auto' : 'smooth',
				} );

				/*
				 * Scrolling moves the page but not the caret. Without this a
				 * keyboard user is returned to the top of the document while
				 * their focus stays on a button at the bottom of it, so the
				 * next Tab jumps them straight back down — the button appears
				 * to do nothing at all.
				 *
				 * Core's block-theme skip link already marks the start of the
				 * content, so reuse its target rather than inventing one.
				 */
				const target =
					doc.getElementById( 'wp--skip-link--target' ) ??
					doc.querySelector( 'main' ) ??
					doc.body;

				if ( ! target.hasAttribute( 'tabindex' ) ) {
					target.setAttribute( 'tabindex', '-1' );
				}

				target.focus( { preventScroll: true } );
			},
		},

		callbacks: {
			/**
			 * Tracks how far down the page the visitor is.
			 *
			 * @return {() => void} Teardown that removes the listener.
			 */
			watchScroll() {
				const { ref } = getElement();
				const view = ref.ownerDocument.defaultView;

				/*
				 * Wrapped in withScope, and getContext() called inside rather
				 * than closed over. A context proxy captured out here goes
				 * stale the moment it is read from a listener the store did not
				 * invoke: the first write lands and every read afterwards still
				 * sees the value the page was served with. The symptom is a
				 * button that appears on the first scroll and then never goes
				 * away again, which is exactly how this shipped for ten
				 * minutes.
				 */
				const update = withScope( () => {
					const context = getContext();
					const past = view.scrollY > context.threshold;

					// Written only on a change: this runs on every scroll
					// event, and assigning the same value would still wake the
					// Interactivity API's reactivity on each one.
					if ( past !== context.isVisible ) {
						context.isVisible = past;
					}
				} );

				// Immediately, because a page can load already scrolled — a
				// restored position, or a link to an anchor partway down.
				update();

				view.addEventListener( 'scroll', update, { passive: true } );

				return () => view.removeEventListener( 'scroll', update );
			},
		},
	},
	{ lock: true }
);
