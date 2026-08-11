/**
 * Creates the fixture page the block specs run against.
 *
 * This used to be a manual step written down in tests/README.md, which meant a
 * fresh environment — CI, or anyone cloning the repo — had no such page and
 * every spec that needed it silently tested a 404 until it timed out.
 *
 * The page is addressed by slug rather than id for the same reason: an id is
 * whatever the install happens to assign, and hard-coding one only works on the
 * machine it was written on.
 */

const { execFileSync } = require( 'node:child_process' );
const { join } = require( 'node:path' );

const SLUG = 'suitemart-block-test';
const FIXTURE = 'tests/e2e/fixtures/blocks-page.html';

/**
 * Runs a WP-CLI command inside the wp-env development container.
 *
 * @param {string[]} args WP-CLI arguments.
 * @return {string} Trimmed stdout.
 */
const wp = ( args ) =>
	execFileSync(
		'npx',
		[
			'wp-env',
			'run',
			'cli',
			'--env-cwd=wp-content/themes/suitemart',
			'wp',
			...args,
		],
		{ encoding: 'utf8', cwd: join( __dirname, '..', '..' ) }
	).trim();

module.exports = async () => {
	// `wp post list` prints the id of an existing page, or nothing.
	const existing = wp( [
		'post',
		'list',
		'--post_type=page',
		`--name=${ SLUG }`,
		'--field=ID',
		'--format=ids',
	] );

	if ( existing ) {
		// Update rather than recreate, so the id stays stable across runs and
		// the content always matches the fixture on disk.
		wp( [ 'post', 'update', existing, FIXTURE ] );
		return;
	}

	wp( [
		'post',
		'create',
		FIXTURE,
		'--post_type=page',
		'--post_title=Suitemart block test',
		`--post_name=${ SLUG }`,
		'--post_status=publish',
		'--porcelain',
	] );
};
