import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	RangeControl,
} from '@wordpress/components';
import Icon from '../_shared/Icon';
import { iconOptions } from '../_shared/icons';

export default function Edit( { attributes, setAttributes } ) {
	const {
		icon,
		iconSize,
		title,
		titleLevel,
		description,
		url,
		orientation,
		alignment,
	} = attributes;

	const blockProps = useBlockProps( {
		className: `sm-infobox sm-infobox--${ orientation } sm-infobox--align-${ alignment }`,
	} );

	const TitleTag = `h${ titleLevel }`;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Info box', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Icon', 'suitemart' ) }
						value={ icon }
						options={ iconOptions() }
						onChange={ ( value ) =>
							setAttributes( { icon: value } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Icon size', 'suitemart' ) }
						value={ iconSize }
						min={ 12 }
						max={ 128 }
						step={ 2 }
						onChange={ ( value ) =>
							setAttributes( { iconSize: value ?? 32 } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Heading level', 'suitemart' ) }
						help={ __(
							'Pick the level that fits the page outline, not the size you want — size comes from the typography controls.',
							'suitemart'
						) }
						value={ String( titleLevel ) }
						options={ [ 2, 3, 4, 5, 6 ].map( ( n ) => ( {
							label: `H${ n }`,
							value: String( n ),
						} ) ) }
						onChange={ ( value ) =>
							setAttributes( { titleLevel: Number( value ) } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Orientation', 'suitemart' ) }
						value={ orientation }
						options={ [
							{
								label: __( 'Vertical', 'suitemart' ),
								value: 'vertical',
							},
							{
								label: __( 'Horizontal', 'suitemart' ),
								value: 'horizontal',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { orientation: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Alignment', 'suitemart' ) }
						value={ alignment }
						options={ [
							{
								label: __( 'Start', 'suitemart' ),
								value: 'start',
							},
							{
								label: __( 'Center', 'suitemart' ),
								value: 'center',
							},
							{ label: __( 'End', 'suitemart' ), value: 'end' },
						] }
						onChange={ ( value ) =>
							setAttributes( { alignment: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						type="url"
						label={ __( 'Link', 'suitemart' ) }
						help={ __(
							'Optional. Makes the whole box clickable.',
							'suitemart'
						) }
						value={ url }
						onChange={ ( value ) =>
							setAttributes( { url: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ icon && (
					<div
						className="sm-infobox__icon"
						style={ { fontSize: `${ iconSize }px` } }
					>
						<Icon name={ icon } size={ iconSize } />
					</div>
				) }
				<div className="sm-infobox__body">
					<RichText
						identifier="title"
						tagName={ TitleTag }
						className="sm-infobox__title"
						value={ title }
						onChange={ ( value ) =>
							setAttributes( { title: value } )
						}
						placeholder={ __( 'Heading…', 'suitemart' ) }
						allowedFormats={ [] }
					/>
					<RichText
						identifier="description"
						tagName="p"
						className="sm-infobox__description"
						value={ description }
						onChange={ ( value ) =>
							setAttributes( { description: value } )
						}
						placeholder={ __( 'Supporting text…', 'suitemart' ) }
						allowedFormats={ [] }
					/>
				</div>
			</div>
		</>
	);
}
