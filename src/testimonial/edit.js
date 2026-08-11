import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
	MediaReplaceFlow,
	MediaUpload,
	MediaUploadCheck,
	RichText,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	RangeControl,
	ToggleControl,
	Button,
} from '@wordpress/components';
import Icon from '../_shared/Icon';

export default function Edit( { attributes, setAttributes } ) {
	const {
		quote,
		author,
		role,
		rating,
		imageId,
		imageUrl,
		imageAlt,
		showQuoteMark,
		alignment,
	} = attributes;

	const blockProps = useBlockProps( {
		className: `sm-testimonial sm-testimonial--align-${ alignment }`,
	} );

	const onSelectImage = ( media ) =>
		setAttributes( {
			imageId: media?.id ?? 0,
			imageUrl: media?.url ?? '',
			imageAlt: media?.alt ?? '',
		} );

	return (
		<>
			{ imageUrl && (
				<BlockControls>
					<MediaReplaceFlow
						mediaId={ imageId }
						mediaURL={ imageUrl }
						allowedTypes={ [ 'image' ] }
						accept="image/*"
						onSelect={ onSelectImage }
					/>
				</BlockControls>
			) }

			<InspectorControls>
				<PanelBody title={ __( 'Testimonial', 'suitemart' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Rating', 'suitemart' ) }
						help={ __(
							'Zero hides the stars. The rating is also stated in text for screen readers.',
							'suitemart'
						) }
						value={ rating }
						min={ 0 }
						max={ 5 }
						onChange={ ( value ) =>
							setAttributes( { rating: value ?? 0 } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Quote mark', 'suitemart' ) }
						checked={ showQuoteMark }
						onChange={ ( value ) =>
							setAttributes( { showQuoteMark: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Alignment', 'suitemart' ) }
						value={ alignment }
						options={ [
							{
								label: __( 'Start', 'suitemart' ),
								value: 'start',
							},
							{
								label: __( 'Center', 'suitemart' ),
								value: 'center',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { alignment: value } )
						}
					/>
					{ ! imageUrl && (
						<MediaUploadCheck>
							<MediaUpload
								allowedTypes={ [ 'image' ] }
								onSelect={ onSelectImage }
								render={ ( { open } ) => (
									<Button
										__next40pxDefaultSize
										variant="secondary"
										onClick={ open }
									>
										{ __( 'Add portrait', 'suitemart' ) }
									</Button>
								) }
							/>
						</MediaUploadCheck>
					) }
					{ imageUrl && (
						<TextControl
							__nextHasNoMarginBottom
							label={ __( 'Portrait alt text', 'suitemart' ) }
							help={ __(
								'Leave empty when the name beside the photo already identifies the person.',
								'suitemart'
							) }
							value={ imageAlt }
							onChange={ ( value ) =>
								setAttributes( { imageAlt: value } )
							}
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<figure { ...blockProps }>
				{ showQuoteMark && (
					<span className="sm-testimonial__mark">
						<Icon name="quote" size={ 28 } />
					</span>
				) }

				{ rating > 0 && (
					<p className="sm-testimonial__rating">
						<span
							className="sm-testimonial__stars"
							aria-hidden="true"
						>
							{ [ 1, 2, 3, 4, 5 ].map( ( n ) => (
								<span
									key={ n }
									className={ `sm-testimonial__star${
										n <= rating ? ' is-filled' : ''
									}` }
								>
									<Icon name="star" size={ 16 } />
								</span>
							) ) }
						</span>
					</p>
				) }

				<blockquote className="sm-testimonial__quote">
					<RichText
						identifier="quote"
						tagName="p"
						value={ quote }
						onChange={ ( value ) =>
							setAttributes( { quote: value } )
						}
						placeholder={ __( 'What they said…', 'suitemart' ) }
						allowedFormats={ [] }
					/>
				</blockquote>

				<figcaption className="sm-testimonial__attribution">
					{ imageUrl && (
						<img
							className="sm-testimonial__avatar"
							src={ imageUrl }
							alt={ imageAlt }
						/>
					) }
					<span className="sm-testimonial__who">
						<RichText
							identifier="author"
							tagName="span"
							className="sm-testimonial__author"
							value={ author }
							onChange={ ( value ) =>
								setAttributes( { author: value } )
							}
							placeholder={ __( 'Name…', 'suitemart' ) }
							allowedFormats={ [] }
						/>
						<RichText
							identifier="role"
							tagName="span"
							className="sm-testimonial__role"
							value={ role }
							onChange={ ( value ) =>
								setAttributes( { role: value } )
							}
							placeholder={ __(
								'Role or company…',
								'suitemart'
							) }
							allowedFormats={ [] }
						/>
					</span>
				</figcaption>
			</figure>
		</>
	);
}
