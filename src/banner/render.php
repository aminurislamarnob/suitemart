<?php
/**
 * Banner block.
 *
 * @package Suitemart
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks markup.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$sm_media_id  = suitemart_clamp_int( $attributes['mediaId'] ?? 0, 0, 0, PHP_INT_MAX );
$sm_media_url = isset( $attributes['mediaUrl'] ) && is_string( $attributes['mediaUrl'] ) ? $attributes['mediaUrl'] : '';
$sm_url       = isset( $attributes['url'] ) && is_string( $attributes['url'] ) ? $attributes['url'] : '';
$sm_alt       = isset( $attributes['mediaAlt'] ) && is_string( $attributes['mediaAlt'] ) ? $attributes['mediaAlt'] : '';

if ( 0 === $sm_media_id && '' === $sm_media_url && '' === trim( $content ) ) {
	return '';
}

$sm_position = suitemart_enum(
	$attributes['contentPosition'] ?? 'bottom-left',
	array(
		'top-left',
		'top-center',
		'top-right',
		'center-left',
		'center-center',
		'center-right',
		'bottom-left',
		'bottom-center',
		'bottom-right',
	),
	'bottom-left'
);

$sm_hover   = suitemart_enum( $attributes['hoverEffect'] ?? 'zoom', array( 'none', 'zoom', 'lift' ), 'zoom' );
$sm_opacity = suitemart_clamp_int( $attributes['overlayOpacity'] ?? 25, 25, 0, 100 );
$sm_ratio   = isset( $attributes['aspectRatio'] ) && is_string( $attributes['aspectRatio'] ) ? $attributes['aspectRatio'] : '3/2';

// Only accept a simple `w/h` ratio; the value lands in a style attribute.
if ( 1 !== preg_match( '/^\d{1,3}\/\d{1,3}$/', $sm_ratio ) ) {
	$sm_ratio = '3/2';
}

$sm_wrapper = get_block_wrapper_attributes(
	array(
		'class' => sprintf(
			'sm-banner sm-banner--%s sm-banner--hover-%s',
			$sm_position,
			$sm_hover
		),
		'style' => sprintf(
			'--sm-banner-ratio:%s;--sm-banner-overlay:%s;',
			$sm_ratio,
			(string) ( $sm_opacity / 100 )
		),
	)
);

// A banner is decorative framing for its own content, so when the whole tile
// links somewhere the image must not repeat that content to screen readers.
$sm_image_alt = '' !== $sm_url ? '' : $sm_alt;

$sm_image = '';

if ( $sm_media_id > 0 ) {
	$sm_image = wp_get_attachment_image(
		$sm_media_id,
		'suitemart-card-wide',
		false,
		array(
			'class'   => 'sm-banner__image',
			'alt'     => $sm_image_alt,
			'loading' => 'lazy',
		)
	);
} elseif ( '' !== $sm_media_url ) {
	$sm_image = sprintf(
		'<img class="sm-banner__image" src="%s" alt="%s" loading="lazy" decoding="async" />',
		esc_url( $sm_media_url ),
		esc_attr( $sm_image_alt )
	);
}
?>
<div <?php echo $sm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes its output. ?>>
	<div class="sm-banner__media">
		<?php echo $sm_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from wp_get_attachment_image() or escaped above. ?>
		<?php if ( $sm_opacity > 0 ) : ?>
			<span class="sm-banner__overlay" aria-hidden="true"></span>
		<?php endif; ?>
	</div>

	<div class="sm-banner__content">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks are rendered and escaped by WordPress. ?>
	</div>

	<?php
	if ( '' !== $sm_url ) :
		/*
		 * A stretched link rather than wrapping the whole banner in an anchor:
		 * wrapping would swallow any buttons or links the editor placed inside
		 * the content, which is invalid and unusable with a keyboard.
		 */
		?>
		<a
			class="sm-banner__link"
			href="<?php echo esc_url( $sm_url ); ?>"
			<?php if ( ! empty( $attributes['opensInNewTab'] ) ) : ?>
				target="_blank" rel="noopener"
			<?php endif; ?>
		>
			<span class="screen-reader-text">
				<?php echo esc_html( '' !== $sm_alt ? $sm_alt : __( 'View', 'suitemart' ) ); ?>
			</span>
		</a>
	<?php endif; ?>
</div>
