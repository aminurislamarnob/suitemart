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
		start,
		end,
		duration,
		prefix,
		suffix,
		label,
		icon,
		iconSize,
		alignment,
	} = attributes;

	const blockProps = useBlockProps( {
		className: `sm-counter sm-counter--align-${ alignment }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Counter', 'suitemart' ) }>
					<TextControl
						__nextHasNoMarginBottom
						type="number"
						label={ __( 'Start value', 'suitemart' ) }
						value={ start }
						onChange={ ( value ) =>
							setAttributes( { start: Number( value ) || 0 } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						type="number"
						label={ __( 'End value', 'suitemart' ) }
						value={ end }
						onChange={ ( value ) =>
							setAttributes( { end: Number( value ) || 0 } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Duration (ms)', 'suitemart' ) }
						help={ __(
							'How long the count takes. Readers who prefer reduced motion always see the final value immediately.',
							'suitemart'
						) }
						value={ duration }
						min={ 200 }
						max={ 10000 }
						step={ 100 }
						onChange={ ( value ) =>
							setAttributes( { duration: value ?? 2000 } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Prefix', 'suitemart' ) }
						value={ prefix }
						onChange={ ( value ) =>
							setAttributes( { prefix: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Suffix', 'suitemart' ) }
						value={ suffix }
						onChange={ ( value ) =>
							setAttributes( { suffix: value } )
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
					{ icon && (
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
					) }
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
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ icon && (
					<div className="sm-counter__icon">
						<Icon name={ icon } size={ iconSize } />
					</div>
				) }
				<p className="sm-counter__value">
					{ prefix && (
						<span className="sm-counter__affix">{ prefix }</span>
					) }
					<span className="sm-counter__number">
						{ end.toLocaleString() }
					</span>
					{ suffix && (
						<span className="sm-counter__affix">{ suffix }</span>
					) }
				</p>
				<RichText
					identifier="label"
					tagName="p"
					className="sm-counter__label"
					value={ label }
					onChange={ ( value ) => setAttributes( { label: value } ) }
					placeholder={ __( 'Happy customers…', 'suitemart' ) }
					allowedFormats={ [] }
				/>
			</div>
		</>
	);
}
