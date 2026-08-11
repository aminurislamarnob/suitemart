/**
 * Builds assets/icons/sprite.svg from lucide-static.
 *
 * The sprite used to be maintained by hand, which meant adding an icon involved
 * transcribing path data — the one job a human is reliably worse at than a
 * script. The icon list in src/_shared/icons.js is now the single source of
 * truth: add a name there, run `npm run build:icons`, and the symbol appears.
 *
 * lucide-static is a devDependency. Nothing at runtime reads it — the generated
 * sprite is committed, so a checkout without node_modules still renders icons.
 *
 * Usage: node tools/build-sprite.mjs [--check]
 *   --check exits non-zero if the committed sprite is stale, for CI.
 */

import { readFile, writeFile } from 'node:fs/promises';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const ROOT = join( dirname( fileURLToPath( import.meta.url ) ), '..' );
const LIST = join( ROOT, 'src/_shared/icons.js' );
const OUT = join( ROOT, 'assets/icons/sprite.svg' );
const ICONS = join( ROOT, 'node_modules/lucide-static/icons' );

// Hand-authored marks that Lucide does not carry, copied verbatim into the
// generated sprite. Lucide removed brand marks upstream, so the share icons
// cannot come from it — keeping them in their own file is what stops a rebuild
// from deleting them.
const SOCIAL = join( ROOT, 'assets/icons/social.svg' );

/**
 * Reads the icon names out of src/_shared/icons.js.
 *
 * The list is extracted textually rather than imported: package.json has no
 * `"type": "module"` (wp-scripts needs its configs to stay CommonJS), so a .mjs
 * script cannot `import` that .js file without Node treating it as CommonJS.
 *
 * @return {Promise<string[]>} Icon names, in the order they are declared.
 */
async function readIconNames() {
	const source = await readFile( LIST, 'utf8' );
	const block = source.match( /ICON_NAMES\s*=\s*\[([\s\S]*?)\]/ );

	if ( ! block ) {
		throw new Error( `Could not find ICON_NAMES in ${ LIST }` );
	}

	return [ ...block[ 1 ].matchAll( /'([^']+)'/g ) ].map( ( m ) => m[ 1 ] );
}

/**
 * Extracts the drawing commands from a lucide-static SVG.
 *
 * Presentation attributes (stroke, width, fill) are dropped: the `.sm-icon`
 * class owns those, so one symbol can serve every size and colour.
 *
 * @param {string} name Icon file name without extension.
 * @return {Promise<string>} Inner markup of the SVG, whitespace collapsed.
 */
async function extract( name ) {
	let svg;

	try {
		svg = await readFile( join( ICONS, `${ name }.svg` ), 'utf8' );
	} catch {
		throw new Error(
			`Lucide has no icon named "${ name }". Check https://lucide.dev/icons — ` +
				`brand marks in particular were removed upstream.`
		);
	}

	const inner = svg.match( /<svg[^>]*>([\s\S]*)<\/svg>/ );

	return inner[ 1 ]
		.replace( /\s*\n\s*/g, '' )
		.replace( /\s*\/>/g, '/>' )
		.trim();
}

const HEADER = `<svg xmlns="http://www.w3.org/2000/svg">
<!--
	Suitemart icon sprite — GENERATED FILE, DO NOT EDIT BY HAND.

	Run \`npm run build:icons\` after changing ICON_NAMES in src/_shared/icons.js.

	Derived from Lucide (https://lucide.dev) — ISC License, Copyright (c) Lucide
	Icons and Contributors; portions from Feather (MIT), Copyright (c) Cole
	Bemis. All icons are 24x24 on a stroke grid; presentation (stroke width,
	colour, size) comes from the \`.sm-icon\` class in global.scss so a single
	symbol serves every context.

	Symbols are referenced as <use href="#sm-icon-NAME"> by suitemart_get_icon().
-->
`;

/**
 * Reads the hand-authored social symbols verbatim.
 *
 * @return {Promise<{markup: string, names: string[]}>} Symbol markup and ids.
 */
async function readSocialSymbols() {
	const source = await readFile( SOCIAL, 'utf8' );
	const symbols = source.match( /<symbol[\s\S]*?<\/symbol>/g ) ?? [];

	return {
		markup: symbols.join( '\n' ),
		names: [ ...source.matchAll( /id="sm-icon-([^"]+)"/g ) ].map(
			( m ) => m[ 1 ]
		),
	};
}

const names = await readIconNames();
const social = await readSocialSymbols();
const symbols = await Promise.all(
	names.map(
		async ( name ) =>
			`<symbol id="sm-icon-${ name }" viewBox="0 0 24 24">${ await extract(
				name
			) }</symbol>`
	)
);

const sprite = `${ HEADER }\n${ symbols.join(
	'\n'
) }\n\n<!-- Hand-authored share marks, copied from assets/icons/social.svg. -->\n${
	social.markup
}\n\n</svg>\n`;

if ( process.argv.includes( '--check' ) ) {
	const current = await readFile( OUT, 'utf8' );

	if ( current !== sprite ) {
		process.stderr.write(
			'assets/icons/sprite.svg is out of date. Run `npm run build:icons`.\n'
		);
		process.exit( 1 );
	}

	process.stdout.write(
		`Sprite is up to date (${ names.length } Lucide + ${ social.names.length } social).\n`
	);
} else {
	await writeFile( OUT, sprite );
	process.stdout.write(
		`Wrote ${ names.length } Lucide + ${ social.names.length } social icons to ${ OUT }\n`
	);
}
