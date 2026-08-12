import { store, getElement } from '@wordpress/interactivity';

const instances = new WeakMap();

store( 'suitemart/product-gallery', {
	callbacks: {
		/**
		 * Loads Swiper and initializes the main and thumb galleries.
		 */
		init: async () => {
			const { ref } = getElement();
			if ( ! ref || instances.has( ref ) ) {
				return;
			}

			const mainEl = ref.querySelector( '.sm-product-gallery__main' );
			const thumbsEl = ref.querySelector( '.sm-product-gallery__thumbs' );

			if ( ! mainEl || ! thumbsEl ) {
				return;
			}

			try {
				const [ { default: Swiper }, modules ] = await Promise.all( [
					import( 'swiper' ),
					import( 'swiper/modules' ),
				] );

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

				instances.set( ref, { mainSwiper, thumbsSwiper } );

				return () => {
					if ( instances.has( ref ) ) {
						const { mainSwiper: m, thumbsSwiper: t } =
							instances.get( ref );
						m.destroy( true, true );
						t.destroy( true, true );
						instances.delete( ref );
					}
				};
			} catch ( error ) {
				// Failed to load Swiper
			}
		},
	},
} );
