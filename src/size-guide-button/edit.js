import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { label } = attributes;

	const blockProps = useBlockProps( {
		className: 'sm-size-guide-button',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'suitemart' ) }>
					<TextControl
						label={ __( 'Button label', 'suitemart' ) }
						value={ label }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
						placeholder={ __( 'Size guide', 'suitemart' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<button { ...blockProps }>
				{ label || __( 'Size guide', 'suitemart' ) }
			</button>
		</>
	);
}
