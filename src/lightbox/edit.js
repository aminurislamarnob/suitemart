import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

const TEMPLATE = [ [ 'core/gallery', { linkTo: 'media' } ] ];

export default function Edit( { attributes, setAttributes } ) {
	const { showCaptions, loop } = attributes;

	const blockProps = useBlockProps( { className: 'sm-lightbox' } );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
		templateLock: false,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Lightbox', 'suitemart' ) }>
					<p>
						{ __(
							'Images only enlarge when they link to the media file. Set “Link to: Media file” on the gallery or image inside.',
							'suitemart'
						) }
					</p>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show captions', 'suitemart' ) }
						help={ __(
							'Uses the caption written under the image, or its description if there is none.',
							'suitemart'
						) }
						checked={ showCaptions }
						onChange={ ( value ) =>
							setAttributes( { showCaptions: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Loop', 'suitemart' ) }
						help={ __(
							'Move from the last image back to the first.',
							'suitemart'
						) }
						checked={ loop }
						onChange={ ( value ) =>
							setAttributes( { loop: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...innerBlocksProps } />
		</>
	);
}
