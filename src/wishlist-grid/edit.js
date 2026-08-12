import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, ToggleControl } from '@wordpress/components';

/**
 * The editor shows sample cards rather than the real list.
 *
 * The saved list is in the visitor's browser, so there is nothing here to read,
 * and an empty grid would give no sense of the layout.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element} Editor markup.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { columns, showPrice, showStock } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-wishlist-grid sm-wishlist-grid--cols-${ columns }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Wishlist grid', 'suitemart' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Columns', 'suitemart' ) }
						value={ columns }
						min={ 1 }
						max={ 6 }
						onChange={ ( value ) =>
							setAttributes( { columns: value ?? 4 } )
						}
						help={ __(
							'The widest layout. Narrower screens step down to three, two, then one.',
							'suitemart'
						) }
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Price', 'suitemart' ) }
						checked={ showPrice }
						onChange={ ( value ) =>
							setAttributes( { showPrice: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Availability', 'suitemart' ) }
						checked={ showStock }
						onChange={ ( value ) =>
							setAttributes( { showStock: value } )
						}
					/>
					<p>
						{ __(
							'Visitors see the products they saved. The list is kept in their browser, so this preview shows sample products.',
							'suitemart'
						) }
					</p>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<ul className="sm-wishlist-grid__list">
					{ [ 1, 2, 3, 4 ].slice( 0, columns ).map( ( index ) => (
						<li key={ index } className="sm-wishlist-grid__item">
							<span className="sm-wishlist-grid__name">
								{ __( 'Sample product', 'suitemart' ) }
							</span>
							{ showPrice && (
								<p className="sm-wishlist-grid__price">
									$19.99
								</p>
							) }
							{ showStock && (
								<p className="sm-wishlist-grid__stock">
									{ __( 'In stock', 'suitemart' ) }
								</p>
							) }
						</li>
					) ) }
				</ul>
			</div>
		</>
	);
}
