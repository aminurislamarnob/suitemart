/**
 * Lightbox interactivity.
 *
 * PhotoSwipe is imported dynamically, and its viewer core is imported again
 * only when a lightbox is actually opened. So a page carrying a gallery
 * downloads roughly 5KB up front, and the remaining ~40KB only if someone
 * enlarges something. Loading it eagerly would put the whole cost on every
 * visitor who scrolled past a photograph.
 */

import {
	store,
	getContext,
	getElement,
	getConfig,
} from '@wordpress/interactivity';

// One PhotoSwipe instance per lightbox element, so a page can hold several.
const instances = new WeakMap();

/**
 * Finds the caption for a gallery item.
 *
 * The figure's own caption first, because that is what the editor wrote and
 * what is already visible under the thumbnail; the alt text second, which
 * describes the image rather than commenting on it but is better than nothing.
 *
 * @param {HTMLElement|undefined} anchor The item's link.
 * @return {string} Caption text, possibly empty.
 */
const captionFor = ( anchor ) => {
	if ( ! anchor ) {
		return '';
	}

	const caption = anchor.closest( 'figure' )?.querySelector( 'figcaption' );

	if ( caption?.textContent.trim() ) {
		return caption.textContent.trim();
	}

	return anchor.querySelector( 'img' )?.getAttribute( 'alt' )?.trim() || '';
};

store(
	'suitemart/lightbox',
	{
		callbacks: {
			/**
			 * Attaches PhotoSwipe to this lightbox.
			 *
			 * @return {Promise<(() => void)|undefined>} Teardown that destroys it.
			 */
			async mount() {
				const { ref } = getElement();

				if ( ! ref || instances.has( ref ) ) {
					return;
				}

				const context = getContext();
				const config = getConfig();

				const { default: PhotoSwipeLightbox } = await import(
					'photoswipe/lightbox'
				);

				const lightbox = new PhotoSwipeLightbox( {
					gallery: ref,
					children: 'a.sm-lightbox__item',
					loop: context.loop,
					pswpModule: () => import( 'photoswipe' ),
					// PhotoSwipe labels its own controls in English otherwise,
					// on a site that may not be in English at all.
					closeTitle: config.closeTitle,
					zoomTitle: config.zoomTitle,
					arrowPrevTitle: config.arrowPrevTitle,
					arrowNextTitle: config.arrowNextTitle,
					errorMsg: config.errorMsg,
				} );

				// PhotoSwipe gives its root `role="dialog"` and nothing else,
				// which leaves the viewer unnamed to a screen reader and,
				// despite trapping focus, not announced as modal.
				lightbox.on( 'firstUpdate', () => {
					lightbox.pswp.element?.setAttribute(
						'aria-label',
						config.dialogLabel
					);
					lightbox.pswp.element?.setAttribute( 'aria-modal', 'true' );
				} );

				if ( context.showCaptions ) {
					// Read off the anchors by index rather than through
					// `pswp.currSlide.data.element`: slides are created lazily,
					// so on the first `change` into a slide that has not been
					// built yet there is no element to read and the caption
					// silently empties.
					const anchors = [
						...ref.querySelectorAll( 'a.sm-lightbox__item' ),
					];

					lightbox.on( 'uiRegister', () => {
						lightbox.pswp.ui.registerElement( {
							name: 'sm-caption',
							order: 9,
							isButton: false,
							appendTo: 'root',
							html: '',
							onInit: ( el, pswp ) => {
								el.className = 'sm-lightbox__caption';

								const update = () => {
									el.textContent = captionFor(
										anchors[ pswp.currIndex ]
									);
								};

								pswp.on( 'change', update );
								update();
							},
						} );
					} );
				}

				lightbox.init();
				instances.set( ref, lightbox );
				ref.classList.add( 'is-enhanced' );

				return () => {
					lightbox.destroy();
					instances.delete( ref );
				};
			},
		},
	},
	{ lock: true }
);
