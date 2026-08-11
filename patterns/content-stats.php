<?php
/**
 * Title: Statistics row
 * Slug: suitemart/content-stats
 * Categories: suitemart/content, featured
 * Description: Four counters that tally up as the section scrolls into view.
 * Keywords: stats, numbers, counters, milestones, achievements
 * Viewport Width: 1400
 *
 * @package Suitemart
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained","wideSize":"1280px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/counter {"end":24000,"suffix":"+","icon":"users","label":"<?php echo esc_attr_x( 'Orders delivered', 'Pattern label', 'suitemart' ); ?>"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/counter {"end":98,"suffix":"%","icon":"star","label":"<?php echo esc_attr_x( 'Would order again', 'Pattern label', 'suitemart' ); ?>"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/counter {"end":48,"suffix":"h","icon":"truck","label":"<?php echo esc_attr_x( 'Average delivery time', 'Pattern label', 'suitemart' ); ?>"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:suitemart/counter {"end":12,"icon":"globe","label":"<?php echo esc_attr_x( 'Countries served', 'Pattern label', 'suitemart' ); ?>"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
