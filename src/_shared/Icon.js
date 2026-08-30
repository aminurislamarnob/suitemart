/**
 * Editor-side icon preview.
 *
 * Emits exactly what `suitemart_get_icon()` emits, down to the internal `<use>`
 * reference, so an icon looks the same however it reached the canvas — from a
 * React edit component or out of a server-rendered block.
 *
 * That reference resolves because build/editor.js inlines the sprite into the
 * canvas document. This used to point `<use>` at the sprite *file* instead,
 * which no icon ever rendered from: Chrome and Safari do not support external
 * file references from `<use>` at all.
 */

/**
 * Renders an icon in the editor.
 *
 * @param {Object} props             Component props.
 * @param {string} props.name        Icon name without the `sm-icon-` prefix.
 * @param {number} [props.size]      Pixel size.
 * @param {string} [props.className] Extra class names.
 * @return {JSX.Element|null} The icon, or null when no name is given.
 */
export default function Icon( { name, size = 24, className = '' } ) {
	if ( ! name ) {
		return null;
	}

	// Share marks are solid shapes while the Lucide set is stroked, and the
	// base class sets `fill: none`. Without this they preview as blank squares.
	// Mirrors the same branch in suitemart_get_icon().
	const variant = name.startsWith( 'share-' ) ? ' sm-icon--social' : '';

	return (
		<svg
			className={ `sm-icon sm-icon--${ name }${ variant } ${ className }`.trim() }
			width={ size }
			height={ size }
			aria-hidden="true"
			focusable="false"
		>
			<use href={ `#sm-icon-${ name }` } />
		</svg>
	);
}
