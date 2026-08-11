import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';

const TEMPLATE = [
	[
		'suitemart/nav-item',
		{ label: __( 'Shop', 'suitemart' ), hasPanel: true },
	],
	[ 'suitemart/nav-item', { label: __( 'About', 'suitemart' ) } ],
	[ 'suitemart/nav-item', { label: __( 'Contact', 'suitemart' ) } ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { mobileBreakpoint, submenuTrigger, ariaLabel } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-nav sm-nav--break-${ mobileBreakpoint } sm-nav--trigger-${ submenuTrigger }`,
	} );

	// The editor renders the nav flat and always-expanded: a drawer that
	// collapses in the canvas is unusable to edit.
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-nav__list' },
		{
			allowedBlocks: [ 'suitemart/nav-item' ],
			template: TEMPLATE,
			orientation: 'horizontal',
			templateLock: false,
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Navigation', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Collapse to drawer below', 'suitemart' ) }
						help={ __(
							'The viewport width at which the menu becomes a mobile drawer.',
							'suitemart'
						) }
						value={ mobileBreakpoint }
						options={ [
							{
								label: __( '576px (Small)', 'suitemart' ),
								value: 'sm',
							},
							{
								label: __( '768px (Medium)', 'suitemart' ),
								value: 'md',
							},
							{
								label: __( '1024px (Large)', 'suitemart' ),
								value: 'lg',
							},
							{
								label: __(
									'1280px (Extra large)',
									'suitemart'
								),
								value: 'xl',
							},
							{
								label: __( 'Always a drawer', 'suitemart' ),
								value: 'never',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { mobileBreakpoint: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Open panels on', 'suitemart' ) }
						help={ __(
							'Panels always open on click and on keyboard activation. Hover is an addition, not a replacement.',
							'suitemart'
						) }
						value={ submenuTrigger }
						options={ [
							{
								label: __( 'Hover and click', 'suitemart' ),
								value: 'hover',
							},
							{
								label: __( 'Click only', 'suitemart' ),
								value: 'click',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { submenuTrigger: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Accessible label', 'suitemart' ) }
						help={ __(
							'Distinguishes this navigation from others on the page. Defaults to “Main navigation”.',
							'suitemart'
						) }
						value={ ariaLabel }
						onChange={ ( value ) =>
							setAttributes( { ariaLabel: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<nav { ...blockProps }>
				<div className="sm-nav__drawer">
					<ul { ...innerBlocksProps } />
				</div>
			</nav>
		</>
	);
}
