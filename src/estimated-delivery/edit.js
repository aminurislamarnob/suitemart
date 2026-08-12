import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { minDays, maxDays } = attributes;

	const blockProps = useBlockProps( {
		className: 'sm-estimated-delivery',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Delivery time', 'suitemart' ) }>
					<RangeControl
						label={ __( 'Minimum working days', 'suitemart' ) }
						value={ minDays }
						onChange={ ( value ) =>
							setAttributes( { minDays: value } )
						}
						min={ 0 }
						max={ 30 }
					/>
					<RangeControl
						label={ __( 'Maximum working days', 'suitemart' ) }
						value={ maxDays }
						onChange={ ( value ) =>
							setAttributes( { maxDays: value } )
						}
						min={ minDays }
						max={ 60 }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<svg
					xmlns="http://www.w3.org/2000/svg"
					width="20"
					height="20"
					viewBox="0 0 24 24"
					fill="none"
					stroke="currentColor"
					strokeWidth="2"
					strokeLinecap="round"
					strokeLinejoin="round"
				>
					<path d="M10 17h4V5H2v12h3" />
					<path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5" />
					<path d="M14 17h1" />
					<circle cx="7.5" cy="17.5" r="2.5" />
					<circle cx="17.5" cy="17.5" r="2.5" />
				</svg>
				<span>
					{ /* translators: 1: start date, 2: end date */ }
					{ __( 'Estimated delivery: Oct 12 – Oct 15', 'suitemart' ) }
				</span>
			</div>
		</>
	);
}
