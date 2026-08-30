import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, RangeControl } from '@wordpress/components';
import Icon from '../_shared/Icon';

export default function Edit( { attributes, setAttributes } ) {
	const { appearance, iconSize } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-add-to-cart sm-add-to-cart--${ appearance }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Add to cart', 'suitemart' ) }>
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
							'Products that cannot be added in one click — variable, external, or out of stock — render as a link to the product instead, using WooCommerce’s own wording.',
							'suitemart'
						) }
					</p>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<button type="button" className="sm-add-to-cart__button">
					<span className="sm-add-to-cart__icon">
						<Icon name="shopping-cart" size={ iconSize } />
					</span>
					{ appearance === 'icon-label' && (
						<span className="sm-add-to-cart__label">
							{ __( 'Add to cart', 'suitemart' ) }
						</span>
					) }
				</button>
			</div>
		</>
	);
}
