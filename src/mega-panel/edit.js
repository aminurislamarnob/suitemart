import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

const TEMPLATE = [
	[
		'core/columns',
		{},
		[
			[
				'core/column',
				{},
				[
					[
						'core/heading',
						{ level: 3, content: __( 'Category', 'suitemart' ) },
					],
					[
						'core/list',
						{},
						[
							[
								'core/list-item',
								{ content: __( 'Link', 'suitemart' ) },
							],
						],
					],
				],
			],
			[ 'core/column', {}, [] ],
		],
	],
];

export default function Edit( { attributes, setAttributes } ) {
	const { panelWidth, align } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-mega-panel sm-mega-panel--${ panelWidth } sm-mega-panel--align-${ align }`,
	} );

	// No allowedBlocks restriction: accepting any block is the entire reason
	// this block exists rather than using core/navigation (decision 14).
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-mega-panel__inner' },
		{ template: TEMPLATE, templateLock: false }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Panel', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Width', 'suitemart' ) }
						value={ panelWidth }
						options={ [
							{
								label: __( 'Fit content', 'suitemart' ),
								value: 'auto',
							},
							{
								label: __( 'Content width', 'suitemart' ),
								value: 'content',
							},
							{
								label: __( 'Wide width', 'suitemart' ),
								value: 'wide',
							},
							{
								label: __( 'Full width', 'suitemart' ),
								value: 'full',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { panelWidth: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Alignment', 'suitemart' ) }
						help={ __(
							'How the panel aligns to its menu item.',
							'suitemart'
						) }
						value={ align }
						options={ [
							{
								label: __( 'Start', 'suitemart' ),
								value: 'start',
							},
							{
								label: __( 'Center', 'suitemart' ),
								value: 'center',
							},
							{ label: __( 'End', 'suitemart' ), value: 'end' },
						] }
						onChange={ ( value ) =>
							setAttributes( { align: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
