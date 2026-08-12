import { store, getElement, getContext } from '@wordpress/interactivity';
import { watchVariations } from './variations';

const instances = new WeakMap();

store( 'suitemart/product-gallery', {
	callbacks: {
		/**
		 * Sets up the carousels, and follows the selected variation.
		 *
		 * Both jobs share one callback because a second `data-wp-init` on the
		 * same element needs the unique-suffix syntax added in WordPress 6.9,
		 * and the theme supports 6.7 (decision 19).
		 */
		init: async () => {
			const { ref } = getElement();
			if ( ! ref || instances.has( ref ) ) {
				return;
			}

			const context = getContext();
			const state = {};

			instances.set( ref, state );

			// The grid layout has no Swiper, so variation switching scrolls to
			// the image instead of sliding to it.
			const getSwiper = () => state.mainSwiper;

			if ( context?.variations?.length ) {
				state.releaseVariations = watchVariations(
					ref,
					context,
					getSwiper
				);
			}

			const mainEl = ref.querySelector( '.sm-product-gallery__main' );
			const thumbsEl = ref.querySelector( '.sm-product-gallery__thumbs' );

			if ( mainEl && thumbsEl ) {
				try {
					const [ { default: Swiper }, modules ] = await Promise.all(
						[ import( 'swiper' ), import( 'swiper/modules' ) ]
					);

					const thumbsSwiper = new Swiper( thumbsEl, {
						modules: [ modules.FreeMode ],
						spaceBetween: 10,
						slidesPerView: 4,
						freeMode: true,
						watchSlidesProgress: true,
						direction: ref.classList.contains(
							'sm-product-gallery--vertical'
						)
							? 'vertical'
							: 'horizontal',
					} );

					const mainSwiper = new Swiper( mainEl, {
						modules: [ modules.Navigation, modules.Thumbs ],
						spaceBetween: 10,
						navigation: {
							nextEl: ref.querySelector(
								'.sm-product-gallery__button-next'
							),
							prevEl: ref.querySelector(
								'.sm-product-gallery__button-prev'
							),
						},
						thumbs: {
							swiper: thumbsSwiper,
						},
					} );

					state.mainSwiper = mainSwiper;
					state.thumbsSwiper = thumbsSwiper;
				} catch ( error ) {
					// Swiper could not be loaded. The slides are still in the
					// document and still readable; only the carousel is lost.
				}
			}

			return () => {
				const current = instances.get( ref );

				if ( ! current ) {
					return;
				}

				current.releaseVariations?.();
				current.mainSwiper?.destroy( true, true );
				current.thumbsSwiper?.destroy( true, true );
				instances.delete( ref );
			};
		},
	},
} );
