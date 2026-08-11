<?php
/**
 * Countdown block.
 *
 * The server renders the correct values rather than leaving zeros for
 * JavaScript to fill in: the countdown is meaningful content, so it must be
 * right in the initial HTML for crawlers, for the moment before hydration, and
 * for anyone browsing without JavaScript.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_all_units = array( 'days', 'hours', 'minutes', 'seconds' );

$sm_units = isset( $attributes['units'] ) && is_array( $attributes['units'] )
	? array_values( array_intersect( $sm_all_units, $attributes['units'] ) )
	: $sm_all_units;

if ( array() === $sm_units ) {
	$sm_units = $sm_all_units;
}

$sm_layout = suitemart_enum( $attributes['layout'] ?? 'boxed', array( 'inline', 'boxed' ), 'boxed' );

$sm_expired_text = isset( $attributes['expiredText'] ) && is_string( $attributes['expiredText'] ) && '' !== $attributes['expiredText']
	? $attributes['expiredText']
	: __( 'This offer has ended.', 'suitemart' );

// The attribute is a datetime-local value with no zone, so it is interpreted in
// the site's timezone — what the editor meant when they picked it.
$sm_raw_date = isset( $attributes['endDate'] ) && is_string( $attributes['endDate'] ) ? $attributes['endDate'] : '';
$sm_end      = null;

if ( '' !== $sm_raw_date ) {
	try {
		$sm_end = new DateTimeImmutable( $sm_raw_date, wp_timezone() );
	} catch ( Exception $e ) {
		$sm_end = null;
	}
}

if ( null === $sm_end ) {
	// Without a valid date there is nothing to count down to. Rendering an
	// all-zero timer would look like a broken clock.
	if ( ! is_admin() && ! defined( 'REST_REQUEST' ) ) {
		return '';
	}

	$sm_end = new DateTimeImmutable( 'now', wp_timezone() );
}

$sm_now       = new DateTimeImmutable( 'now', wp_timezone() );
$sm_remaining = max( 0, $sm_end->getTimestamp() - $sm_now->getTimestamp() );
$sm_expired   = 0 === $sm_remaining;

$sm_values = array(
	'days'    => (int) floor( $sm_remaining / DAY_IN_SECONDS ),
	'hours'   => (int) floor( ( $sm_remaining % DAY_IN_SECONDS ) / HOUR_IN_SECONDS ),
	'minutes' => (int) floor( ( $sm_remaining % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS ),
	'seconds' => $sm_remaining % MINUTE_IN_SECONDS,
);

$sm_labels = array(
	'days'    => _n_noop( 'Day', 'Days', 'suitemart' ),
	'hours'   => _n_noop( 'Hour', 'Hours', 'suitemart' ),
	'minutes' => _n_noop( 'Minute', 'Minutes', 'suitemart' ),
	'seconds' => _n_noop( 'Second', 'Seconds', 'suitemart' ),
);

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'sm-countdown sm-countdown--' . $sm_layout . ( $sm_expired ? ' is-expired' : '' ),
	)
);

/*
 * The digits are bound from context rather than from a derived state getter.
 * Directives are evaluated on the server as well as in the browser, and an
 * expression the server cannot resolve blanks the element — a JS-only getter
 * here meant the timer rendered with no digits at all before the module
 * loaded. Context is resolvable on both sides, and unlike state it is per
 * block, so two countdowns on one page keep their own values.
 *
 * Days are unpadded — "07 Days" reads as a mistake — while the time units are,
 * so the digits do not change width as they tick. view.js formats them the
 * same way.
 */
$sm_display = array(
	'days'    => (string) $sm_values['days'],
	'hours'   => str_pad( (string) $sm_values['hours'], 2, '0', STR_PAD_LEFT ),
	'minutes' => str_pad( (string) $sm_values['minutes'], 2, '0', STR_PAD_LEFT ),
	'seconds' => str_pad( (string) $sm_values['seconds'], 2, '0', STR_PAD_LEFT ),
);

$sm_context = array(
	'endTimestamp' => $sm_end->getTimestamp() * 1000,
	'isExpired'    => $sm_expired,
	'values'       => $sm_values,
	'display'      => $sm_display,
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	data-wp-interactive="suitemart/countdown"
	<?php echo wp_interactivity_data_wp_context( $sm_context ); ?>
	data-wp-init="callbacks.start"
	data-wp-class--is-expired="context.isExpired"
>
	<?php
	/*
	 * A ticking clock announced on every change would make the page unusable
	 * with a screen reader, so the live region is off and the whole timer is
	 * given a single static description instead.
	 */
	?>
	<div class="sm-countdown__units" aria-hidden="true" data-wp-bind--hidden="context.isExpired">
		<?php foreach ( $sm_units as $sm_unit ) : ?>
			<div class="sm-countdown__unit">
				<span class="sm-countdown__value" data-wp-text="context.display.<?php echo esc_attr( $sm_unit ); ?>">
					<?php echo esc_html( $sm_display[ $sm_unit ] ); ?>
				</span>
				<span class="sm-countdown__label">
					<?php echo esc_html( translate_nooped_plural( $sm_labels[ $sm_unit ], $sm_values[ $sm_unit ], 'suitemart' ) ); ?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>

	<p class="sm-countdown__sr-summary screen-reader-text">
		<?php
		echo esc_html(
			sprintf(
				/* translators: %s: formatted end date and time. */
				__( 'Offer ends %s.', 'suitemart' ),
				wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $sm_end->getTimestamp() )
			)
		);
		?>
	</p>

	<p class="sm-countdown__expired" data-wp-bind--hidden="!context.isExpired" <?php echo $sm_expired ? '' : 'hidden'; ?>>
		<?php echo esc_html( $sm_expired_text ); ?>
	</p>
</div>
