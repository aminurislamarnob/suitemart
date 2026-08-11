/**
 * Marquee interactivity.
 *
 * Turns the static row the server rendered into a seamless loop:
 *
 *   1. Clone the track so the lane holds two identical copies.
 *   2. Measure one copy and derive a duration from the authored px/second, so
 *      a short strip and a long one travel at the same visual speed.
 *   3. Add `is-animating`, which is the only thing that starts the animation
 *      and the only thing that reveals the pause button.
 *
 * If any of that does not happen — no JavaScript, reduced motion preferred —
 * the row simply stays static and scrollable.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

/**
 * Strips `id` attributes from a subtree.
 *
 * The clone is a duplicate of real content, so it carries real ids with it.
 * Leaving them would produce duplicate ids and break every `aria-labelledby`
 * and in-page link that points at the original.
 *
 * @param {Element} root Cloned subtree.
 */
const stripIds = ( root ) => {
	if ( root.id ) {
		root.removeAttribute( 'id' );
	}

	root.querySelectorAll( '[id]' ).forEach( ( node ) =>
		node.removeAttribute( 'id' )
	);
};

// `state` is destructured from the store so the label getter can read the two
// strings render.php seeded. They have to live in state rather than on the
// element: the `aria-label` binding is evaluated on the server as well as in
// the browser, and an expression the server cannot resolve *removes* the
// attribute, which left this button with no accessible name at all.
const { state } = store( 'suitemart/marquee', {
	state: {
		get toggleLabel() {
			return getContext().isPlaying ? state.pauseLabel : state.playLabel;
		},
	},

	actions: {
		toggle() {
			const context = getContext();
			context.isPlaying = ! context.isPlaying;
		},
	},

	callbacks: {
		/**
		 * Clones the track and starts the loop.
		 *
		 * @return {() => void} Teardown that removes the clone and observer.
		 */
		setup() {
			const { ref } = getElement();
			const view = ref.ownerDocument.defaultView;

			if (
				view.matchMedia( '(prefers-reduced-motion: reduce)' ).matches
			) {
				return () => {};
			}

			const lane = ref.querySelector( '.sm-marquee__lane' );
			const track = ref.querySelector( '.sm-marquee__track' );

			if ( ! lane || ! track ) {
				return () => {};
			}

			const context = getContext();

			const clone = track.cloneNode( true );
			stripIds( clone );
			// The clone is decoration; a screen reader must not read the strip
			// twice, and Tab must not walk through it.
			clone.setAttribute( 'aria-hidden', 'true' );
			clone.inert = true;
			lane.appendChild( clone );

			const measure = () => {
				const width = track.getBoundingClientRect().width;

				if ( width <= 0 ) {
					return;
				}

				// px ÷ px-per-second = seconds for one copy to pass.
				ref.style.setProperty(
					'--sm-marquee-duration',
					`${ width / context.speed }s`
				);
				context.isAnimating = true;
			};

			measure();

			// Font loading and image decode both change the track's width
			// after first paint, which would leave the speed wrong.
			const observer = new view.ResizeObserver( measure );
			observer.observe( track );

			return () => {
				observer.disconnect();
				clone.remove();
			};
		},
	},
} );
