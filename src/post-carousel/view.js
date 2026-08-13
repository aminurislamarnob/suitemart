/**
 * Post carousel interactivity.
 *
 * The Swiper configuration lives in `src/_shared/carousel.js`, shared with the
 * slider. This is the wiring only.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

import { mountCarousel, carouselFor } from '../_shared/carousel';

const ROOT = '.sm-post-carousel';

store(
	'suitemart/post-carousel',
	{
		actions: {
			/**
			 * Moves back a slide.
			 */
			previous() {
				carouselFor( getElement().ref, ROOT )?.slidePrev();
			},

			/**
			 * Moves on a slide.
			 */
			next() {
				carouselFor( getElement().ref, ROOT )?.slideNext();
			},

			/**
			 * Pauses or resumes autoplay.
			 */
			toggleAutoplay() {
				const swiper = carouselFor( getElement().ref, ROOT );

				if ( ! swiper?.autoplay ) {
					return;
				}

				const context = getContext();

				if ( context.isPaused ) {
					swiper.autoplay.start();
				} else {
					swiper.autoplay.stop();
				}

				context.isPaused = ! context.isPaused;
			},
		},

		callbacks: {
			/**
			 * Loads Swiper and enhances the markup.
			 *
			 * @return {Promise<(() => void)|undefined>} Teardown.
			 */
			async mount() {
				const { ref } = getElement();

				if ( ! ref ) {
					return undefined;
				}

				return mountCarousel( {
					root: ref,
					viewport: ref.querySelector(
						'.sm-post-carousel__viewport'
					),
					prev: ref.querySelector( '.sm-post-carousel__arrow--prev' ),
					next: ref.querySelector( '.sm-post-carousel__arrow--next' ),
					dots: ref.querySelector( '.sm-post-carousel__pagination' ),
					options: getContext().options,
				} );
			},
		},
	},
	{ lock: true }
);
