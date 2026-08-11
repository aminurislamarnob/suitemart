/**
 * Icon names available in assets/icons/sprite.svg.
 *
 * Kept as a plain list rather than parsed from the sprite so the editor bundle
 * does not have to ship or fetch the SVG. `Test_Icons::test_sprite_matches_list`
 * asserts the two stay in sync, so adding a symbol without listing it here (or
 * the reverse) fails the build rather than silently producing an empty icon.
 */

export const ICON_NAMES = [
	'arrow-left',
	'arrow-right',
	'arrow-up',
	'check',
	'chevron-down',
	'chevron-left',
	'chevron-right',
	'chevron-up',
	'clock',
	'eye',
	'filter',
	'grid',
	'heart',
	'list',
	'mail',
	'map-pin',
	'menu',
	'minus',
	'package',
	'phone',
	'plus',
	'refresh',
	'search',
	'shield',
	'shopping-bag',
	'shopping-cart',
	'shuffle',
	'star',
	'trash',
	'truck',
	'user',
	'x',
];

/**
 * Icon options shaped for SelectControl.
 *
 * @return {Array<{label: string, value: string}>} Options including an empty choice.
 */
export const iconOptions = () => [
	{ label: '—', value: '' },
	...ICON_NAMES.map( ( name ) => ( { label: name, value: name } ) ),
];
