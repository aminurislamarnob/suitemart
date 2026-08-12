import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, RangeControl } from '@wordpress/components';
import Icon from '../_shared/Icon';

export default function Edit( { attributes, setAttributes } ) {
	const { appearance, iconSize } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-compare-button sm-compare-button--${ appearance }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Compare button', 'suitemart' ) }>
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
					<p>
						{ __(
							'The comparison list is kept in each visitor’s browser and holds a fixed number of products; adding another drops the oldest. Change the limit with the suitemart_compare_limit filter.',
							'suitemart'
						) }
					</p>
				</PanelBody>
			</InspectorControls>

			<button type="button" { ...blockProps }>
				<span className="sm-compare-button__icon">
					<Icon name="shuffle" size={ iconSize } />
				</span>
				{ appearance === 'icon-label' && (
					<span className="sm-compare-button__label">
						{ __( 'Add to compare', 'suitemart' ) }
					</span>
				) }
			</button>
		</>
	);
}
