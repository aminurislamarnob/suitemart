import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, RangeControl } from '@wordpress/components';
import Icon from '../_shared/Icon';

export default function Edit( { attributes, setAttributes } ) {
	const { appearance, iconSize } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-quick-view-button sm-quick-view-button--${ appearance }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Quick view button', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Style', 'suitemart' ) }
						value={ appearance }
						options={ [
							{
								label: __( 'Icon only', 'suitemart' ),
								value: 'icon',
							},
							{
								label: __( 'Icon and label', 'suitemart' ),
								value: 'icon-label',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { appearance: value } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Icon size', 'suitemart' ) }
						value={ iconSize }
						min={ 12 }
						max={ 48 }
						onChange={ ( value ) =>
							setAttributes( { iconSize: value ?? 20 } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<button type="button" { ...blockProps }>
				<span className="sm-quick-view-button__icon">
					<Icon name="search" size={ iconSize } />
				</span>
				{ appearance === 'icon-label' && (
					<span className="sm-quick-view-button__label">
						{ __( 'Quick view', 'suitemart' ) }
					</span>
				) }
			</button>
		</>
	);
}
