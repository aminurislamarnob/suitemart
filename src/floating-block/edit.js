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
		'core/paragraph',
		{
			placeholder: __(
				'Anything you like, pinned to a corner…',
				'suitemart'
			),
		},
	],
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		position,
		trigger,
		threshold,
		delay,
		maxWidth,
		dismissible,
		dismissLabel,
		remember,
		rememberKey,
		hideOnMobile,
	} = attributes;

	/*
	 * A remembered dismissal needs a key that survives the request, and the
	 * client id is the only stable identifier a block has. It is written once
	 * and left alone: duplicating the block gives the copy a new id here, so
	 * two panels never share a memory, and re-saving an existing one never
	 * resurrects a panel someone had closed.
	 */
	useEffect( () => {
		if ( remember && ! rememberKey ) {
			setAttributes( { rememberKey: clientId.replace( /-/g, '' ) } );
		}
	}, [ remember, rememberKey, clientId, setAttributes ] );

	const blockProps = useBlockProps( {
		className: `sm-floating-block sm-floating-block--${ position }`,
		style: { '--sm-floating-max-width': `${ maxWidth }px` },
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-floating-block__content' },
		{ template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Position', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Corner', 'suitemart' ) }
						value={ position }
						options={ [
							{
								label: __( 'Bottom end', 'suitemart' ),
								value: 'bottom-end',
							},
							{
								label: __( 'Bottom start', 'suitemart' ),
								value: 'bottom-start',
							},
							{
								label: __( 'Middle end', 'suitemart' ),
								value: 'middle-end',
							},
							{
								label: __( 'Middle start', 'suitemart' ),
								value: 'middle-start',
							},
							{
								label: __( 'Top end', 'suitemart' ),
								value: 'top-end',
							},
							{
								label: __( 'Top start', 'suitemart' ),
								value: 'top-start',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { position: value } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Maximum width (px)', 'suitemart' ) }
						value={ maxWidth }
						min={ 160 }
						max={ 720 }
						step={ 10 }
						onChange={ ( value ) =>
							setAttributes( { maxWidth: value ?? 360 } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Hide on small screens', 'suitemart' ) }
						help={ __(
							'A panel pinned to the corner of a phone covers a good third of the screen.',
							'suitemart'
						) }
						checked={ hideOnMobile }
						onChange={ ( value ) =>
							setAttributes( { hideOnMobile: value } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'When it appears', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Trigger', 'suitemart' ) }
						value={ trigger }
						options={ [
							{
								label: __( 'Straight away', 'suitemart' ),
								value: 'immediate',
							},
							{
								label: __( 'After scrolling', 'suitemart' ),
								value: 'scroll',
							},
							{
								label: __( 'After a delay', 'suitemart' ),
								value: 'delay',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { trigger: value } )
						}
					/>
					{ 'scroll' === trigger && (
						<RangeControl
							__nextHasNoMarginBottom
							label={ __( 'Appears after (px)', 'suitemart' ) }
							value={ threshold }
							min={ 0 }
							max={ 3000 }
							step={ 50 }
							onChange={ ( value ) =>
								setAttributes( { threshold: value ?? 600 } )
							}
						/>
					) }
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
				</PanelBody>

				<PanelBody title={ __( 'Closing', 'suitemart' ) }>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show a close button', 'suitemart' ) }
						checked={ dismissible }
						onChange={ ( value ) =>
							setAttributes( { dismissible: value } )
						}
					/>
					{ dismissible && (
						<>
							<TextControl
								__nextHasNoMarginBottom
								label={ __( 'Close label', 'suitemart' ) }
								help={ __(
									'Read out by screen readers.',
									'suitemart'
								) }
								placeholder={ __( 'Close', 'suitemart' ) }
								value={ dismissLabel }
								onChange={ ( value ) =>
									setAttributes( { dismissLabel: value } )
								}
							/>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __( 'Stay closed', 'suitemart' ) }
								help={ __(
									'Remembers the dismissal in this browser, so the panel does not come back on the next page.',
									'suitemart'
								) }
								checked={ remember }
								onChange={ ( value ) =>
									setAttributes( { remember: value } )
								}
							/>
						</>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div { ...innerBlocksProps } />
				{ dismissible && (
					<span className="sm-floating-block__dismiss">
						<Icon name="x" size={ 18 } />
					</span>
				) }
			</div>
		</>
	);
}
