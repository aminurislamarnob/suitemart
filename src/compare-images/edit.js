import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	Button,
	Placeholder,
} from '@wordpress/components';

/**
 * One "choose an image" button, used for both halves.
 *
 * @param {Object}   props          Props.
 * @param {string}   props.label    Button label.
 * @param {string}   props.url      Current image URL.
 * @param {Function} props.onSelect Called with the chosen media object.
 * @return {JSX.Element} The control.
 */
function ImagePicker( { label, url, onSelect } ) {
	return (
		<MediaUploadCheck>
			<MediaUpload
				allowedTypes={ [ 'image' ] }
				onSelect={ onSelect }
				render={ ( { open } ) => (
					<Button
						variant={ url ? 'secondary' : 'primary' }
						onClick={ open }
					>
						{ label }
					</Button>
				) }
			/>
		</MediaUploadCheck>
	);
}

export default function Edit( { attributes, setAttributes } ) {
	const {
		beforeUrl,
		beforeAlt,
		beforeLabel,
		afterUrl,
		afterAlt,
		afterLabel,
		orientation,
		startPosition,
	} = attributes;

	// Editor-only: the front end starts at `startPosition` and moves from
	// there. Keeping a separate value here lets an editor check the alignment
	// of the two photographs without changing what visitors first see.
	const [ preview, setPreview ] = useState( startPosition );

	const blockProps = useBlockProps( {
		className: `sm-compare-images sm-compare-images--${ orientation }`,
		style: { '--sm-compare-position': `${ preview }%` },
	} );

	const select = ( half ) => ( media ) =>
		setAttributes( {
			[ `${ half }Id` ]: media?.id ?? 0,
			[ `${ half }Url` ]: media?.url ?? '',
			[ `${ half }Alt` ]: media?.alt ?? '',
		} );

	const controls = (
		<InspectorControls>
			<PanelBody title={ __( 'Images', 'suitemart' ) }>
				<ImagePicker
					label={
						beforeUrl
							? __( 'Replace first image', 'suitemart' )
							: __( 'Choose first image', 'suitemart' )
					}
					url={ beforeUrl }
					onSelect={ select( 'before' ) }
				/>
				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'First image description', 'suitemart' ) }
					value={ beforeAlt }
					onChange={ ( value ) =>
						setAttributes( { beforeAlt: value } )
					}
				/>
				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'First image caption', 'suitemart' ) }
					help={ __(
						'Shown over the image. Leave empty for none.',
						'suitemart'
					) }
					value={ beforeLabel }
					onChange={ ( value ) =>
						setAttributes( { beforeLabel: value } )
					}
				/>
				<hr />
				<ImagePicker
					label={
						afterUrl
							? __( 'Replace second image', 'suitemart' )
							: __( 'Choose second image', 'suitemart' )
					}
					url={ afterUrl }
					onSelect={ select( 'after' ) }
				/>
				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Second image description', 'suitemart' ) }
					value={ afterAlt }
					onChange={ ( value ) =>
						setAttributes( { afterAlt: value } )
					}
				/>
				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Second image caption', 'suitemart' ) }
					value={ afterLabel }
					onChange={ ( value ) =>
						setAttributes( { afterLabel: value } )
					}
				/>
			</PanelBody>

			<PanelBody title={ __( 'Comparison', 'suitemart' ) }>
				<SelectControl
					__nextHasNoMarginBottom
					label={ __( 'Wipe direction', 'suitemart' ) }
					value={ orientation }
					options={ [
						{
							label: __( 'Left to right', 'suitemart' ),
							value: 'horizontal',
						},
						{
							label: __( 'Top to bottom', 'suitemart' ),
							value: 'vertical',
						},
					] }
					onChange={ ( value ) =>
						setAttributes( { orientation: value } )
					}
				/>
				<RangeControl
					__nextHasNoMarginBottom
					label={ __( 'Starting position (%)', 'suitemart' ) }
					value={ startPosition }
					min={ 0 }
					max={ 100 }
					onChange={ ( value ) => {
						setAttributes( { startPosition: value ?? 50 } );
						setPreview( value ?? 50 );
					} }
				/>
			</PanelBody>
		</InspectorControls>
	);

	if ( ! beforeUrl || ! afterUrl ) {
		return (
			<>
				{ controls }
				<div { ...blockProps }>
					<Placeholder
						label={ __( 'Image comparison', 'suitemart' ) }
						instructions={ __(
							'Pick the two images to compare. They should be the same size and framed the same way, or the wipe will not line up.',
							'suitemart'
						) }
					>
						<ImagePicker
							label={
								beforeUrl
									? __( 'First image ✓', 'suitemart' )
									: __( 'Choose first image', 'suitemart' )
							}
							url={ beforeUrl }
							onSelect={ select( 'before' ) }
						/>
						<ImagePicker
							label={
								afterUrl
									? __( 'Second image ✓', 'suitemart' )
									: __( 'Choose second image', 'suitemart' )
							}
							url={ afterUrl }
							onSelect={ select( 'after' ) }
						/>
					</Placeholder>
				</div>
			</>
		);
	}

	return (
		<>
			{ controls }
			<div { ...blockProps }>
				<div className="sm-compare-images__frame">
					<img
						className="sm-compare-images__image"
						src={ beforeUrl }
						alt={ beforeAlt }
					/>
					<div className="sm-compare-images__reveal">
						<img
							className="sm-compare-images__image"
							src={ afterUrl }
							alt={ afterAlt }
						/>
					</div>
					<input
						type="range"
						className="sm-compare-images__range"
						min="0"
						max="100"
						value={ preview }
						aria-label={ __( 'Preview the wipe', 'suitemart' ) }
						onChange={ ( event ) =>
							setPreview( Number( event.target.value ) )
						}
					/>
					<div
						className="sm-compare-images__divider"
						aria-hidden="true"
					>
						<span className="sm-compare-images__handle" />
					</div>
					{ beforeLabel && (
						<span
							className="sm-compare-images__label sm-compare-images__label--before"
							aria-hidden="true"
						>
							{ beforeLabel }
						</span>
					) }
					{ afterLabel && (
						<span
							className="sm-compare-images__label sm-compare-images__label--after"
							aria-hidden="true"
						>
							{ afterLabel }
						</span>
					) }
				</div>
			</div>
		</>
	);
}
