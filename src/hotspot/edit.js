import { __ } from '@wordpress/i18n';
import { useRef } from '@wordpress/element';
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
} from '@wordpress/components';

const TEMPLATE = [
	[ 'core/heading', { level: 4, placeholder: __( 'Title…', 'suitemart' ) } ],
	[
		'core/paragraph',
		{ placeholder: __( 'What is worth saying here…', 'suitemart' ) },
	],
];

const clamp = ( value ) => Math.min( 100, Math.max( 0, Math.round( value ) ) );

export default function Edit( { attributes, setAttributes, isSelected } ) {
	const { x, y, label, placement } = attributes;
	const markerRef = useRef( null );

	const blockProps = useBlockProps( {
		className: `sm-hotspots__point sm-hotspots__point--${ placement }${
			isSelected ? ' is-open' : ''
		}`,
		style: {
			'--sm-hotspot-x': `${ x }%`,
			'--sm-hotspot-y': `${ y }%`,
		},
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-hotspots__panel' },
		{ template: TEMPLATE, templateLock: false }
	);

	/**
	 * Drags the marker across the image.
	 *
	 * Two sliders in the sidebar would technically set the same two numbers,
	 * but placing a pin on a photograph is a spatial job — the editor has to be
	 * the picture itself, or nobody can aim.
	 *
	 * @param {PointerEvent} event Pointer down on the marker.
	 */
	const startDrag = ( event ) => {
		const frame = markerRef.current?.closest( '.sm-hotspots__frame' );

		if ( ! frame ) {
			return;
		}

		event.preventDefault();

		const move = ( moveEvent ) => {
			const rect = frame.getBoundingClientRect();

			if ( ! rect.width || ! rect.height ) {
				return;
			}

			setAttributes( {
				x: clamp(
					( ( moveEvent.clientX - rect.left ) / rect.width ) * 100
				),
				y: clamp(
					( ( moveEvent.clientY - rect.top ) / rect.height ) * 100
				),
			} );
		};

		const stop = () => {
			frame.ownerDocument.removeEventListener( 'pointermove', move );
			frame.ownerDocument.removeEventListener( 'pointerup', stop );
		};

		frame.ownerDocument.addEventListener( 'pointermove', move );
		frame.ownerDocument.addEventListener( 'pointerup', stop );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Hotspot', 'suitemart' ) }>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Marker label', 'suitemart' ) }
						help={ __(
							'Read out in place of the marker itself, so it should say what opens — “Show details about the lamp”.',
							'suitemart'
						) }
						value={ label }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Panel position', 'suitemart' ) }
						value={ placement }
						options={ [
							{ label: __( 'Above', 'suitemart' ), value: 'top' },
							{
								label: __( 'Below', 'suitemart' ),
								value: 'bottom',
							},
							{
								label: __( 'Before', 'suitemart' ),
								value: 'start',
							},
							{ label: __( 'After', 'suitemart' ), value: 'end' },
						] }
						onChange={ ( value ) =>
							setAttributes( { placement: value } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Horizontal (%)', 'suitemart' ) }
						help={ __(
							'Or drag the marker on the image itself.',
							'suitemart'
						) }
						value={ x }
						min={ 0 }
						max={ 100 }
						onChange={ ( value ) =>
							setAttributes( {
								x: clamp( Number( value ) || 0 ),
							} )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Vertical (%)', 'suitemart' ) }
						value={ y }
						min={ 0 }
						max={ 100 }
						onChange={ ( value ) =>
							setAttributes( {
								y: clamp( Number( value ) || 0 ),
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<button
					type="button"
					ref={ markerRef }
					className="sm-hotspots__marker"
					onPointerDown={ startDrag }
				>
					<span className="screen-reader-text">
						{ label || __( 'Show details', 'suitemart' ) }
					</span>
				</button>
				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
