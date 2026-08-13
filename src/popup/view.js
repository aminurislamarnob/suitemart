/**
 * Popup interactivity.
 *
 * Everything modal about this block belongs to `<dialog>`: focus moves in and
 * is trapped, the page behind goes inert, Escape closes, focus returns to
 * whatever had it. What is left here is when to open, and whether this visitor
 * has seen it before.
 */

import {
	store,
	getContext,
	getElement,
	withScope,
} from '@wordpress/interactivity';

const PREFIX = 'suitemart:popup:';

/**
 * Reads whether this popup has already been shown to this browser.
 *
 * @param {Window} view Window to read from.
 * @param {string} key  Popup key, empty when the popup has no memory.
 * @return {boolean} True when it has been shown before.
 */
const wasSeen = ( view, key ) => {
	if ( ! key ) {
		return false;
	}

	try {
		return 'seen' === view.localStorage.getItem( PREFIX + key );
	} catch {
		// Site data blocked. It shows again, which is the honest outcome.
		return false;
	}
};

/**
 * Records that this popup has been shown.
 *
 * Written when it opens rather than when it closes, because someone who
 * ignored it and navigated away has still been shown it once.
 *
 * @param {Window} view Window to write to.
 * @param {string} key  Popup key, empty when the popup has no memory.
 */
const markSeen = ( view, key ) => {
	if ( ! key ) {
		return;
	}

	try {
		view.localStorage.setItem( PREFIX + key, 'seen' );
	} catch {
		// Nothing to do but carry on.
	}
};

/**
 * The dialog an event landed in.
 *
 * @param {HTMLElement} ref Element the directive is on.
 * @return {HTMLDialogElement|null} The dialog.
 */
const dialogOf = ( ref ) =>
	'DIALOG' === ref.tagName ? ref : ref.closest( 'dialog' );

store(
	'suitemart/popup',
	{
		actions: {
			/**
			 * Closes the dialog.
			 */
			close() {
				dialogOf( getElement().ref )?.close();
			},

			/**
			 * Closes when the click landed outside the dialog box.
			 *
			 * A click on the backdrop is reported against the dialog itself,
			 * so the target alone cannot tell it apart from a click on the
			 * dialog's own padding. The coordinates can.
			 *
			 * @param {MouseEvent} event Click.
			 */
			onBackdropClick( event ) {
				const context = getContext();
				const dialog = dialogOf( getElement().ref );

				if (
					! context.overlayClose ||
					! dialog ||
					event.target !== dialog
				) {
					return;
				}

				const box = dialog.getBoundingClientRect();
				const inside =
					event.clientX >= box.left &&
					event.clientX <= box.right &&
					event.clientY >= box.top &&
					event.clientY <= box.bottom;

				if ( ! inside ) {
					dialog.close();
				}
			},
		},

		callbacks: {
			/**
			 * Opens the dialog when its trigger says to.
			 *
			 * @return {Function|undefined} Cleanup, when one is needed.
			 */
			watchForTrigger() {
				const context = getContext();
				const { ref } = getElement();
				const doc = ref.ownerDocument;
				const view = doc.defaultView;
				const dialog = ref.querySelector( 'dialog' );

				if ( ! dialog || wasSeen( view, context.key ) ) {
					return undefined;
				}

				const open = () => {
					if ( dialog.open ) {
						return;
					}

					dialog.showModal();
					markSeen( view, getContext().key );
				};

				if ( 'scroll' === context.trigger ) {
					/*
					 * getContext() inside the scope rather than closed over: a
					 * proxy captured outside goes stale as soon as it is read
					 * from a listener the store did not invoke.
					 */
					const onScroll = withScope( () => {
						if ( view.scrollY < getContext().threshold ) {
							return;
						}

						view.removeEventListener( 'scroll', onScroll );
						open();
					} );

					view.addEventListener( 'scroll', onScroll, {
						passive: true,
					} );
					onScroll();

					return () => view.removeEventListener( 'scroll', onScroll );
				}

				if ( 'exit' === context.trigger ) {
					/*
					 * Exit intent is a pointer leaving through the top of the
					 * window, so it only exists where there is a pointer.
					 * There is no touch equivalent and no attempt at one here:
					 * a popup that guessed at leaving would fire while someone
					 * was reading.
					 */
					const onOut = withScope( ( event ) => {
						if ( event.relatedTarget || event.clientY > 0 ) {
							return;
						}

						doc.removeEventListener( 'mouseout', onOut );
						open();
					} );

					doc.addEventListener( 'mouseout', onOut );

					return () => doc.removeEventListener( 'mouseout', onOut );
				}

				const timer = view.setTimeout(
					withScope( () => open() ),
					context.delay
				);

				return () => view.clearTimeout( timer );
			},
		},
	},
	{ lock: true }
);
