/**
 * Shared Swiper mounting.
 *
 * Two blocks are carousels — `suitemart/slider`, which holds arbitrary slides,
 * and `suitemart/post-carousel`, which builds its own from a query — and they
 * want exactly the same Swiper: the same modules, the same a11y settings, the
 * same breakpoints. This is that configuration, in one place, so the two cannot
 * drift into behaving differently.
 *
 * Swiper is imported dynamically so its ~140KB only downloads on pages that
 * contain a carousel, and only once the page is interactive. Until it arrives —
 * or if it never does — the CSS scroll-snap fallback in each block's stylesheet
 * keeps the slides scrollable and every one of them reachable.
 */

// One Swiper instance per carousel root.
const instances = new WeakMap();

/**
 * The Swiper instance belonging to the carousel an element sits in.
 *
 * @param {HTMLElement} element Any element inside the carousel.
 * @param {string}      root    Selector for the carousel root.
 * @return {Object|undefined} The instance, or undefined before it mounts.
 */
export const carouselFor = ( element, root ) =>
	instances.get( element.closest( root ) );

/**
 * Loads Swiper and enhances a carousel's markup.
 *
 * @param {Object}      config          Mount configuration.
 * @param {HTMLElement} config.root     Carousel root; gets `is-enhanced`.
 * @param {HTMLElement} config.viewport Element Swiper takes over.
 * @param {HTMLElement} [config.prev]   Previous-slide button.
 * @param {HTMLElement} [config.next]   Next-slide button.
 * @param {HTMLElement} [config.dots]   Pagination container.
 * @param {Object}      config.options  Server-supplied options.
 * @return {Promise<(() => void)|undefined>} Teardown, once mounted.
 */
export const mountCarousel = async ( {
	root,
	viewport,
	prev,
	next,
	dots,
	options,
} ) => {
	if ( ! root || ! viewport || instances.has( root ) ) {
		return undefined;
	}

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
		/*
		 * Swiper's own a11y strings would be in English whatever the site
		 * language is. The buttons and the carousel are already labelled in
		 * PHP, through the text domain, so its generated ones are turned off.
		 */
		a11y: {
			enabled: true,
			containerMessage: null,
			containerRoleDescriptionMessage: null,
			itemRoleDescriptionMessage: null,
		},
		keyboard: { enabled: true, onlyInViewport: true },
		navigation: { prevEl: prev, nextEl: next },
		pagination: dots ? { el: dots, clickable: true } : false,
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

	instances.set( root, swiper );
	root.classList.add( 'is-enhanced' );

	return () => {
		swiper.destroy( true, true );
		instances.delete( root );
	};
};
