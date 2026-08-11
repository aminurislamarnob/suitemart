/**
 * Counter interactivity.
 *
 * Animates from the start value to the end value the first time the block
 * scrolls into view. Everything about this is opt-in enhancement: the server
 * already printed the final number, so if this module never runs, or the
 * reader prefers reduced motion, the correct figure is simply there.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

/**
 * Formats a value using the document's language, matching the server's
 * `number_format_i18n()` grouping.
 *
 * @param {number} value Value to format.
 * @return {string} Localised number.
 */
const localise = ( value ) => {
	const lang =
		typeof document !== 'undefined'
			? document.documentElement.lang || undefined
			: undefined;

	return new Intl.NumberFormat( lang ).format( Math.round( value ) );
};

// Ease-out cubic: fast at first, settling on the final figure, which reads as
// a total being counted rather than a dial being turned.
const easeOut = ( t ) => 1 - Math.pow( 1 - t, 3 );

store(
	'suitemart/counter',
	{
		callbacks: {
			/**
			 * Starts the count the first time the block is visible.
			 *
			 * @return {() => void} Teardown that disconnects the observer.
			 */
			observe() {
				const { ref } = getElement();
				const view = ref.ownerDocument.defaultView;

				const reducedMotion = view.matchMedia(
					'(prefers-reduced-motion: reduce)'
				).matches;

				if ( reducedMotion || ! view.IntersectionObserver ) {
					return () => {};
				}

				const context = getContext();
				let frame = 0;

				const run = () => {
					const startedAt = view.performance.now();
					const distance = context.end - context.start;

					const step = ( now ) => {
						const elapsed = now - startedAt;
						const progress = Math.min(
							1,
							elapsed / context.duration
						);

						context.display = localise(
							context.start + distance * easeOut( progress )
						);

						if ( progress < 1 ) {
							frame = view.requestAnimationFrame( step );
							return;
						}

						// Land exactly on the authored value rather than
						// whatever the easing rounds to.
						context.display = localise( context.end );
					};

					frame = view.requestAnimationFrame( step );
				};

				const observer = new view.IntersectionObserver(
					( entries ) => {
						if ( ! entries.some( ( e ) => e.isIntersecting ) ) {
							return;
						}

						// Counting once is the point; a number that re-runs on
						// every scroll past is a distraction.
						observer.disconnect();
						context.display = localise( context.start );
						run();
					},
					{ threshold: 0.4 }
				);

				observer.observe( ref );

				return () => {
					observer.disconnect();
					view.cancelAnimationFrame( frame );
				};
			},
		},
	},
	{ lock: true }
);
