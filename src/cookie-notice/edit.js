import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';

const TEMPLATE = [
	[
		'core/paragraph',
		{
			placeholder: __( 'Explain what is set and why…', 'suitemart' ),
			content: __(
				'We use cookies to run the shop and to understand how it is used.',
				'suitemart'
			),
		},
	],
];

export default function Edit( { attributes, setAttributes } ) {
	const { acceptLabel, declineLabel, regionLabel, position } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-cookie-notice sm-cookie-notice--${ position }`,
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-cookie-notice__message' },
		{ template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Cookie notice', 'suitemart' ) }>
					<p>
						{ __(
							'This block records the visitor’s choice and announces it — it does not block cookies or scripts by itself. A consent manager has to listen for the suitemart-cookie-consent event, or read the data-sm-consent attribute on the html element, and act on it.',
							'suitemart'
						) }
					</p>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Position', 'suitemart' ) }
						value={ position }
						options={ [
							{
								label: __( 'Full-width bar', 'suitemart' ),
								value: 'bottom',
							},
							{
								label: __( 'Bottom start', 'suitemart' ),
								value: 'bottom-start',
							},
							{
								label: __( 'Bottom end', 'suitemart' ),
								value: 'bottom-end',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { position: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Accept label', 'suitemart' ) }
						placeholder={ __( 'Accept', 'suitemart' ) }
						value={ acceptLabel }
						onChange={ ( value ) =>
							setAttributes( { acceptLabel: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Decline label', 'suitemart' ) }
						help={ __(
							'Declining has to be as easy as accepting, so both buttons are drawn the same size and weight.',
							'suitemart'
						) }
						placeholder={ __( 'Decline', 'suitemart' ) }
						value={ declineLabel }
						onChange={ ( value ) =>
							setAttributes( { declineLabel: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Region label', 'suitemart' ) }
						help={ __(
							'Announced to screen readers as the name of this region.',
							'suitemart'
						) }
						placeholder={ __( 'Cookie notice', 'suitemart' ) }
						value={ regionLabel }
						onChange={ ( value ) =>
							setAttributes( { regionLabel: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div { ...innerBlocksProps } />
				<div className="sm-cookie-notice__actions">
					<span className="sm-cookie-notice__button sm-cookie-notice__button--decline">
						{ declineLabel || __( 'Decline', 'suitemart' ) }
					</span>
					<span className="sm-cookie-notice__button sm-cookie-notice__button--accept">
						{ acceptLabel || __( 'Accept', 'suitemart' ) }
					</span>
				</div>
			</div>
		</>
	);
}
