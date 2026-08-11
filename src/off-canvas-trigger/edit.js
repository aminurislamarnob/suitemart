import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import Icon from '../_shared/Icon';
import { iconOptions } from '../_shared/icons';

export default function Edit( { attributes, setAttributes } ) {
	const { panelId, label, icon, showLabel } = attributes;

	const blockProps = useBlockProps( {
		className: 'sm-off-canvas-trigger',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Trigger', 'suitemart' ) }>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Opens panel ID', 'suitemart' ) }
						help={ __(
							'Must match the Panel ID of the off-canvas panel this opens.',
							'suitemart'
						) }
						value={ panelId }
						onChange={ ( value ) =>
							setAttributes( { panelId: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Label', 'suitemart' ) }
						value={ label }
						placeholder={ __( 'Open panel', 'suitemart' ) }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show label', 'suitemart' ) }
						help={ __(
							'When hidden, the label is still read by screen readers.',
							'suitemart'
						) }
						checked={ showLabel }
						onChange={ ( value ) =>
							setAttributes( { showLabel: value } )
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
				</PanelBody>
			</InspectorControls>

			<button type="button" { ...blockProps }>
				<Icon name={ icon } size={ 20 } />
				{ showLabel && (
					<span className="sm-off-canvas-trigger__label">
						{ label || __( 'Open panel', 'suitemart' ) }
					</span>
				) }
			</button>
		</>
	);
}
