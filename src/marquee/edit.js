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
	RangeControl,
	ToggleControl,
} from '@wordpress/components';

const ALLOWED = [ 'suitemart/marquee-item' ];

const TEMPLATE = [
	[ 'suitemart/marquee-item' ],
	[ 'suitemart/marquee-item' ],
	[ 'suitemart/marquee-item' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { speed, direction, pauseOnHover, ariaLabel } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-marquee sm-marquee--${ direction }`,
	} );

	// The editor shows the static row rather than the animation: content that
	// slides away mid-edit cannot be clicked.
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-marquee__track' },
		{
			allowedBlocks: ALLOWED,
			template: TEMPLATE,
			orientation: 'horizontal',
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Marquee', 'suitemart' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Speed (pixels per second)', 'suitemart' ) }
						help={ __(
							'Set as a speed rather than a duration so a short strip and a long one move at the same pace.',
							'suitemart'
						) }
						value={ speed }
						min={ 5 }
						max={ 400 }
						step={ 5 }
						onChange={ ( value ) =>
							setAttributes( { speed: value ?? 60 } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Direction', 'suitemart' ) }
						value={ direction }
						options={ [
							{
								label: __(
									'Towards the end of the line',
									'suitemart'
								),
								value: 'end',
							},
							{
								label: __(
									'Towards the start of the line',
									'suitemart'
								),
								value: 'start',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { direction: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Pause on hover', 'suitemart' ) }
						checked={ pauseOnHover }
						onChange={ ( value ) =>
							setAttributes( { pauseOnHover: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Accessible label', 'suitemart' ) }
						help={ __(
							'Names the strip for screen readers. Defaults to “Announcements”.',
							'suitemart'
						) }
						value={ ariaLabel }
						onChange={ ( value ) =>
							setAttributes( { ariaLabel: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="sm-marquee__viewport">
					<div className="sm-marquee__lane">
						<div { ...innerBlocksProps } />
					</div>
				</div>
			</div>
		</>
	);
}
