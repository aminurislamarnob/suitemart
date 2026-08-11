/**
 * Navigation interactivity.
 *
 * Implements the WAI-ARIA APG disclosure-navigation pattern. The accessibility
 * contract this file owns (decision 14 gave us the mega menu, and with it all
 * the keyboard work core would otherwise have done):
 *
 *   - only one panel open at a time
 *   - Escape closes the open panel and returns focus to its trigger
 *   - Tab moves through the panel naturally, and leaving it closes the panel
 *   - ArrowDown from a trigger opens the panel and enters it
 *   - pointer open/close is intent-delayed so a diagonal mouse path does not
 *     slam the panel shut
 *   - the mobile drawer traps focus and locks background scroll
 *   - `prefers-reduced-motion` is honoured by the stylesheet, not here
 */

import { store, getContext, getElement } from '@wordpress/interactivity';
import { focusableWithin, focusFirstWhenReady } from '../_shared/focus';

// Delay before a hover opens or closes a panel. Long enough to survive a
// diagonal mouse path across a neighbouring item, short enough to feel instant.
const HOVER_INTENT_MS = 150;

let hoverTimer = null;

const clearHoverTimer = () => {
	if ( hoverTimer ) {
		clearTimeout( hoverTimer );
		hoverTimer = null;
	}
};

/**
 * Moves focus into a trigger's panel once the panel is actually focusable.
 *
 * The panel is unhidden by the Interactivity API, which schedules its own DOM
 * update, so the element is not focusable the instant the state changes.
 *
 * @param {HTMLElement} trigger The disclosure button.
 * @return {() => void} Teardown that cancels any pending attempt.
 */
const focusPanelOf = ( trigger ) =>
	focusFirstWhenReady( () => {
		const panelId = trigger?.getAttribute( 'aria-controls' );

		return panelId ? trigger.ownerDocument.getElementById( panelId ) : null;
	} );

/**
 * Reads the navigation's overlay state from CSS.
 *
 * The breakpoint is defined once, in the stylesheet, and exposed through
 * `--sm-nav-is-overlay`. Reading it back here keeps JavaScript and CSS from
 * disagreeing about where the drawer starts.
 *
 * @param {HTMLElement} ref The navigation root.
 * @return {boolean} True when the drawer is an overlay.
 */
const isOverlayLayout = ( ref ) =>
	!! ref &&
	getComputedStyle( ref ).getPropertyValue( '--sm-nav-is-overlay' ).trim() ===
		'1';

