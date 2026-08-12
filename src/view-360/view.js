/**
 * 360° view interactivity.
 *
 * Dragging maps horizontal distance to frames: a drag the full width of the
 * viewer is one full rotation, whatever the frame count, so a 24-frame sequence
 * and a 72-frame one feel the same under the hand.
 *
 * `isCurrentFrame` below is deliberately a duplicate of the PHP closure in
 * render.php. Derived state has to exist on both sides for the server to render
 * the right frame and the browser to keep rendering the right one after that.
 */

import {
	store,
	getContext,
	getElement,
	withScope,
} from '@wordpress/interactivity';

// Timers, keyed by the block element, because they are the one thing that has
// to be reachable from a different callback than the one that created them.
const timers = new WeakMap();

const AUTO_ROTATE_MS = 90;

/**
 * Starts the automatic rotation for one viewer.
 *
 * The tick is wrapped in `withScope` and reads its context inside rather than
 * closing over one. A context proxy captured outside and read from a timer the
 * store did not invoke goes stale: the first write lands and every read after
 * it still sees the value the page was served with, so the sequence advances
 * one frame and then spins in place.
 *
 * @param {HTMLElement} root Block element, used as the timer's key.
 */
const startTimer = ( root ) => {
	timers.set(
		root,
		setInterval(
			withScope( () => {
				const context = getContext();
				context.index = wrap( context.index + 1, context.count );
			} ),
			AUTO_ROTATE_MS
		)
	);
};

/**
 * Wraps an index into the frame range.
 *
 * @param {number} index Any integer.
 * @param {number} count Number of frames.
 * @return {number} Index within 0..count-1.
 */
const wrap = ( index, count ) => ( ( index % count ) + count ) % count;

/**
 * Stops the automatic rotation, if it is running.
 *
 * @param {HTMLElement} root Block element.
 */
const stopTimer = ( root ) => {
	const timer = timers.get( root );

	if ( timer ) {
		clearInterval( timer );
		timers.delete( root );
	}
};

store(
	'suitemart/view-360',
	{
		state: {
			/**
			 * Whether the frame in scope is the one on show.
			 *
			 * Mirrored by a PHP closure in render.php, which reads the same two
			 * context values through wp_interactivity_get_context().
			 *
			 * @return {boolean} True for the current frame.
			 */
			get isCurrentFrame() {
				const { frame, index } = getContext();
				return frame === index;
			},
		},

		actions: {
			/**
			 * Steps one frame backwards.
			 */
			previous() {
				const context = getContext();
				context.index = wrap( context.index - 1, context.count );
				stopTimer( getElement().ref.closest( '.sm-view-360' ) );
				context.autoRotate = false;
			},

			/**
			 * Steps one frame forwards.
			 */
			next() {
				const context = getContext();
				context.index = wrap( context.index + 1, context.count );
				stopTimer( getElement().ref.closest( '.sm-view-360' ) );
				context.autoRotate = false;
			},

			/**
			 * Starts or stops the automatic rotation.
			 */
			toggleAutoRotate() {
				const context = getContext();
				const root = getElement().ref.closest( '.sm-view-360' );

				context.autoRotate = ! context.autoRotate;

				if ( context.autoRotate ) {
					startTimer( root );
				} else {
					stopTimer( root );
				}
			},

			/**
			 * Begins a drag.
			 *
			 * @param {PointerEvent} event Pointer down on the frame stack.
			 */
			startDrag( event ) {
				const context = getContext();
				const { ref } = getElement();

				stopTimer( ref.closest( '.sm-view-360' ) );
				context.autoRotate = false;

				context.dragging = true;
				context.origin = event.clientX;
				context.originIndex = context.index;

				// Pointer capture keeps the frames coming when the pointer
				// leaves the viewer mid-drag, which it will, because a spin
				// gesture is wider than the thing being spun.
				ref.setPointerCapture?.( event.pointerId );

				// Otherwise the browser starts its own image drag and the
				// rotation stops dead after a few pixels.
				event.preventDefault();
			},

			/**
			 * Rotates while dragging.
			 *
			 * @param {PointerEvent} event Pointer move.
			 */
			drag( event ) {
				const context = getContext();

				if ( ! context.dragging ) {
					return;
				}

				const { ref } = getElement();
				const width = ref.getBoundingClientRect().width;

				if ( ! width ) {
					return;
				}

				const travelled = ( event.clientX - context.origin ) / width;

				context.index = wrap(
					context.originIndex +
						Math.round( travelled * context.count ),
					context.count
				);
			},

			/**
			 * Ends a drag.
			 */
			endDrag() {
				const context = getContext();

				if ( context.dragging ) {
					context.dragging = false;
				}
			},
		},

		callbacks: {
			/**
			 * Marks the viewer as enhanced, and starts rotating if asked to.
			 *
			 * @return {() => void} Teardown that clears the timer.
			 */
			start() {
				const context = getContext();
				const { ref } = getElement();

				ref.classList.add( 'is-enhanced' );

				const still = ref.ownerDocument.defaultView?.matchMedia(
					'(prefers-reduced-motion: reduce)'
				)?.matches;

				// Rotating on its own is exactly the kind of movement that
				// setting asks to be spared. The controls still work.
				if ( context.autoRotate && ! still ) {
					startTimer( ref );
				} else if ( context.autoRotate ) {
					context.autoRotate = false;
				}

				return () => stopTimer( ref );
			},
		},
	},
	{ lock: true }
);
