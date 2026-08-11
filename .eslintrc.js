/**
 * ESLint configuration for Suitemart.
 *
 * Extends the WordPress preset and adds two theme-specific guardrails:
 * jQuery is banned outright (decision 8), and view.js modules may only import
 * from @wordpress/interactivity plus explicitly allowed vendor libraries.
 */

module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	env: {
		browser: true,
		es2022: true,
	},
	globals: {
		wp: 'readonly',
	},
	rules: {
		// Decision 8: no jQuery anywhere in the theme.
		'no-restricted-globals': [
			'error',
			{ name: '$', message: 'jQuery is not used in Suitemart. Use the Interactivity API.' },
			{ name: 'jQuery', message: 'jQuery is not used in Suitemart. Use the Interactivity API.' },
		],
		'@wordpress/no-global-event-listener': 'off',
		'jsdoc/require-param-type': 'off',
	},
	overrides: [
		{
			// Interactivity API store modules run as ES modules on the front end.
			files: [ 'src/**/view.js' ],
			parserOptions: {
				sourceType: 'module',
			},
			rules: {
				'no-restricted-imports': [
					'error',
					{
						patterns: [
							{
								// Everything under @wordpress/ except interactivity:
								// view modules must not pull in editor packages.
								group: [ '@wordpress/*', '!@wordpress/interactivity' ],
								message:
									'Front-end view modules may only import @wordpress/interactivity.',
							},
						],
					},
				],
			},
		},
		{
			files: [ '*.config.js', '.eslintrc.js', 'tools/**/*.mjs', 'tests/e2e/**/*.js' ],
			env: { node: true },
			rules: {
				'@wordpress/no-unused-vars-before-return': 'off',
				// The rule's advice — reach the active element through a node's
				// ownerDocument — is for component code that may run inside an
				// iframe. Specs read it inside page.evaluate(), where there is
				// exactly one document and it is the one under test.
				'@wordpress/no-global-active-element': 'off',
			},
		},
	],
};
