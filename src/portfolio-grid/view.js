/**
 * Portfolio grid interactivity.
 *
 * Filtering hides projects that are already in the page rather than fetching a
 * new set, so the grid never empties while a request is in flight and works the
 * same on a slow connection as on a fast one.
 *
 * The three getters below are the other half of the closures in `render.php`.
 * Both halves have to agree: the server decides what the page arrives as, this
 * decides what it becomes, and a difference between them shows up as the grid
 * rearranging itself the moment the module loads.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

store(
	'suitemart/portfolio-grid',
	{
		state: {
			/**
			 * Whether this project is filtered out.
			 *
			 * @return {boolean} True when it should be hidden.
			 */
			get isHidden() {
				const { active, terms } = getContext();

				return (
					Boolean( active ) && ! ( terms ?? [] ).includes( active )
				);
			},

			/**
			 * Whether this filter button is the chosen one.
			 *
			 * @return {boolean} True when it is.
			 */
			get isPressed() {
				const { slug, active } = getContext();

				return ( slug ?? '' ) === ( active ?? '' );
			},

			/**
			 * What the live region announces after a filter is pressed.
			 *
			 * @return {string} The announcement.
			 */
			get status() {
				const context = getContext();

				if ( ! context.active ) {
					return context.showingAll;
				}

				return context.showingOne.replace(
					'%s',
					context.labels?.[ context.active ] ?? context.active
				);
			},
		},

		actions: {
			/**
			 * Applies the filter that was pressed.
			 *
			 * Delegated from the filter bar, which sits outside the buttons'
			 * own contexts: a write from inside one of those would land on the
			 * button rather than on the grid, and nothing would move.
			 *
			 * @param {MouseEvent} event Click.
			 */
			filter( event ) {
				const button = event.target.closest( '[data-slug]' );

				if ( ! button || ! getElement().ref.contains( button ) ) {
					return;
				}

				getContext().active = button.dataset.slug;
			},
		},
	},
	{ lock: true }
);
