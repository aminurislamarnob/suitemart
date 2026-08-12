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
const WISHLIST_SLUG = 'suitemart-wishlist';
const PRODUCT_BLOCKS_SLUG = 'suitemart-product-blocks';
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
	process.env.SUITEMART_COMPARE_URL = ensurePage(
		COMPARE_SLUG,
		'Compare',
		'<!-- wp:suitemart/compare-table {"showSku":true} /-->'
	);
	process.env.SUITEMART_WISHLIST_URL = ensurePage(
		WISHLIST_SLUG,
		'Wishlist',
		'<!-- wp:suitemart/wishlist-grid /-->'
	);

	/*
	 * The gallery and the quick view button both read the product from `postId`
	 * context, and neither belongs on the single-product template: WooCommerce's
	 * own gallery block stays there until ours handles variation images, and a
	 * quick view of the product you are already looking at is pointless. So they
	 * get a page that supplies the context explicitly.
	 */
	process.env.SUITEMART_PRODUCT_BLOCKS_URL = ensurePage(
		PRODUCT_BLOCKS_SLUG,
		'Product blocks',
		`<!-- wp:woocommerce/single-product {"productId":${ process.env.SUITEMART_PRODUCT_ID }} -->
<div class="wp-block-woocommerce-single-product"><!-- wp:suitemart/product-gallery {"layout":"horizontal"} /-->
<!-- wp:suitemart/quick-view-button /--></div>
<!-- /wp:woocommerce/single-product -->`
	);
};

/**
 * Creates or updates a one-block page, and returns its path.
 *
 * The commerce list blocks each need a page of their own, the way a shop would
 * place them: their whole behaviour is what happens when the visitor's stored
 * list changes underneath them, which the shared block fixture page cannot
 * express.
 *
 * @param {string} slug    Page slug.
 * @param {string} title   Page title.
 * @param {string} content Block markup for the page.
 * @return {string} Path to the page, including any query string.
 */
function ensurePage( slug, title, content ) {
	const existing = wp( [
		'post',
		'list',
		'--post_type=page',
		`--name=${ slug }`,
		'--field=ID',
	] );

	const id = /^\d+$/.test( existing )
		? ( wp( [ 'post', 'update', existing, `--post_content=${ content }` ] ),
		  existing )
		: wp( [
				'post',
				'create',
				'--post_type=page',
				`--post_title=${ title }`,
				`--post_name=${ slug }`,
				'--post_status=publish',
				`--post_content=${ content }`,
				'--porcelain',
		  ] );

	if ( ! /^\d+$/.test( id ) ) {
		throw new Error(
			`Could not create the ${ slug } page. WP-CLI returned: ${ id }`
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

	/*
	 * Real attachments, so wp_get_attachment_image() returns markup rather than
	 * an empty string and the gallery has something to page through. The files
	 * are committed and imported from disk: fetching a fixture over the network
	 * makes the suite fail whenever the remote host is unreachable, and a
	 * silently skipped import turns the gallery spec into an assertion about an
	 * empty element.
	 */
	const gallery = [
		'tests/e2e/fixtures/product-image-1.png',
		'tests/e2e/fixtures/product-image-2.png',
	].map( ( file ) => {
		const attachId = wp( [ 'media', 'import', file, '--porcelain' ] );

		if ( ! /^\d+$/.test( attachId ) ) {
			throw new Error(
				`Could not import ${ file }. WP-CLI returned: ${ attachId }`
			);
		}

		return attachId;
	} );

	/*
	 * As WooCommerce itself stores them: the featured image is the first slide
	 * and `_product_image_gallery` holds the rest. Listing the featured image
	 * in the gallery too would give the block the same picture twice, which is
	 * exactly the case a "does the second thumbnail select the second slide"
	 * assertion cannot detect.
	 */
	wp( [ 'post', 'meta', 'update', id, '_thumbnail_id', gallery[ 0 ] ] );
	wp( [
		'post',
		'meta',
		'update',
		id,
		'_product_image_gallery',
		gallery[ 1 ],
	] );

	const parsed = new URL( wp( [ 'post', 'get', id, '--field=url' ] ) );

	// Create a second product to act as a cross-sell.
	const crossExisting = wp( [
		'post',
		'list',
		'--post_type=product',
		`--name=${ PRODUCT_SLUG }-cross`,
		'--field=ID',
	] );

	const crossId = /^\d+$/.test( crossExisting )
		? crossExisting
		: wp( [
				'post',
				'create',
				'--post_type=product',
				'--post_title=Suitemart cross sell',
				`--post_name=${ PRODUCT_SLUG }-cross`,
				'--post_status=publish',
				'--porcelain',
		  ] );

	if ( /^\d+$/.test( crossId ) ) {
		wp( [ 'post', 'meta', 'update', crossId, '_price', '9.99' ] );
		wp( [ 'post', 'meta', 'update', crossId, '_regular_price', '9.99' ] );
		// Link it to the main product. The meta key is _crosssell_ids and it holds an array.
		wp( [
			'post',
			'meta',
			'update',
			id,
			'_crosssell_ids',
			`[${ crossId }]`,
			'--format=json',
		] );
	}

	// The product-context blocks need the id, not just the permalink, so they
	// can be placed on a page of their own inside woocommerce/single-product.
	process.env.SUITEMART_PRODUCT_ID = id;

	return `${ parsed.pathname }${ parsed.search }`;
}
