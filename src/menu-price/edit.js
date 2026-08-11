import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { title, description, price, oldPrice, badge, url, showLeader } =
		attributes;

	const blockProps = useBlockProps( {
		className: `sm-menu-price${
			showLeader ? ' sm-menu-price--leader' : ''
		}`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Price list item', 'suitemart' ) }>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Previous price', 'suitemart' ) }
						help={ __(
							'Shown struck through before the current price.',
							'suitemart'
						) }
						value={ oldPrice }
						onChange={ ( value ) =>
							setAttributes( { oldPrice: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Badge', 'suitemart' ) }
						value={ badge }
						onChange={ ( value ) =>
							setAttributes( { badge: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						type="url"
						label={ __( 'Link', 'suitemart' ) }
						value={ url }
						onChange={ ( value ) =>
							setAttributes( { url: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Dotted leader', 'suitemart' ) }
						help={ __(
							'Draws dots between the name and the price.',
							'suitemart'
						) }
						checked={ showLeader }
						onChange={ ( value ) =>
							setAttributes( { showLeader: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="sm-menu-price__head">
					<RichText
						identifier="title"
						tagName="p"
						className="sm-menu-price__title"
						value={ title }
						onChange={ ( value ) =>
							setAttributes( { title: value } )
						}
						placeholder={ __( 'Item name…', 'suitemart' ) }
						allowedFormats={ [] }
					/>
					{ showLeader && (
						<span
							className="sm-menu-price__leader"
							aria-hidden="true"
						/>
					) }
					<RichText
						identifier="price"
						tagName="p"
						className="sm-menu-price__price"
						value={ price }
						onChange={ ( value ) =>
							setAttributes( { price: value } )
						}
						placeholder={ __( '£0.00', 'suitemart' ) }
						allowedFormats={ [] }
					/>
				</div>
				<RichText
					identifier="description"
					tagName="p"
					className="sm-menu-price__description"
					value={ description }
					onChange={ ( value ) =>
						setAttributes( { description: value } )
					}
					placeholder={ __( 'Short description…', 'suitemart' ) }
					allowedFormats={ [] }
				/>
			</div>
		</>
	);
}
