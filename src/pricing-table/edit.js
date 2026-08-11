import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';

const TEMPLATE = [
	[
		'core/list',
		{},
		[
			[ 'core/list-item', { content: __( 'Feature one', 'suitemart' ) } ],
			[ 'core/list-item', { content: __( 'Feature two', 'suitemart' ) } ],
			[
				'core/list-item',
				{ content: __( 'Feature three', 'suitemart' ) },
			],
		],
	],
	[
		'core/buttons',
		{},
		[ [ 'core/button', { text: __( 'Choose plan', 'suitemart' ) } ] ],
	],
];

export default function Edit( { attributes, setAttributes } ) {
	const {
		planName,
		planLevel,
		currency,
		price,
		period,
		summary,
		badge,
		featured,
	} = attributes;

	const blockProps = useBlockProps( {
		className: `sm-pricing${ featured ? ' sm-pricing--featured' : '' }`,
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-pricing__body' },
		{ template: TEMPLATE, templateLock: false }
	);

	const NameTag = `h${ planLevel }`;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pricing plan', 'suitemart' ) }>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Currency symbol', 'suitemart' ) }
						value={ currency }
						onChange={ ( value ) =>
							setAttributes( { currency: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Billing period', 'suitemart' ) }
						help={ __( 'For example “/month”.', 'suitemart' ) }
						value={ period }
						onChange={ ( value ) =>
							setAttributes( { period: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Badge', 'suitemart' ) }
						help={ __(
							'A short label above the plan name, such as “Most popular”.',
							'suitemart'
						) }
						value={ badge }
						onChange={ ( value ) =>
							setAttributes( { badge: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Plan heading level', 'suitemart' ) }
						value={ String( planLevel ) }
						options={ [ 2, 3, 4, 5, 6 ].map( ( n ) => ( {
							label: `H${ n }`,
							value: String( n ),
						} ) ) }
						onChange={ ( value ) =>
							setAttributes( { planLevel: Number( value ) } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Highlight this plan', 'suitemart' ) }
						checked={ featured }
						onChange={ ( value ) =>
							setAttributes( { featured: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ badge && <p className="sm-pricing__badge">{ badge }</p> }

				<RichText
					identifier="planName"
					tagName={ NameTag }
					className="sm-pricing__name"
					value={ planName }
					onChange={ ( value ) =>
						setAttributes( { planName: value } )
					}
					placeholder={ __( 'Plan name…', 'suitemart' ) }
					allowedFormats={ [] }
				/>

				<p className="sm-pricing__price">
					{ currency && (
						<span className="sm-pricing__currency">
							{ currency }
						</span>
					) }
					<RichText
						identifier="price"
						tagName="span"
						className="sm-pricing__amount"
						value={ price }
						onChange={ ( value ) =>
							setAttributes( { price: value } )
						}
						placeholder={ __( '29', 'suitemart' ) }
						allowedFormats={ [] }
					/>
					{ period && (
						<span className="sm-pricing__period">{ period }</span>
					) }
				</p>

				<RichText
					identifier="summary"
					tagName="p"
					className="sm-pricing__summary"
					value={ summary }
					onChange={ ( value ) =>
						setAttributes( { summary: value } )
					}
					placeholder={ __( 'Who this plan suits…', 'suitemart' ) }
					allowedFormats={ [] }
				/>

				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
