import { __, sprintf, _n } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToolbarGroup,
	ToolbarButton,
	TextControl,
	ToggleControl,
	Placeholder,
	Button,
} from '@wordpress/components';

import Icon from '../_shared/Icon';

export default function Edit( { attributes, setAttributes } ) {
	const { frames, label, reverse, autoRotate } = attributes;

	// Only the frame on show needs resolving in the editor. Fetching all
	// thirty-six to render one of them makes selecting a sequence feel broken.
	const preview = useSelect(
		( select ) => {
			if ( ! frames.length ) {
				return null;
			}

			const id = reverse ? frames[ frames.length - 1 ] : frames[ 0 ];

			return select( coreStore ).getMedia( id, { context: 'view' } );
		},
		[ frames, reverse ]
	);

	const blockProps = useBlockProps( { className: 'sm-view-360' } );

	const onSelect = ( media ) =>
		setAttributes( {
			frames: ( Array.isArray( media ) ? media : [ media ] )
				.filter( Boolean )
				.map( ( item ) => item.id ),
		} );

	const picker = ( render ) => (
		<MediaUploadCheck>
			<MediaUpload
				multiple
				gallery
				addToGallery
				allowedTypes={ [ 'image' ] }
				value={ frames }
				onSelect={ onSelect }
				render={ render }
			/>
		</MediaUploadCheck>
	);

	const controls = (
		<>
			<BlockControls>
				{ frames.length > 0 &&
					picker( ( { open } ) => (
						<ToolbarGroup>
							<ToolbarButton onClick={ open }>
								{ __( 'Edit frames', 'suitemart' ) }
							</ToolbarButton>
						</ToolbarGroup>
					) ) }
			</BlockControls>

			<InspectorControls>
				<PanelBody title={ __( '360° view', 'suitemart' ) }>
					<p>
						{ sprintf(
							/* translators: %d: number of frames. */
							_n(
								'%d frame.',
								'%d frames.',
								frames.length,
								'suitemart'
							),
							frames.length
						) }
					</p>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Description', 'suitemart' ) }
						help={ __(
							'Read out in place of the whole sequence — “A walnut dining chair, seen from every side”.',
							'suitemart'
						) }
						value={ label }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Reverse the order', 'suitemart' ) }
						help={ __(
							'Use this when the object turns the wrong way — it is quicker than renaming the files.',
							'suitemart'
						) }
						checked={ reverse }
						onChange={ ( value ) =>
							setAttributes( { reverse: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Rotate on its own', 'suitemart' ) }
						help={ __(
							'Starts spinning when the page loads, and stops the moment anyone touches it. Never starts for visitors who prefer reduced motion.',
							'suitemart'
						) }
						checked={ autoRotate }
						onChange={ ( value ) =>
							setAttributes( { autoRotate: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);

	if ( frames.length < 2 ) {
		return (
			<>
				{ controls }
				<div { ...blockProps }>
					<Placeholder
						label={ __( '360° view', 'suitemart' ) }
						instructions={ __(
							'Choose the frames of a turntable sequence, in order. Two at minimum; twenty-four to thirty-six is what a smooth rotation usually takes.',
							'suitemart'
						) }
					>
						{ picker( ( { open } ) => (
							<Button variant="primary" onClick={ open }>
								{ __( 'Choose frames', 'suitemart' ) }
							</Button>
						) ) }
					</Placeholder>
				</div>
			</>
		);
	}

	return (
		<>
			{ controls }
			<div { ...blockProps }>
				<div className="sm-view-360__frames">
					{ preview?.source_url ? (
						<img
							className="sm-view-360__frame"
							src={ preview.source_url }
							alt=""
						/>
					) : (
						<Placeholder
							label={ __( '360° view', 'suitemart' ) }
							instructions={ __(
								'Loading the first frame…',
								'suitemart'
							) }
						/>
					) }
				</div>
				<div className="sm-view-360__controls">
					{ /* Inert here on purpose: the sequence does not spin in
					     the editor, and controls that look live but do nothing
					     read as a bug. They are drawn so the block occupies the
					     same space it will on the page. */ }
					<span className="sm-view-360__button" aria-hidden="true">
						<Icon name="chevron-left" size={ 20 } />
					</span>
					{ autoRotate && (
						<span
							className="sm-view-360__button"
							aria-hidden="true"
						>
							<Icon name="pause" size={ 20 } />
						</span>
					) }
					<span className="sm-view-360__button" aria-hidden="true">
						<Icon name="chevron-right" size={ 20 } />
					</span>
				</div>
			</div>
		</>
	);
}
