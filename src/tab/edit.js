import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';
import { iconOptions } from '../_shared/icons';

const TEMPLATE = [
	[ 'core/paragraph', { placeholder: __( 'Tab content…', 'suitemart' ) } ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { label, icon } = attributes;

	const blockProps = useBlockProps( { className: 'sm-tabs__panel' } );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
		templateLock: false,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Tab', 'suitemart' ) }>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Label', 'suitemart' ) }
						help={ __(
							'Shown on the tab button. Keep it short — long labels scroll on narrow screens.',
							'suitemart'
						) }
						value={ label }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Icon', 'suitemart' ) }
						value={ icon }
						options={ iconOptions() }
						onChange={ ( value ) =>
							setAttributes( { icon: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...innerBlocksProps } />
		</>
	);
}
