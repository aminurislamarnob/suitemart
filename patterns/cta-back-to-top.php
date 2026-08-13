<?php
/**
 * Title: Back to top control
 * Slug: suitemart/cta-back-to-top
 * Categories: suitemart/cta
 * Block Types: core/template-part/footer
 * Description: A scroll-to-top button that appears once the page has been scrolled.
 * Keywords: scroll, top, back, floating, footer
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/*
 * This belongs in the footer part rather than on a page: it pins itself to the
 * viewport, so one per page is the whole budget, and a footer part is the only
 * place that is true of by construction.
 *
 * The other two pinned blocks — the cookie notice and a bottom-corner floating
 * block — move out of its way through `:has()` rules in their own stylesheets.
 * Anything else pinned to a screen edge has to be checked against them.
 */
?>
<!-- wp:suitemart/back-to-top {"threshold":400,"position":"end","appearance":"icon"} /-->
