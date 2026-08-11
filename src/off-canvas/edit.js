import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	Notice,
} from '@wordpress/components';

const TEMPLATE = [
	[ 'core/paragraph', { placeholder: __( 'Panel content…', 'suitemart' ) } ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { panelId, side, size, title } = attributes;

	// The editor shows the panel inline and always open — a panel that slides
	// off screen in the canvas cannot be edited.
	const blockProps = useBlockProps( {
		className: `sm-off-canvas sm-off-canvas--${ side } is-editing`,
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-off-canvas__body' },
		{ template: TEMPLATE, templateLock: false }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Off-canvas panel', 'suitemart' ) }>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Panel ID', 'suitemart' ) }
						help={ __(
							'Triggers open this panel by matching this ID. Use the same value on both blocks.',
							'suitemart'
						) }
						value={ panelId }
						onChange={ ( value ) =>
							setAttributes( { panelId: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Title', 'suitemart' ) }
						help={ __(
							'Names the dialog for screen readers, and appears in its header.',
							'suitemart'
						) }
						value={ title }
						onChange={ ( value ) =>
							setAttributes( { title: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Slides in from', 'suitemart' ) }
						value={ side }
						options={ [
							{
								label: __( 'Start (left in LTR)', 'suitemart' ),
								value: 'start',
							},
							{
								label: __( 'End (right in LTR)', 'suitemart' ),
								value: 'end',
							},
							{ label: __( 'Top', 'suitemart' ), value: 'top' },
							{
								label: __( 'Bottom', 'suitemart' ),
								value: 'bottom',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { side: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Size', 'suitemart' ) }
						help={ __(
							'Width for side panels, height for top and bottom. A CSS length such as 22rem or 380px.',
							'suitemart'
						) }
						value={ size }
						onChange={ ( value ) =>
							setAttributes( { size: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ ! panelId && (
					<Notice status="warning" isDismissible={ false }>
						{ __(
							'Set a Panel ID so a trigger can open this panel.',
							'suitemart'
						) }
					</Notice>
				) }
				<div className="sm-off-canvas__panel">
					<div className="sm-off-canvas__header">
						<h2 className="sm-off-canvas__title">
							{ title || __( 'Panel', 'suitemart' ) }
						</h2>
					</div>
					<div { ...innerBlocksProps } />
				</div>
			</div>
		</>
	);
}
