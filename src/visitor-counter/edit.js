import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { minVisitors, maxVisitors } = attributes;

	const blockProps = useBlockProps( {
		className: 'sm-visitor-counter',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'suitemart' ) }>
					<RangeControl
						label={ __( 'Minimum visitors', 'suitemart' ) }
						value={ minVisitors }
						onChange={ ( value ) =>
							setAttributes( { minVisitors: value } )
						}
						min={ 1 }
						max={ 1000 }
					/>
					<RangeControl
						label={ __( 'Maximum visitors', 'suitemart' ) }
						value={ maxVisitors }
						onChange={ ( value ) =>
							setAttributes( { maxVisitors: value } )
						}
						min={ minVisitors }
						max={ 2000 }
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
					<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
					<circle cx="9" cy="7" r="4" />
					<path d="M22 21v-2a4 4 0 0 0-3-3.87" />
					<path d="M16 3.13a4 4 0 0 1 0 7.75" />
				</svg>
				<span>
					<strong>{ maxVisitors }</strong>{ ' ' }
					{ __( 'people are viewing this right now.', 'suitemart' ) }
				</span>
			</div>
		</>
	);
}
