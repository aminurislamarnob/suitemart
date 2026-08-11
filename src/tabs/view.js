/**
 * Tabs interactivity.
 *
 * Implements the WAI-ARIA APG tabs pattern:
 *
 *   - exactly one tab is in the tab order (roving tabindex), so Tab moves from
 *     the tab list into the panel rather than through every tab
 *   - Left/Right (or Up/Down when vertical) move between tabs and wrap
 *   - Home and End jump to the first and last tab
 *   - automatic activation selects on arrow; manual activation waits for
 *     Enter or Space, which is the correct choice when panels are expensive
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

/**
 * Returns the tab buttons belonging to the same tab list.
 *
 * @param {HTMLElement} el An element inside the tabs block.
 * @return {HTMLElement[]} Tab buttons in document order.
 */
const tabsOf = ( el ) => {
	const list = el.closest( '.sm-tabs' )?.querySelector( '.sm-tabs__list' );
	return list ? Array.from( list.querySelectorAll( '.sm-tabs__tab' ) ) : [];
};

const { state } = store(
	'suitemart/tabs',
	{
		state: {
			/**
			 * Whether this tab is the selected one.
			 *
			 * @return {boolean} True when selected.
			 */
			get isSelected() {
				const { index, activeIndex } = getContext();
				return index === activeIndex;
			},

			/**
			 * Roving tabindex value for this tab.
			 *
			 * @return {string} "0" for the selected tab, "-1" for the rest.
			 */
			get tabIndex() {
				return state.isSelected ? '0' : '-1';
			},

			/**
			 * Whether this panel belongs to the selected tab.
			 *
			 * @return {boolean} True when its tab is selected.
			 */
			get isActivePanel() {
				const { index, activeIndex } = getContext();
				return index === activeIndex;
			},
		},

		actions: {
			/**
			 * Selects the clicked tab.
			 */
			selectTab() {
				const context = getContext();
				context.activeIndex = context.index;
			},

			/**
			 * Keyboard navigation within the tab list.
			 *
			 * @param {KeyboardEvent} event Key event.
			 */
			handleListKeydown( event ) {
				const { ref } = getElement();
				const tabs = tabsOf( ref );

				if ( tabs.length === 0 ) {
					return;
				}

				const context = getContext();
				const isVertical =
					ref.getAttribute( 'aria-orientation' ) === 'vertical';
				const previous = isVertical ? 'ArrowUp' : 'ArrowLeft';
				const next = isVertical ? 'ArrowDown' : 'ArrowRight';

				const current = tabs.findIndex(
					( tab ) => tab === ref.ownerDocument.activeElement
				);
				const from = current === -1 ? context.activeIndex : current;

				let target = null;

				if ( event.key === next ) {
					target = ( from + 1 ) % tabs.length;
				} else if ( event.key === previous ) {
					target = ( from - 1 + tabs.length ) % tabs.length;
				} else if ( event.key === 'Home' ) {
					target = 0;
				} else if ( event.key === 'End' ) {
					target = tabs.length - 1;
				}

				if ( target === null ) {
					return;
				}

				event.preventDefault();
				tabs[ target ].focus();

				// With manual activation, moving focus must not change the
				// panel — the user activates with Enter or Space, which fires
				// a click and is handled by selectTab.
				if ( ! context.manual ) {
					context.activeIndex = target;
				}
			},
		},
	},
	{ lock: true }
);
