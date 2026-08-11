import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	ToggleControl,
	TextControl,
} from '@wordpress/components';

const TEMPLATE = [
	[ 'suitemart/slide', {} ],
	[ 'suitemart/slide', {} ],
	[ 'suitemart/slide', {} ],
];

export default function Edit( { attributes, setAttributes } ) {
	const {
		slidesPerView,
		slidesPerViewTablet,
		slidesPerViewDesktop,
		spaceBetween,
		loop,
		autoplay,
		autoplayDelay,
		showArrows,
		showPagination,
		label,
	} = attributes;

	// The editor shows the slides as a plain scrolling row. Running Swiper in
	// the canvas fights the block editor's own drag and drop.
	const blockProps = useBlockProps( {
		className: 'sm-slider',
		style: {
			'--sm-slider-gap': `${ spaceBetween }px`,
			'--sm-slider-per-view': slidesPerView,
			'--sm-slider-per-view-md': slidesPerViewTablet,
			'--sm-slider-per-view-lg': slidesPerViewDesktop,
		},
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-slider__track' },
		{
			allowedBlocks: [ 'suitemart/slide' ],
			template: TEMPLATE,
			templateLock: false,
			orientation: 'horizontal',
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Slides in view', 'suitemart' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Mobile', 'suitemart' ) }
						value={ slidesPerView }
						min={ 1 }
						max={ 8 }
						onChange={ ( value ) =>
							setAttributes( { slidesPerView: value ?? 1 } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Tablet (768px+)', 'suitemart' ) }
						value={ slidesPerViewTablet }
						min={ 1 }
						max={ 8 }
						onChange={ ( value ) =>
							setAttributes( { slidesPerViewTablet: value ?? 2 } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Desktop (1024px+)', 'suitemart' ) }
						value={ slidesPerViewDesktop }
						min={ 1 }
						max={ 8 }
						onChange={ ( value ) =>
							setAttributes( {
								slidesPerViewDesktop: value ?? 3,
							} )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Gap between slides', 'suitemart' ) }
						value={ spaceBetween }
						min={ 0 }
						max={ 96 }
						step={ 4 }
						onChange={ ( value ) =>
							setAttributes( { spaceBetween: value ?? 16 } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'Behaviour', 'suitemart' ) }>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Loop', 'suitemart' ) }
						checked={ loop }
						onChange={ ( value ) =>
							setAttributes( { loop: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Autoplay', 'suitemart' ) }
						help={ __(
							'A visible pause button is added automatically, which accessibility guidelines require for anything that moves on its own.',
							'suitemart'
						) }
						checked={ autoplay }
						onChange={ ( value ) =>
							setAttributes( { autoplay: value } )
						}
					/>
					{ autoplay && (
						<RangeControl
							__nextHasNoMarginBottom
							label={ __( 'Delay (ms)', 'suitemart' ) }
							value={ autoplayDelay }
							min={ 1000 }
							max={ 30000 }
							step={ 500 }
							onChange={ ( value ) =>
								setAttributes( {
									autoplayDelay: value ?? 5000,
								} )
							}
						/>
					) }
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show arrows', 'suitemart' ) }
						checked={ showArrows }
						onChange={ ( value ) =>
							setAttributes( { showArrows: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show pagination', 'suitemart' ) }
						checked={ showPagination }
						onChange={ ( value ) =>
							setAttributes( { showPagination: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Accessible label', 'suitemart' ) }
						help={ __(
							'Describes what this carousel contains, for screen readers.',
							'suitemart'
						) }
						value={ label }
						placeholder={ __( 'Carousel', 'suitemart' ) }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="sm-slider__viewport">
					<div { ...innerBlocksProps } />
				</div>
			</div>
		</>
	);
}
