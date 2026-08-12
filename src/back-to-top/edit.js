import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
} from '@wordpress/components';

import Icon from '../_shared/Icon';

export default function Edit( { attributes, setAttributes } ) {
	const { threshold, position, label, appearance } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-back-to-top sm-back-to-top--${ position } sm-back-to-top--${ appearance } is-visible`,
	} );

	const text = label || __( 'Back to top', 'suitemart' );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Back to top', 'suitemart' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Appears after (px)', 'suitemart' ) }
						help={ __(
							'How far down the page the visitor has to be before the button shows.',
							'suitemart'
						) }
						value={ threshold }
						min={ 0 }
						max={ 3000 }
						step={ 50 }
						onChange={ ( value ) =>
							setAttributes( { threshold: value ?? 400 } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Corner', 'suitemart' ) }
						value={ position }
						options={ [
							{
								label: __( 'Bottom end', 'suitemart' ),
								value: 'end',
							},
							{
								label: __( 'Bottom start', 'suitemart' ),
								value: 'start',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { position: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Appearance', 'suitemart' ) }
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
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Label', 'suitemart' ) }
						help={ __(
							'Read out by screen readers, and shown beside the icon when the appearance includes it.',
							'suitemart'
						) }
						value={ label }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<span className="sm-back-to-top__button">
					<Icon name="arrow-up" size={ 20 } />
					{ 'icon-label' === appearance && (
						<span className="sm-back-to-top__label">{ text }</span>
					) }
				</span>
			</div>
		</>
	);
}
