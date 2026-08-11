/**
 * Focus utilities shared by the overlay components (navigation drawer,
 * off-canvas, and any dialog added later).
 *
 * Trapping focus is the part of an overlay that is easiest to get subtly wrong
 * and hardest to notice, so it lives in one place rather than being reimplemented
 * per block.
 */

const FOCUSABLE = [
	'a[href]',
	'button:not([disabled])',
	'input:not([disabled]):not([type="hidden"])',
	'select:not([disabled])',
	'textarea:not([disabled])',
	'[tabindex]:not([tabindex="-1"])',
].join( ',' );

/**
 * Returns the focusable elements inside a container, in document order.
 *
 * Elements hidden with `display: none` have no offsetParent and are excluded;
 * the currently focused element is kept even if it fails that check, so focus
 * is never lost mid-transition.
 *
 * @param {HTMLElement|null} container Element to search.
 * @return {HTMLElement[]} Focusable descendants.
 */
export const focusableWithin = ( container ) => {
	if ( ! container ) {
		return [];
	}

	const active = container.ownerDocument?.activeElement;

	return Array.from( container.querySelectorAll( FOCUSABLE ) ).filter(
		( el ) => el.offsetParent !== null || el === active
	);
};

/**
 * Moves focus into a container as soon as it is actually focusable.
 *
 * A single `requestAnimationFrame` is not enough. Two things have to have
 * happened before `.focus()` will do anything: the Interactivity API must have
 * flushed its DOM update, which it schedules itself, and the browser must have
 * applied the styles that make the element visible — `.focus()` is silently a
 * no-op on anything still `display: none` or `visibility: hidden`. One frame
 * usually covers both and intermittently does not, which strands a keyboard
 * user behind an overlay they just opened.
 *
 * So retry across a few frames and stop as soon as focus has genuinely landed.
 *
 * @param {() => HTMLElement|null} getContainer Returns the container, re-read each
 *                                              attempt because it may not exist yet.
 * @param {number}                 frames       How many frames to keep trying for.
 * @return {() => void} Teardown that cancels any pending attempt.
 */
export const focusFirstWhenReady = ( getContainer, frames = 10 ) => {
	let remaining = frames;
	let handle = 0;
	let view = globalThis;

	const attempt = () => {
		const container = getContainer();

		if ( container ) {
			view = container.ownerDocument.defaultView ?? view;

			const [ first ] = focusableWithin( container );
			const target = first ?? container;

			target.focus();

			// Confirm rather than assume: focus() reports nothing when it fails.
			if ( container.contains( container.ownerDocument.activeElement ) ) {
				return;
			}
		}

		remaining -= 1;

		if ( remaining > 0 ) {
			handle = view.requestAnimationFrame( attempt );
		}
	};

	handle = view.requestAnimationFrame( attempt );

	return () => view.cancelAnimationFrame( handle );
};

/**
 * Confines Tab to the given elements.
 *
 * @param {HTMLElement}         root     Element to listen on.
 * @param {() => HTMLElement[]} getItems Returns the trapped elements, evaluated
 *                                       on each keypress so content that appears
 *                                       while the overlay is open is included.
 * @return {() => void} Teardown that removes the listener.
 */
export const trapFocus = ( root, getItems ) => {
	const onKeydown = ( event ) => {
		if ( event.key !== 'Tab' ) {
			return;
		}

		const items = getItems();

		if ( items.length === 0 ) {
			return;
		}

		const active = root.ownerDocument.activeElement;
		const first = items[ 0 ];
		const last = items[ items.length - 1 ];

		if ( event.shiftKey && active === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && active === last ) {
			event.preventDefault();
			first.focus();
		}
	};

	root.addEventListener( 'keydown', onKeydown );

	return () => root.removeEventListener( 'keydown', onKeydown );
};

/**
 * Prevents the page behind an overlay from scrolling.
 *
 * Counts open overlays so that closing one while another is still open does not
 * release the lock early.
 *
 * @param {Document} doc      Document to lock.
 * @param {boolean}  isLocked Whether this caller wants the page locked.
 * @return {() => void} Teardown that releases this caller's lock.
 */
export const lockScroll = ( doc, isLocked ) => {
	const root = doc.documentElement;

	if ( ! isLocked ) {
		return () => {};
	}

	const depth = Number( root.dataset.smScrollLocks || 0 ) + 1;
	root.dataset.smScrollLocks = String( depth );
	root.classList.add( 'sm-has-overlay-open' );

	return () => {
		const next = Number( root.dataset.smScrollLocks || 1 ) - 1;

		if ( next <= 0 ) {
			delete root.dataset.smScrollLocks;
			root.classList.remove( 'sm-has-overlay-open' );
			return;
		}

		root.dataset.smScrollLocks = String( next );
	};
};
