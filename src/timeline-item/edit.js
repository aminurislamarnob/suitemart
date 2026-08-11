import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';

const TEMPLATE = [
	[ 'core/paragraph', { placeholder: __( 'What happened…', 'suitemart' ) } ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { date, title, titleLevel, isComplete } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-timeline__item${ isComplete ? ' is-complete' : '' }`,
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-timeline__body' },
		{ template: TEMPLATE, templateLock: false }
	);

	const TitleTag = `h${ titleLevel }`;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Timeline entry', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Heading level', 'suitemart' ) }
						value={ String( titleLevel ) }
						options={ [ 2, 3, 4, 5, 6 ].map( ( n ) => ( {
							label: `H${ n }`,
							value: String( n ),
						} ) ) }
						onChange={ ( value ) =>
							setAttributes( { titleLevel: Number( value ) } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Mark as complete', 'suitemart' ) }
						help={ __(
							'Fills in the marker. Use it for stages already reached.',
							'suitemart'
						) }
						checked={ isComplete }
						onChange={ ( value ) =>
							setAttributes( { isComplete: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<li { ...blockProps }>
				<span className="sm-timeline__dot" aria-hidden="true" />
				<RichText
					identifier="date"
					tagName="p"
					className="sm-timeline__date"
					value={ date }
					onChange={ ( value ) => setAttributes( { date: value } ) }
					placeholder={ __( '2024', 'suitemart' ) }
					allowedFormats={ [] }
				/>
				<RichText
					identifier="title"
					tagName={ TitleTag }
					className="sm-timeline__title"
					value={ title }
					onChange={ ( value ) => setAttributes( { title: value } ) }
					placeholder={ __( 'Milestone…', 'suitemart' ) }
					allowedFormats={ [] }
				/>
				<div { ...innerBlocksProps } />
			</li>
		</>
	);
}
