/**
 * Creates the fixture page the block specs run against, and proves it serves.
 *
 * This used to be a manual step written down in tests/README.md, which meant a
 * fresh environment — CI, or anyone cloning the repo — had no such page and
 * every spec that needed it silently tested a 404 until it timed out.
 *
 * Creating it is not enough on its own. The first CI run to include this setup
 * still failed every fixture spec with "element(s) not found", because the page
 * existed but the URL the specs guessed did not reach it. So the permalink now
 * comes from WordPress rather than being constructed here, and the page is
 * fetched once and checked before a single test runs. A failure here names the
 * problem in one line instead of forty timeouts.
 */

const { execFileSync } = require( 'node:child_process' );
const { join } = require( 'node:path' );

const SLUG = 'suitemart-block-test';
const PRODUCT_SLUG = 'suitemart-test-product';
const COMPARE_SLUG = 'suitemart-compare';
const FIXTURE = 'tests/e2e/fixtures/blocks-page.html';

// Present on the fixture page and nowhere else, so finding it proves the page
// served its own content rather than a 404 or the front page.
const MARKER = 'sm-counter__number';

/**
 * Runs a WP-CLI command inside the wp-env development container.
 *
 * wp-env writes its own progress lines to stderr, so stdout is just WP-CLI's
 * output — but only the last line is taken, because a PHP notice from a plugin
 * loading early will happily land there too.
 *
 * @param {string[]} args WP-CLI arguments.
 * @return {string} Last line of stdout, trimmed.
 */
const wp = ( args ) => {
	const stdout = execFileSync(
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
	);

	const lines = stdout
		.split( '\n' )
		.map( ( line ) => line.trim() )
		.filter( Boolean );

	return lines[ lines.length - 1 ] ?? '';
};

module.exports = async ( config ) => {
	// wp-env installs the themes it is given but does not reliably activate one,
	// so locally this passed on a theme activated by hand months ago while CI
	// tested a default theme: the fixture page served a healthy 200 with none of
	// Suitemart's blocks in it. Activating here makes the run self-contained.
	wp( [ 'theme', 'activate', 'suitemart' ] );

	const existing = wp( [
		'post',
		'list',
		'--post_type=page',
		`--name=${ SLUG }`,
		'--field=ID',
	] );

	// Update rather than recreate, so the id stays stable across runs and the
	// content always matches the fixture on disk.
	const id = /^\d+$/.test( existing )
		? ( wp( [ 'post', 'update', existing, FIXTURE ] ), existing )
		: wp( [
				'post',
				'create',
				FIXTURE,
				'--post_type=page',
				'--post_title=Suitemart block test',
				`--post_name=${ SLUG }`,
				'--post_status=publish',
				'--porcelain',
		  ] );

	if ( ! /^\d+$/.test( id ) ) {
		throw new Error(
			`Could not create the fixture page. WP-CLI returned: ${ id }`
		);
	}

	// Ask WordPress for the URL rather than assembling one: it depends on the
	// permalink structure, which differs between installs.
	const url = wp( [ 'post', 'get', id, '--field=url' ] );

	const baseURL =
		config?.projects?.[ 0 ]?.use?.baseURL ||
		process.env.WP_BASE_URL ||
		'http://localhost:8888';

	// The container reports its own hostname, which is not reachable from here,
	// so only the path is kept. The query string is part of that path: under
	// the default plain permalinks WordPress returns `/?page_id=12`, and
	// dropping the query leaves the site's front page — which serves a perfectly
	// healthy 200 containing none of the fixture's blocks.
	const parsed = new URL( url );
	const path = `${ parsed.pathname }${ parsed.search }`;
	const reachable = new URL( path, baseURL ).href;

	const response = await fetch( reachable );
	const html = await response.text();

	if ( ! response.ok || ! html.includes( MARKER ) ) {
		// The bare "did not serve its blocks" message cost a CI round-trip to
		// interpret, so the two things that explain almost every occurrence —
		// the wrong theme, or an unbuilt block — are reported with it.
		const theme = wp( [
			'theme',
			'list',
			'--status=active',
			'--field=name',
		] );
		const registered = wp( [
			'eval',
			'echo WP_Block_Type_Registry::get_instance()->is_registered( "suitemart/counter" ) ? "yes" : "no";',
		] );

		throw new Error(
			`The fixture page at ${ reachable } did not serve its blocks ` +
				`(HTTP ${ response.status }, ${ html.length } bytes). ` +
				`Active theme: ${ theme }. suitemart/counter registered: ${ registered }. ` +
				`Every fixture spec would fail with "element(s) not found".`
		);
	}

	// Handed to the specs so they never guess at the URL form.
	process.env.SUITEMART_FIXTURE_URL = path;

	process.env.SUITEMART_PRODUCT_URL = ensureProduct();
	process.env.SUITEMART_COMPARE_URL = ensureComparePage();
};

/**
 * Creates the page holding the comparison table, and returns its path.
 *
 * The table has no home in the block fixture page: it belongs on a page of its
 * own, the way a shop would place it, and its whole behaviour is what happens
 * when the visitor's stored list changes underneath it.
 *
 * @return {string} Path to the page, including any query string.
 */
function ensureComparePage() {
	const content = '<!-- wp:suitemart/compare-table {"showSku":true} /-->';

	const existing = wp( [
		'post',
		'list',
		'--post_type=page',
		`--name=${ COMPARE_SLUG }`,
		'--field=ID',
	] );

	const id = /^\d+$/.test( existing )
		? ( wp( [ 'post', 'update', existing, `--post_content=${ content }` ] ),
		  existing )
		: wp( [
				'post',
				'create',
				'--post_type=page',
				'--post_title=Compare',
				`--post_name=${ COMPARE_SLUG }`,
				'--post_status=publish',
				`--post_content=${ content }`,
				'--porcelain',
		  ] );

	if ( ! /^\d+$/.test( id ) ) {
		throw new Error(
			`Could not create the compare page. WP-CLI returned: ${ id }`
		);
	}

	const parsed = new URL( wp( [ 'post', 'get', id, '--field=url' ] ) );

	return `${ parsed.pathname }${ parsed.search }`;
}

/**
 * Creates a published product for the commerce specs, and returns its path.
 *
 * WooCommerce is installed in the test environment but ships no products, so
 * without this the wishlist specs would have nothing to act on. A bare post of
 * type `product` with a price is enough: these tests are about the theme's
 * blocks, not about WooCommerce's own behaviour.
 *
 * @return {string} Path to the product, including any query string.
 */
function ensureProduct() {
	const existing = wp( [
		'post',
		'list',
		'--post_type=product',
		`--name=${ PRODUCT_SLUG }`,
		'--field=ID',
	] );

	const id = /^\d+$/.test( existing )
		? existing
		: wp( [
				'post',
				'create',
				'--post_type=product',
				'--post_title=Suitemart test product',
				`--post_name=${ PRODUCT_SLUG }`,
				'--post_status=publish',
				'--porcelain',
		  ] );

	if ( ! /^\d+$/.test( id ) ) {
		throw new Error(
			`Could not create the test product. WP-CLI returned: ${ id }`
		);
	}

	// Without a price WooCommerce treats the product as incomplete and some
	// blocks decline to render.
	wp( [ 'post', 'meta', 'update', id, '_price', '19.99' ] );
	wp( [ 'post', 'meta', 'update', id, '_regular_price', '19.99' ] );

	const parsed = new URL( wp( [ 'post', 'get', id, '--field=url' ] ) );

	return `${ parsed.pathname }${ parsed.search }`;
}
