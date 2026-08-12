/**
 * Switching the gallery to the selected variation's image.
 *
 * WooCommerce keeps the chosen variation id in its `woocommerce/products`
 * Interactivity store, which is locked private — the unlock string it ships with
 * states outright that reading it will break on the next release. The add-to-cart
 * form is the stable contract instead: classic and block forms both carry one
 * `attribute_<name>` field per attribute, because that is what the cart consumes.
 * So this reads those fields and matches them against the table render.php seeded.
 */

/**
 * Finds the add-to-cart form governing a gallery.
 *
 * Scoped to the single-product wrapper when there is one, so a gallery placed in
 * a grid cannot pick up a neighbouring card's form.
 *
 * @param {HTMLElement} gallery The gallery root.
 * @return {HTMLFormElement|null} The form, or null when there is none.
 */
const findForm = ( gallery ) => {
	const scope =
		gallery.closest( '.wp-block-woocommerce-single-product' ) ??
		gallery.ownerDocument;

	const forms = scope.querySelectorAll( 'form' );

	for ( const form of forms ) {
		if ( form.querySelector( '[name^="attribute_"]' ) ) {
			return form;
		}
	}

	return null;
};

/**
 * Reads the currently chosen attributes off a form.
 *
 * @param {HTMLFormElement} form The add-to-cart form.
 * @return {Object|null} Attribute name to lower-cased value, or null when the
 *                       selection is incomplete.
 */
const readSelection = ( form ) => {
	const fields = [ ...form.querySelectorAll( '[name^="attribute_"]' ) ];

	if ( fields.length === 0 ) {
		return null;
	}

	// Grouped by name, because a radio set contributes one input per option and
	// only the checked one carries the answer.
	const names = new Set( fields.map( ( field ) => field.name ) );
	const selection = {};

	for ( const name of names ) {
		const group = fields.filter( ( field ) => field.name === name );
		const chosen =
			group.find(
				( field ) =>
					( field.type === 'radio' || field.type === 'checkbox' ) &&
					field.checked
			) ??
			group.find(
				( field ) => field.type !== 'radio' && field.type !== 'checkbox'
			);

		const value = ( chosen?.value ?? '' ).toString().trim();

		// One attribute still unchosen means no variation is selected yet.
		if ( value === '' ) {
			return null;
		}

		selection[ name ] = value.toLowerCase();
	}

	return selection;
};

/**
 * Picks the variation matching a selection.
 *
 * A variation attribute stored as an empty string means "any", which is how
 * WooCommerce represents a variation that covers every value of an attribute.
 *
 * @param {Array}  variations Seeded variation table.
 * @param {Object} selection  Chosen attributes.
 * @return {Object|null} The matching variation, or null.
 */
const matchVariation = ( variations, selection ) => {
	return (
		variations.find( ( variation ) =>
			Object.entries( variation.attributes ).every(
				( [ key, value ] ) => {
					if ( value === '' ) {
						return true;
					}

					return selection[ key ] === value;
				}
			)
		) ?? null
	);
};

/**
 * Watches the add-to-cart form and moves the gallery to the variation's image.
 *
 * @param {HTMLElement} gallery   The gallery root.
 * @param {Object}      context   Block context: `variations` and `slideIds`.
 * @param {Function}    getSwiper Returns the main Swiper, or undefined for the
 *                                grid layout.
 * @return {Function} Teardown.
 */
export const watchVariations = ( gallery, context, getSwiper ) => {
	const form = findForm( gallery );

	if ( ! form ) {
		return () => {};
	}

	const primary = gallery.querySelector(
		'.sm-product-gallery__main-slide img, .sm-product-gallery__grid-item img'
	);

	// Kept so a reset — clearing the selection — puts the original image back.
	const original = primary
		? {
				src: primary.getAttribute( 'src' ) ?? '',
				srcset: primary.getAttribute( 'srcset' ) ?? '',
				sizes: primary.getAttribute( 'sizes' ) ?? '',
				alt: primary.getAttribute( 'alt' ) ?? '',
		  }
		: null;

	const apply = ( image ) => {
		if ( ! primary || ! image ) {
			return;
		}

		primary.setAttribute( 'src', image.src );
		primary.setAttribute( 'alt', image.alt );

		for ( const name of [ 'srcset', 'sizes' ] ) {
			if ( image[ name ] ) {
				primary.setAttribute( name, image[ name ] );
			} else {
				primary.removeAttribute( name );
			}
		}
	};

	let lastImageId = 0;

	const sync = () => {
		const selection = readSelection( form );
		const variation = selection
			? matchVariation( context.variations, selection )
			: null;
		const imageId = variation?.imageId ?? 0;

		if ( imageId === lastImageId ) {
			return;
		}

		lastImageId = imageId;

		if ( ! variation ) {
			// Clearing the selection goes back to the featured image, the way
			// WooCommerce's own gallery does. The guard above means this only
			// runs on a real reset, never on first paint.
			apply( original );
			getSwiper()?.slideTo( 0 );
			gallery.classList.remove( 'is-showing-variation' );
			return;
		}

		gallery.classList.add( 'is-showing-variation' );

		// When the variation's image is already a slide, move to it rather than
		// rewriting one — that keeps the thumbnails and the active slide honest.
		const index = context.slideIds.indexOf( imageId );
		const swiper = getSwiper();

		if ( index > -1 && swiper ) {
			swiper.slideTo( index );
			return;
		}

		if ( index > -1 && ! swiper ) {
			const target = gallery.querySelector(
				`.sm-product-gallery__grid-item[data-sm-image-id="${ imageId }"]`
			);
			target?.scrollIntoView( { block: 'nearest', behavior: 'smooth' } );
			return;
		}

		// Not in the gallery at all, so the leading image is replaced instead.
		apply( variation.image );
		swiper?.slideTo( 0 );
	};

	// The block form's attribute fields are hidden inputs whose value is set
	// programmatically, which fires no change event — so the form subtree is
	// observed as well. `change` still covers the classic form's <select>s.
	const observer = new MutationObserver( sync );

	observer.observe( form, {
		attributes: true,
		childList: true,
		subtree: true,
	} );

	form.addEventListener( 'change', sync );

	sync();

	return () => {
		observer.disconnect();
		form.removeEventListener( 'change', sync );
	};
};
