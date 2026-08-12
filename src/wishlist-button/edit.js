import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, RangeControl } from '@wordpress/components';
import Icon from '../_shared/Icon';

export default function Edit( { attributes, setAttributes } ) {
	const { appearance, iconSize } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-wishlist-button sm-wishlist-button--${ appearance }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Wishlist button', 'suitemart' ) }>
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
							'The wishlist is kept in each visitor’s browser, so it needs no account and sets no cookie — which is what lets pages stay cacheable.',
							'suitemart'
						) }
					</p>
				</PanelBody>
			</InspectorControls>

			<button type="button" { ...blockProps }>
				<span className="sm-wishlist-button__icon">
					<Icon name="heart" size={ iconSize } />
				</span>
				{ appearance === 'icon-label' && (
					<span className="sm-wishlist-button__label">
						{ __( 'Add to wishlist', 'suitemart' ) }
					</span>
				) }
			</button>
		</>
	);
}
