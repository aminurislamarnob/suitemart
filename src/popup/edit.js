import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';

import Icon from '../_shared/Icon';

const TEMPLATE = [
	[
		'core/heading',
		{ level: 2, content: __( 'Join the list', 'suitemart' ) },
	],
	[
		'core/paragraph',
		{
			content: __(
				'Ten percent off your first order, and nothing else in your inbox.',
				'suitemart'
			),
		},
	],
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		trigger,
		delay,
		threshold,
		maxWidth,
		label,
		closeLabel,
		overlayClose,
		showOnce,
		onceKey,
	} = attributes;

	// Written once, from the only identifier a block has that survives the
	// request. Same arrangement as the floating block, and the same reason.
	useEffect( () => {
		if ( showOnce && ! onceKey ) {
			setAttributes( { onceKey: clientId.replace( /-/g, '' ) } );
		}
	}, [ showOnce, onceKey, clientId, setAttributes ] );

	/*
	 * A plain div, not a <dialog>: an open dialog in the editor canvas is a
	 * non-modal one the editor's own popovers would draw over, and a closed one
	 * cannot be edited at all.
	 */
	const blockProps = useBlockProps( {
		className: 'sm-popup',
		style: { '--sm-popup-max-width': `${ maxWidth }px` },
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-popup__content' },
		{ template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'When it opens', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Trigger', 'suitemart' ) }
						value={ trigger }
						options={ [
							{
								label: __( 'After a delay', 'suitemart' ),
								value: 'delay',
							},
							{
								label: __( 'After scrolling', 'suitemart' ),
								value: 'scroll',
							},
							{
								label: __( 'On leaving', 'suitemart' ),
								value: 'exit',
							},
						] }
						help={
							'exit' === trigger
								? __(
										'Exit intent is the pointer leaving through the top of the window. There is no touch equivalent, so this never fires on a phone.',
										'suitemart'
								  )
								: undefined
						}
						onChange={ ( value ) =>
							setAttributes( { trigger: value } )
						}
					/>
					{ 'delay' === trigger && (
						<RangeControl
							__nextHasNoMarginBottom
							label={ __( 'Delay (seconds)', 'suitemart' ) }
							value={ delay }
							min={ 0 }
							max={ 120 }
							step={ 1 }
							onChange={ ( value ) =>
								setAttributes( { delay: value ?? 5 } )
							}
						/>
					) }
					{ 'scroll' === trigger && (
						<RangeControl
							__nextHasNoMarginBottom
							label={ __( 'Opens after (px)', 'suitemart' ) }
							value={ threshold }
							min={ 0 }
							max={ 3000 }
							step={ 50 }
							onChange={ ( value ) =>
								setAttributes( { threshold: value ?? 800 } )
							}
						/>
					) }
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show once per visitor', 'suitemart' ) }
						help={ __(
							'Remembered in this browser. Turning it off means the popup opens on every page, which is the fastest way to lose a visitor.',
							'suitemart'
						) }
						checked={ showOnce }
						onChange={ ( value ) =>
							setAttributes( { showOnce: value } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'The dialog', 'suitemart' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Maximum width (px)', 'suitemart' ) }
						value={ maxWidth }
						min={ 240 }
						max={ 1200 }
						step={ 10 }
						onChange={ ( value ) =>
							setAttributes( { maxWidth: value ?? 520 } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Dialog name', 'suitemart' ) }
						help={ __(
							'Announced when the dialog opens. Name it after what it is for — "Newsletter signup" tells someone far more than "Notice".',
							'suitemart'
						) }
						placeholder={ __( 'Notice', 'suitemart' ) }
						value={ label }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Close label', 'suitemart' ) }
						placeholder={ __( 'Close', 'suitemart' ) }
						value={ closeLabel }
						onChange={ ( value ) =>
							setAttributes( { closeLabel: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __(
							'Close when the backdrop is clicked',
							'suitemart'
						) }
						checked={ overlayClose }
						onChange={ ( value ) =>
							setAttributes( { overlayClose: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<span className="sm-popup__close">
					<Icon name="x" size={ 20 } />
				</span>
				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
