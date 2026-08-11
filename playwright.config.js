/**
 * Playwright configuration.
 *
 * Targets the wp-env development instance. The suite is deliberately a smoke
 * suite (decision 20): it proves every block inserts, every template loads,
 * checkout completes, and the navigation is keyboard-operable. It is not a
 * visual-regression suite.
 */

const { defineConfig, devices } = require( '@playwright/test' );

const baseURL = process.env.WP_BASE_URL || 'http://localhost:8888';

module.exports = defineConfig( {
	testDir: './tests/e2e',
	outputDir: './test-results',
	timeout: 60_000,
	expect: { timeout: 10_000 },

	// A failing e2e test is almost always a real regression; retry once locally to
	// absorb wp-env warm-up flake, twice in CI.
	retries: process.env.CI ? 2 : 1,
	workers: process.env.CI ? 2 : undefined,
	forbidOnly: !! process.env.CI,

	reporter: process.env.CI
		? [ [ 'github' ], [ 'html', { open: 'never' } ] ]
		: [ [ 'list' ] ],

	use: {
		baseURL,
		trace: 'retain-on-failure',
		screenshot: 'only-on-failure',
		video: 'retain-on-failure',
	},

	projects: [
		{
			name: 'chromium',
			use: { ...devices[ 'Desktop Chrome' ] },
		},
		{
			// The mobile drawer and off-canvas behaviours only exist below the
			// navigation breakpoint, so they need their own viewport.
			name: 'mobile',
			use: { ...devices[ 'Pixel 7' ] },
			testMatch: /.*(nav-a11y|off-canvas)\.spec\.js/,
		},
	],
} );
