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
				editor: './src/editor.scss',
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
