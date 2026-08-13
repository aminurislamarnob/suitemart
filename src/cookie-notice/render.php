<?php
/**
 * Cookie notice block.
 *
 * **What this block does, precisely: it shows a message, records which button
 * was pressed, and announces that choice. It does not block cookies, scripts or
 * trackers, because a theme block cannot — the code that sets them belongs to
 * plugins and to the site owner.** Whoever ships those has to listen for the
 * choice and act on it. The `suitemart-cookie-consent` event and the
 * `data-sm-consent` attribute on `<html>` exist for exactly that.
 *
 * Saying so plainly is not a disclaimer; it is the difference between a useful
 * component and a banner that makes a merchant believe they are compliant when
 * nothing has changed. Under the GDPR and the ePrivacy Directive consent must
 * be informed and freely given, and declining has to be as easy as accepting —
 * which is why there are two buttons of equal weight and no pre-made choice.
 *
 * The decision lives in localStorage rather than a cookie, like the wishlist
 * and comparison lists: no per-visitor cookie means the page stays cacheable.
 * It also means the server cannot know whether this visitor has already
 * answered, so the notice is served hidden and revealed by the browser when it
 * finds no answer stored. The other way round — served visible, hidden on
 * discovery — would flash the banner at everyone who had already dismissed it.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup (the message).
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return '';
}

$sm_position = suitemart_enum(
	$attributes['position'] ?? 'bottom',
	array( 'bottom', 'bottom-start', 'bottom-end' ),
	'bottom'
);

$sm_accept = isset( $attributes['acceptLabel'] ) && is_string( $attributes['acceptLabel'] ) && '' !== trim( $attributes['acceptLabel'] )
	? trim( $attributes['acceptLabel'] )
	: __( 'Accept', 'suitemart' );

$sm_decline = isset( $attributes['declineLabel'] ) && is_string( $attributes['declineLabel'] ) && '' !== trim( $attributes['declineLabel'] )
	? trim( $attributes['declineLabel'] )
	: __( 'Decline', 'suitemart' );

$sm_region = isset( $attributes['regionLabel'] ) && is_string( $attributes['regionLabel'] ) && '' !== trim( $attributes['regionLabel'] )
	? trim( $attributes['regionLabel'] )
	: __( 'Cookie notice', 'suitemart' );

$sm_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'sm-cookie-notice sm-cookie-notice--' . $sm_position )
);
?>
<div
	<?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>
	role="region"
	aria-label="<?php echo esc_attr( $sm_region ); ?>"
	hidden
	data-wp-interactive="suitemart/cookie-notice"
	<?php echo wp_interactivity_data_wp_context( array( 'isOpen' => false ) ); ?>
	data-wp-bind--hidden="!context.isOpen"
	data-wp-init="callbacks.decideVisibility"
>
	<div class="sm-cookie-notice__message">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
	</div>

	<?php
	/*
	 * Two buttons, same size, same weight, neither pre-selected. Making
	 * "Decline" quieter than "Accept" is a dark pattern with a name — nudging —
	 * and it is the specific thing regulators have been fining people for.
	 */
	?>
	<div class="sm-cookie-notice__actions">
		<button
			type="button"
			class="sm-cookie-notice__button sm-cookie-notice__button--decline"
			data-wp-on--click="actions.decline"
		>
			<?php echo esc_html( $sm_decline ); ?>
		</button>
		<button
			type="button"
			class="sm-cookie-notice__button sm-cookie-notice__button--accept"
			data-wp-on--click="actions.accept"
		>
			<?php echo esc_html( $sm_accept ); ?>
		</button>
	</div>
</div>
