/**
 * Editor-side icon preview.
 *
 * The front end renders icons from the inlined sprite, but the sprite is not
 * present in the editor canvas, so `<use href="#id">` would resolve to nothing.
 * This references the sprite file directly instead — acceptable in the editor,
 * where the asset is same-origin.
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

	const spriteUrl = `${
		window.suitemartThemeUri || ''
	}/assets/icons/sprite.svg`;

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
			<use href={ `${ spriteUrl }#sm-icon-${ name }` } />
		</svg>
	);
}
