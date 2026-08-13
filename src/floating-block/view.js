/**
 * Floating block interactivity.
 *
 * Three ways in — straight away, after a scroll, after a delay — one way out,
 * and an optional memory of having been closed.
 */

import {
	store,
	getContext,
	getElement,
	withScope,
} from '@wordpress/interactivity';

const PREFIX = 'suitemart:floating:';

/**
 * Reads whether this panel has already been dismissed.
 *
 * @param {Window} view Window to read from.
 * @param {string} key  Panel key, empty when the panel has no memory.
 * @return {boolean} True when it was closed before.
 */
const wasDismissed = ( view, key ) => {
	if ( ! key ) {
		return false;
	}

	try {
		return 'dismissed' === view.localStorage.getItem( PREFIX + key );
	} catch {
		// Site data blocked. The panel comes back, which is the honest outcome:
		// the dismissal really was not kept.
		return false;
	}
};

/**
 * Records that this panel was dismissed.
 *
 * @param {Window} view Window to write to.
 * @param {string} key  Panel key, empty when the panel has no memory.
 */
const rememberDismissal = ( view, key ) => {
	if ( ! key ) {
		return;
	}

	try {
		view.localStorage.setItem( PREFIX + key, 'dismissed' );
	} catch {
		// Nothing to do but carry on: it still closes for this page view.
	}
};

store(
	'suitemart/floating-block',
	{
		actions: {
			/**
			 * Closes the panel, and remembers it if asked to.
			 */
			dismiss() {
				const context = getContext();
				const { ref } = getElement();

				context.isOpen = false;
				rememberDismissal( ref.ownerDocument.defaultView, context.key );
			},
		},

		callbacks: {
			/**
			 * Opens the panel when its trigger says to.
			 *
			 * @return {Function|undefined} Cleanup, when one is needed.
			 */
			decideVisibility() {
				const context = getContext();
				const { ref } = getElement();
				const view = ref.ownerDocument.defaultView;

				if ( wasDismissed( view, context.key ) ) {
					return undefined;
				}

				if ( 'scroll' === context.trigger ) {
					/*
					 * getContext() is called inside the scope rather than
					 * closed over: a proxy captured out here goes stale the
					 * moment it is read from a listener the store did not
					 * invoke, and writes to it land nowhere. That bug shipped
					 * once already, in back-to-top.
					 */
					const onScroll = withScope( () => {
						const live = getContext();

						if ( view.scrollY < live.threshold ) {
							return;
						}

						live.isOpen = true;
						view.removeEventListener( 'scroll', onScroll );
					} );

					view.addEventListener( 'scroll', onScroll, {
						passive: true,
					} );

					// In case the browser restored a position further down the
					// page than the threshold.
					onScroll();

					return () => view.removeEventListener( 'scroll', onScroll );
				}

				if ( 'delay' === context.trigger ) {
					const timer = view.setTimeout(
						withScope( () => {
							getContext().isOpen = true;
						} ),
						context.delay
					);

					return () => view.clearTimeout( timer );
				}

				// Immediate. Already open unless a remembered dismissal made
				// the server serve it closed.
				context.isOpen = true;

				return undefined;
			},
		},
	},
	{ lock: true }
);
