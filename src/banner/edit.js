import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	BlockControls,
	MediaReplaceFlow,
	MediaPlaceholder,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
	RangeControl,
} from '@wordpress/components';

const TEMPLATE = [
	[
		'core/heading',
		{ level: 3, content: __( 'Banner heading', 'suitemart' ) },
	],
	[
		'core/paragraph',
		{ placeholder: __( 'Short supporting line…', 'suitemart' ) },
	],
];

const POSITIONS = [
	'top-left',
	'top-center',
	'top-right',
	'center-left',
	'center-center',
	'center-right',
	'bottom-left',
	'bottom-center',
	'bottom-right',
];

export default function Edit( { attributes, setAttributes } ) {
	const {
		mediaId,
		mediaUrl,
		mediaAlt,
		url,
		opensInNewTab,
		aspectRatio,
		contentPosition,
		hoverEffect,
		overlayOpacity,
	} = attributes;

	const blockProps = useBlockProps( {
		className: `sm-banner sm-banner--${ contentPosition } sm-banner--hover-${ hoverEffect }`,
		style: {
			'--sm-banner-ratio': aspectRatio,
			'--sm-banner-overlay': overlayOpacity / 100,
		},
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-banner__content' },
		{ template: TEMPLATE, templateLock: false }
	);

	const onSelectMedia = ( media ) =>
		setAttributes( {
			mediaId: media?.id ?? 0,
			mediaUrl: media?.url ?? '',
			mediaAlt: media?.alt ?? '',
		} );

	if ( ! mediaUrl ) {
		return (
			<div { ...blockProps }>
				<MediaPlaceholder
					accept="image/*"
					allowedTypes={ [ 'image' ] }
					labels={ { title: __( 'Banner image', 'suitemart' ) } }
					onSelect={ onSelectMedia }
				/>
			</div>
		);
	}

	return (
		<>
			<BlockControls>
				<MediaReplaceFlow
					mediaId={ mediaId }
					mediaURL={ mediaUrl }
					allowedTypes={ [ 'image' ] }
					accept="image/*"
					onSelect={ onSelectMedia }
				/>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={ __( 'Banner', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Aspect ratio', 'suitemart' ) }
						value={ aspectRatio }
						options={ [
							{ label: '1:1', value: '1/1' },
							{ label: '3:2', value: '3/2' },
							{ label: '4:3', value: '4/3' },
							{ label: '16:9', value: '16/9' },
							{ label: '2:3', value: '2/3' },
						] }
						onChange={ ( value ) =>
							setAttributes( { aspectRatio: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Content position', 'suitemart' ) }
						value={ contentPosition }
						options={ POSITIONS.map( ( p ) => ( {
							label: p.replace( '-', ' ' ),
							value: p,
						} ) ) }
						onChange={ ( value ) =>
							setAttributes( { contentPosition: value } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Overlay opacity', 'suitemart' ) }
						help={ __(
							'Darkens the image so text over it stays readable.',
							'suitemart'
						) }
						value={ overlayOpacity }
						min={ 0 }
						max={ 100 }
						step={ 5 }
						onChange={ ( value ) =>
							setAttributes( { overlayOpacity: value ?? 25 } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Hover effect', 'suitemart' ) }
						help={ __(
							'Disabled automatically for visitors who prefer reduced motion.',
							'suitemart'
						) }
						value={ hoverEffect }
						options={ [
							{ label: __( 'None', 'suitemart' ), value: 'none' },
							{
								label: __( 'Zoom image', 'suitemart' ),
								value: 'zoom',
							},
							{ label: __( 'Lift', 'suitemart' ), value: 'lift' },
						] }
						onChange={ ( value ) =>
							setAttributes( { hoverEffect: value } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Link', 'suitemart' ) }
					initialOpen={ false }
				>
					<TextControl
						__nextHasNoMarginBottom
						type="url"
						label={ __( 'URL', 'suitemart' ) }
						help={ __(
							'Makes the whole banner clickable.',
							'suitemart'
						) }
						value={ url }
						onChange={ ( value ) =>
							setAttributes( { url: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Open in new tab', 'suitemart' ) }
						checked={ opensInNewTab }
						onChange={ ( value ) =>
							setAttributes( { opensInNewTab: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Image description', 'suitemart' ) }
						help={ __(
							'Describes the image for screen readers. Leave empty if the image is purely decorative.',
							'suitemart'
						) }
						value={ mediaAlt }
						onChange={ ( value ) =>
							setAttributes( { mediaAlt: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="sm-banner__media">
					<img
						className="sm-banner__image"
						src={ mediaUrl }
						alt={ mediaAlt }
					/>
					{ overlayOpacity > 0 && (
						<span
							className="sm-banner__overlay"
							aria-hidden="true"
						/>
					) }
				</div>
				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
