/**
 * Icon names available in assets/icons/sprite.svg.
 *
 * This list is the source of truth: `npm run build:icons` generates the sprite
 * from it by pulling the matching path data out of lucide-static. Adding a name
 * here and rebuilding is the whole workflow — never hand-edit the sprite.
 *
 * It is kept as a plain list rather than parsed from the sprite at runtime so
 * the editor bundle does not have to ship or fetch the SVG.
 * `Test_Icons::test_sprite_matches_list` asserts the two stay in sync, so a
 * forgotten rebuild fails the suite rather than rendering an empty icon.
 *
 * Names must exist in Lucide. Brand marks (Facebook, X, LinkedIn…) were removed
 * upstream and are deliberately absent — see src/social-share for how sharing
 * is labelled without them.
 */

export const ICON_NAMES = [
	'arrow-left',
	'arrow-right',
	'arrow-up',
	'award',
	'calendar',
	'check',
	'chevron-down',
	'chevron-left',
	'chevron-right',
	'chevron-up',
	'circle',
	'clock',
	'eye',
	'filter',
	'globe',
	'grid',
	'heart',
	'image',
	'link',
	'list',
	'mail',
	'map-pin',
	'maximize',
	'menu',
	'minus',
	'move-horizontal',
	'package',
	'pause',
	'phone',
	'play',
	'plus',
	'quote',
	'refresh-cw',
	'rotate-3d',
	'search',
	'share-2',
	'shield',
	'shopping-bag',
	'shopping-cart',
	'shuffle',
	'star',
	'trash',
	'trending-up',
	'truck',
	'user',
	'users',
	'x',
	'zoom-in',
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
