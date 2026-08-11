import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

const ALLOWED = [ 'suitemart/timeline-item' ];

const TEMPLATE = [
	[ 'suitemart/timeline-item' ],
	[ 'suitemart/timeline-item' ],
	[ 'suitemart/timeline-item' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { layout } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-timeline sm-timeline--${ layout }`,
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED,
		template: TEMPLATE,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Timeline', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Layout', 'suitemart' ) }
						help={ __(
							'Alternating sides apply on wide screens only; narrow screens always stack.',
							'suitemart'
						) }
						value={ layout }
						options={ [
							{
								label: __( 'Stacked', 'suitemart' ),
								value: 'stacked',
							},
							{
								label: __( 'Alternating', 'suitemart' ),
								value: 'alternating',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { layout: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<ol { ...innerBlocksProps } />
		</>
	);
}
