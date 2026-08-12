import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * The editor shows a sample row rather than the real list.
 *
 * The comparison list is in the visitor's browser, so there is nothing for the
 * editor to read — and an empty table would give no sense of the layout. The
 * placeholder row is clearly marked as such.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element} Editor markup.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { showImage, showRating, showStock, showSku } = attributes;

	const blockProps = useBlockProps( { className: 'sm-compare-table' } );

	const columns = [
		showImage && '',
		__( 'Product', 'suitemart' ),
		__( 'Price', 'suitemart' ),
		showRating && __( 'Rating', 'suitemart' ),
		showStock && __( 'Availability', 'suitemart' ),
		showSku && __( 'SKU', 'suitemart' ),
		'',
	].filter( ( label ) => label !== false );

	const sample = [
		showImage && '',
		__( 'Sample product', 'suitemart' ),
		'$19.99',
		showRating && '4.5 / 5',
		showStock && __( 'In stock', 'suitemart' ),
		showSku && 'SM-001',
		'',
	].filter( ( value ) => value !== false );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Columns', 'suitemart' ) }>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Image', 'suitemart' ) }
						checked={ showImage }
						onChange={ ( value ) =>
							setAttributes( { showImage: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Rating', 'suitemart' ) }
						checked={ showRating }
						onChange={ ( value ) =>
							setAttributes( { showRating: value } )
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
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'SKU', 'suitemart' ) }
						checked={ showSku }
						onChange={ ( value ) =>
							setAttributes( { showSku: value } )
						}
					/>
					<p>
						{ __(
							'Visitors see the products they added to compare. The list is kept in their browser, so this preview shows sample data.',
							'suitemart'
						) }
					</p>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="sm-compare-table__scroll">
					<table>
						<thead>
							<tr>
								{ columns.map( ( label, index ) => (
									<th key={ index } scope="col">
										{ label }
									</th>
								) ) }
							</tr>
						</thead>
						<tbody>
							<tr>
								{ sample.map( ( value, index ) => (
									<td key={ index }>{ value }</td>
								) ) }
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</>
	);
}
