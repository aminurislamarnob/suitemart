/**
 * Cookie notice interactivity.
 *
 * The block records a choice and announces it. Acting on it — not loading
 * analytics, not setting a marketing cookie — belongs to whatever code sets
 * those things, which is never the theme. Two ways to listen, because
 * integrations differ:
 *
 *   document.addEventListener( 'suitemart-cookie-consent', ( event ) => {
 *       event.detail.choice; // 'accepted' | 'declined'
 *   } );
 *
 * and `<html data-sm-consent="accepted">`, for anything that would rather read
 * a value than wait for an event it may have missed.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

const KEY = 'suitemart:cookie-consent';
const CHOICES = [ 'accepted', 'declined' ];

/**
 * Reads the stored choice.
 *
 * @param {Window} view Window to read from.
 * @return {string|null} 'accepted', 'declined', or null when nothing is stored
 *                       or storage is unavailable.
 */
const readChoice = ( view ) => {
	try {
		const stored = view.localStorage.getItem( KEY );

		return CHOICES.includes( stored ) ? stored : null;
	} catch {
		// Site data blocked. Treated as "not answered", which shows the notice
		// again on the next page — the honest outcome, since the answer really
		// was not kept.
		return null;
	}
};

/**
 * Records a choice and tells the rest of the page about it.
 *
 * @param {HTMLElement} ref    An element inside the notice.
 * @param {string}      choice 'accepted' or 'declined'.
 */
const publish = ( ref, choice ) => {
	const doc = ref.ownerDocument;
	const view = doc.defaultView;

	try {
		view.localStorage.setItem( KEY, choice );
	} catch {
		// Nothing to do but carry on: the notice still closes for this page
		// view, and returns next time because the answer was not kept. Pretending
		// otherwise would be worse.
	}

	doc.documentElement.dataset.smConsent = choice;

	doc.dispatchEvent(
		new view.CustomEvent( 'suitemart-cookie-consent', {
			detail: { choice },
		} )
	);
};

store(
	'suitemart/cookie-notice',
	{
		actions: {
			/**
			 * Records acceptance.
			 */
			accept() {
				const { ref } = getElement();

				publish( ref, 'accepted' );
				getContext().isOpen = false;
			},

			/**
			 * Records refusal.
			 */
			decline() {
				const { ref } = getElement();

				publish( ref, 'declined' );
				getContext().isOpen = false;
			},
		},

		callbacks: {
			/**
			 * Shows the notice only to visitors who have not answered.
			 */
			decideVisibility() {
				const { ref } = getElement();
				const doc = ref.ownerDocument;
				const choice = readChoice( doc.defaultView );

				if ( choice ) {
					// Republished on every page load, because anything
					// listening for the event was not around when the choice
					// was first made.
					doc.documentElement.dataset.smConsent = choice;
					return;
				}

				getContext().isOpen = true;
			},
		},
	},
	{ lock: true }
);
