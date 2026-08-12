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
} from '@wordpress/components';

const ALLOWED = [ 'suitemart/hotspot' ];

const TEMPLATE = [
	[ 'suitemart/hotspot', { x: 32, y: 38 } ],
	[ 'suitemart/hotspot', { x: 68, y: 62 } ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { mediaId, mediaUrl, mediaAlt, markerStyle, pulse } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-hotspots sm-hotspots--${ markerStyle }${
			pulse ? ' has-pulse' : ''
		}`,
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-hotspots__frame' },
		{ allowedBlocks: ALLOWED, template: TEMPLATE }
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
					labels={ { title: __( 'Hotspot image', 'suitemart' ) } }
					onSelect={ onSelectMedia }
				/>
			</div>
		);
	}

	// The image sits behind the inner blocks rather than inside them, so that
	// dragging a marker never reorders it into the block list.
	const { children, ...frameProps } = innerBlocksProps;

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
				<PanelBody title={ __( 'Hotspots', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Marker', 'suitemart' ) }
						value={ markerStyle }
						options={ [
							{ label: __( 'Plus', 'suitemart' ), value: 'plus' },
							{ label: __( 'Dot', 'suitemart' ), value: 'dot' },
							{
								label: __( 'Numbered', 'suitemart' ),
								value: 'number',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { markerStyle: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Pulse', 'suitemart' ) }
						help={ __(
							'Draws attention to the markers. Stops for visitors who prefer reduced motion.',
							'suitemart'
						) }
						checked={ pulse }
						onChange={ ( value ) =>
							setAttributes( { pulse: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Image description', 'suitemart' ) }
						help={ __(
							'Describes the image for screen readers. Leave empty if it is purely decorative.',
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
				<div { ...frameProps }>
					<img
						className="sm-hotspots__image"
						src={ mediaUrl }
						alt={ mediaAlt }
					/>
					{ children }
				</div>
			</div>
		</>
	);
}