const { state } = store(
	'suitemart/navigation',
	{
		state: {
			/**
			 * Whether this nav item's panel is the open one.
			 *
			 * @return {boolean} True when open.
			 */
			get isOpen() {
				const context = getContext();
				return !! context.itemId && context.activeId === context.itemId;
			},

			/**
			 * Whether the drawer should be inert.
			 *
			 * Only meaningful below the mobile breakpoint, where the drawer is an
			 * overlay. Above it the drawer is the ordinary nav bar and must never
			 * be inert. The stylesheet owns the breakpoint, so it is read back
			 * from a computed custom property rather than duplicated here.
			 *
			 * @return {boolean} True when the drawer is closed and overlaying.
			 */
			get drawerIsInert() {
				const { ref } = getElement();

				if ( ! ref ) {
					return false;
				}

				return isOverlayLayout( ref ) && ! getContext().isDrawerOpen;
			},
		},

		actions: {
			/**
			 * Toggles a panel from its trigger.
			 */
			toggleItem() {
				const context = getContext();
				clearHoverTimer();
				context.activeId = state.isOpen ? '' : context.itemId;
			},

			/**
			 * Closes every open panel and the drawer.
			 */
			closeAll() {
				const context = getContext();
				clearHoverTimer();
				context.activeId = '';
				context.isDrawerOpen = false;
			},

			/**
			 * Opens a panel after the hover-intent delay.
			 */
			pointerEnter() {
				const context = getContext();

				if ( ! context.hoverIntent ) {
					return;
				}

				clearHoverTimer();
				const { itemId } = context;
				hoverTimer = setTimeout( () => {
					context.activeId = itemId;
				}, HOVER_INTENT_MS );
			},

			/**
			 * Closes a panel after the hover-intent delay.
			 */
			pointerLeave() {
				const context = getContext();

				if ( ! context.hoverIntent ) {
					return;
				}

				clearHoverTimer();
				hoverTimer = setTimeout( () => {
					if ( context.activeId === context.itemId ) {
						context.activeId = '';
					}
				}, HOVER_INTENT_MS );
			},

			/**
			 * Toggles the mobile drawer.
			 */
			toggleDrawer() {
				const context = getContext();
				context.isDrawerOpen = ! context.isDrawerOpen;

				// Closing the drawer must also collapse whatever was open inside it.
				if ( ! context.isDrawerOpen ) {
					context.activeId = '';
				}
			},

			/**
			 * Keyboard handling on a disclosure trigger.
			 *
			 * @param {KeyboardEvent} event Key event.
			 */
			handleTriggerKeydown( event ) {
				const context = getContext();
				const { ref } = getElement();

				if ( event.key === 'ArrowDown' ) {
					event.preventDefault();
					context.activeId = context.itemId;
					focusPanelOf( ref );
				}
			},

			/**
			 * Closes a panel when focus leaves it entirely.
			 *
			 * Without this, tabbing out of a panel leaves it visually open behind
			 * the user's focus position.
			 *
			 * @param {FocusEvent} event Focus event.
			 */
			handleFocusOut( event ) {
				const context = getContext();
				const { ref } = getElement();

				if ( ! ref || context.activeId !== context.itemId ) {
					return;
				}

				const next = event.relatedTarget;

				if ( ! next || ! ref.contains( next ) ) {
					context.activeId = '';
				}
			},

			/**
			 * Document-level key handling: Escape closes and restores focus.
			 *
			 * @param {KeyboardEvent} event Key event.
			 */
			handleDocumentKeydown( event ) {
				if ( event.key !== 'Escape' ) {
					return;
				}

				const context = getContext();
				const { ref } = getElement();

				if ( context.activeId ) {
					const trigger = ref?.ownerDocument.getElementById(
						context.activeId
					);
					context.activeId = '';

					// Returning focus to the control that opened the panel is the
					// part of the pattern that is most often skipped, and the part
					// keyboard users notice immediately when it is missing.
					if ( trigger ) {
						trigger.focus();
					}

					return;
				}

				if ( context.isDrawerOpen ) {
					context.isDrawerOpen = false;
					ref?.querySelector( '.sm-nav__toggle' )?.focus();
				}
			},

			/**
			 * Closes panels when the user clicks outside the navigation.
			 *
			 * @param {MouseEvent} event Click event.
			 */
			handleDocumentClick( event ) {
				const { ref } = getElement();

				if ( ! ref || ref.contains( event.target ) ) {
					return;
				}

				const context = getContext();

				if ( context.activeId || context.isDrawerOpen ) {
					clearHoverTimer();
					context.activeId = '';
					context.isDrawerOpen = false;
				}
			},
		},

		callbacks: {
			/**
			 * Collapses the drawer when the viewport grows past the breakpoint.
			 *
			 * Leaving `isDrawerOpen` true across a resize strands the scroll lock
			 * and leaves the desktop nav in drawer state.
			 */
			handleResize() {
				const { ref } = getElement();
				const context = getContext();

				if ( ! ref || ! context.isDrawerOpen ) {
					return;
				}

				if ( ! isOverlayLayout( ref ) ) {
					context.isDrawerOpen = false;
				}
			},

			/**
			 * Locks background scroll and traps focus while the drawer is open.
			 */
			lockBodyScroll() {
				const { ref } = getElement();

				if ( ! ref ) {
					return;
				}

				const context = getContext();

				ref.ownerDocument.documentElement.classList.toggle(
					'sm-has-drawer-open',
					!! context.isDrawerOpen
				);

				if ( ! context.isDrawerOpen ) {
					return;
				}

				const drawer = ref.querySelector( '.sm-nav__drawer' );
				const trapped = [
					ref.querySelector( '.sm-nav__toggle' ),
					...focusableWithin( drawer ),
				].filter( Boolean );

				if ( trapped.length === 0 ) {
					return;
				}

				const onKeydown = ( event ) => {
					if ( event.key !== 'Tab' ) {
						return;
					}

					const active = ref.ownerDocument.activeElement;
					const first = trapped[ 0 ];
					const last = trapped[ trapped.length - 1 ];

					if ( event.shiftKey && active === first ) {
						event.preventDefault();
						last.focus();
					} else if ( ! event.shiftKey && active === last ) {
						event.preventDefault();
						first.focus();
					}
				};

				ref.addEventListener( 'keydown', onKeydown );

				// Returned teardown runs when the watched state changes or the
				// element unmounts, so the listener never accumulates.
				return () => {
					ref.removeEventListener( 'keydown', onKeydown );
					ref.ownerDocument.documentElement.classList.remove(
						'sm-has-drawer-open'
					);
				};
			},
		},
	},
	{ lock: true }
);
