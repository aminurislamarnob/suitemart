/**
 * Slider interactivity.
 *
 * Swiper is imported dynamically rather than statically so its ~140KB only
 * downloads on pages that actually contain a slider, and only after the page is
 * interactive. Until it arrives — or if it never does — the CSS scroll-snap
 * fallback in the stylesheet keeps the slides usable.
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

// One Swiper instance per slider element.
const instances = new WeakMap();

store(
	'suitemart/slider',
	{
		actions: {
			/**
			 * Moves to the previous slide.
			 */
			previous() {
				const { ref } = getElement();
				instances.get( ref.closest( '.sm-slider' ) )?.slidePrev();
			},

			/**
			 * Moves to the next slide.
			 */
			next() {
				const { ref } = getElement();
				instances.get( ref.closest( '.sm-slider' ) )?.slideNext();
			},

			/**
			 * Pauses or resumes autoplay.
			 */
			toggleAutoplay() {
				const { ref } = getElement();
				const swiper = instances.get( ref.closest( '.sm-slider' ) );

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
			 * @return {(() => void)|undefined} Teardown that destroys the instance.
			 */
			async mount() {
				const { ref } = getElement();

				if ( ! ref || instances.has( ref ) ) {
					return;
				}

				const viewport = ref.querySelector( '.sm-slider__viewport' );

				if ( ! viewport ) {
					return;
				}

				const { options } = getContext();

				const [ { default: Swiper }, modules ] = await Promise.all( [
					import( 'swiper' ),
					import( 'swiper/modules' ),
				] );

				const used = [
					modules.A11y,
					modules.Keyboard,
					modules.Navigation,
					modules.Pagination,
				];

				if ( options.autoplay ) {
					used.push( modules.Autoplay );
				}

				const swiper = new Swiper( viewport, {
					modules: used,
					slidesPerView: options.slidesPerView,
					spaceBetween: options.spaceBetween,
					loop: options.loop,
					// Swiper's own a11y strings would be in English regardless
					// of site language; the buttons are already labelled in
					// render.php, so its generated labels are turned off.
					a11y: {
						enabled: true,
						containerMessage: null,
						containerRoleDescriptionMessage: null,
						itemRoleDescriptionMessage: null,
					},
					keyboard: { enabled: true, onlyInViewport: true },
					navigation: {
						prevEl: ref.querySelector( '.sm-slider__arrow--prev' ),
						nextEl: ref.querySelector( '.sm-slider__arrow--next' ),
					},
					pagination: {
						el: ref.querySelector( '.sm-slider__pagination' ),
						clickable: true,
					},
					autoplay: options.autoplay
						? {
								delay: options.autoplayDelay,
								pauseOnMouseEnter: true,
								disableOnInteraction: false,
						  }
						: false,
					breakpoints: {
						768: { slidesPerView: options.breakpoints[ '768' ] },
						1024: { slidesPerView: options.breakpoints[ '1024' ] },
					},
				} );

				instances.set( ref, swiper );
				ref.classList.add( 'is-enhanced' );

				return () => {
					swiper.destroy( true, true );
					instances.delete( ref );
				};
			},
		},
	},
	{ lock: true }
);
