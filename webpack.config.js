/**
 * Webpack configuration.
 *
 * Extends the @wordpress/scripts default (which discovers every src/<block>/
 * block.json and builds its scripts and styles) with two theme-level entries
 * that wp-scripts would not find on its own: the small global stylesheet and
 * the editor stylesheet.
 */

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

// wp-scripts exports an array when module builds are enabled
// (--experimental-modules): [ scriptConfig, moduleConfig ]. Only the script
// config should carry the theme's stylesheet entries.
const configs = Array.isArray( defaultConfig ) ? defaultConfig : [ defaultConfig ];

const [ scriptConfig, ...rest ] = configs;

module.exports = [
	{
		...scriptConfig,
		entry: () => {
			// The default `entry` is a function that scans for block.json files.
			const blockEntries =
				typeof scriptConfig.entry === 'function'
					? scriptConfig.entry()
					: scriptConfig.entry;

			return {
				...blockEntries,
				global: './src/global.scss',
				// Imports editor.scss, so this one entry still emits
				// build/editor.css for add_editor_style().
				editor: './src/editor.js',
				/*
				 * PhotoSwipe's stylesheet, emitted beside the lightbox block and
				 * listed as a second `style` in its block.json, so it loads only
				 * on pages that use the block. Importing it from the block's own
				 * style.scss would be the obvious route, but postcss-import only
				 * looks in the importing file's directory and does not resolve
				 * package names — the alternative is a relative path picking its
				 * way out to node_modules, which breaks the moment the install
				 * layout changes.
				 */
				'lightbox/photoswipe': 'photoswipe/photoswipe.css',
			};
		},
		plugins: [
			...scriptConfig.plugins,
			// Sass-only entries would otherwise emit a stub global.js/editor.js.
			new RemoveEmptyScriptsPlugin( {
				stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
			} ),
		],
	},
	...rest,
];
