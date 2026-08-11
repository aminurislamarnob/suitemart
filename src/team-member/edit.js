import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	BlockControls,
	MediaReplaceFlow,
	MediaPlaceholder,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';

// Social links are core's job, brand icons included — Suitemart's Lucide sprite
// carries no brand marks, and reproducing them would be a trademark question
// rather than a design one.
const TEMPLATE = [ [ 'core/social-links', { size: 'has-small-icon-size' } ] ];

export default function Edit( { attributes, setAttributes } ) {
	const {
		name,
		role,
		bio,
		imageId,
		imageUrl,
		imageAlt,
		imageShape,
		aspectRatio,
		nameLevel,
		alignment,
	} = attributes;

	const blockProps = useBlockProps( {
		className: `sm-team-member sm-team-member--${ imageShape } sm-team-member--align-${ alignment }`,
		style: { '--sm-team-ratio': aspectRatio },
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-team-member__links' },
		{ template: TEMPLATE, templateLock: false }
	);

	const onSelectImage = ( media ) =>
		setAttributes( {
			imageId: media?.id ?? 0,
			imageUrl: media?.url ?? '',
			imageAlt: media?.alt ?? '',
		} );

	const NameTag = `h${ nameLevel }`;

	const controls = (
		<InspectorControls>
			<PanelBody title={ __( 'Team member', 'suitemart' ) }>
				<SelectControl
					__nextHasNoMarginBottom
					label={ __( 'Portrait shape', 'suitemart' ) }
					value={ imageShape }
					options={ [
						{ label: __( 'Square', 'suitemart' ), value: 'square' },
						{
							label: __( 'Rounded', 'suitemart' ),
							value: 'rounded',
						},
						{ label: __( 'Circle', 'suitemart' ), value: 'circle' },
					] }
					onChange={ ( value ) =>
						setAttributes( { imageShape: value } )
					}
				/>
				{ imageShape !== 'circle' && (
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Aspect ratio', 'suitemart' ) }
						value={ aspectRatio }
						options={ [
							{ label: __( 'Square', 'suitemart' ), value: '1' },
							{
								label: __( 'Portrait 4:5', 'suitemart' ),
								value: '0.8',
							},
							{
								label: __( 'Portrait 3:4', 'suitemart' ),
								value: '0.75',
							},
							{
								label: __( 'Landscape 5:4', 'suitemart' ),
								value: '1.25',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { aspectRatio: value } )
						}
					/>
				) }
				<SelectControl
					__nextHasNoMarginBottom
					label={ __( 'Name heading level', 'suitemart' ) }
					help={ __(
						'Pick the level that fits the page outline, not the size you want.',
						'suitemart'
					) }
					value={ String( nameLevel ) }
					options={ [ 2, 3, 4, 5, 6 ].map( ( n ) => ( {
						label: `H${ n }`,
						value: String( n ),
					} ) ) }
					onChange={ ( value ) =>
						setAttributes( { nameLevel: Number( value ) } )
					}
				/>
				<SelectControl
					__nextHasNoMarginBottom
					label={ __( 'Alignment', 'suitemart' ) }
					value={ alignment }
					options={ [
						{ label: __( 'Start', 'suitemart' ), value: 'start' },
						{ label: __( 'Center', 'suitemart' ), value: 'center' },
					] }
					onChange={ ( value ) =>
						setAttributes( { alignment: value } )
					}
				/>
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
			</PanelBody>
		</InspectorControls>
	);

	return (
		<>
			{ controls }

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

			<div { ...blockProps }>
				{ imageUrl ? (
					<figure className="sm-team-member__figure">
						<img
							className="sm-team-member__image"
							src={ imageUrl }
							alt={ imageAlt }
						/>
					</figure>
				) : (
					<MediaPlaceholder
						accept="image/*"
						allowedTypes={ [ 'image' ] }
						labels={ { title: __( 'Portrait', 'suitemart' ) } }
						onSelect={ onSelectImage }
					/>
				) }

				<div className="sm-team-member__body">
					<RichText
						identifier="name"
						tagName={ NameTag }
						className="sm-team-member__name"
						value={ name }
						onChange={ ( value ) =>
							setAttributes( { name: value } )
						}
						placeholder={ __( 'Name…', 'suitemart' ) }
						allowedFormats={ [] }
					/>
					<RichText
						identifier="role"
						tagName="p"
						className="sm-team-member__role"
						value={ role }
						onChange={ ( value ) =>
							setAttributes( { role: value } )
						}
						placeholder={ __( 'Role…', 'suitemart' ) }
						allowedFormats={ [] }
					/>
					<RichText
						identifier="bio"
						tagName="p"
						className="sm-team-member__bio"
						value={ bio }
						onChange={ ( value ) =>
							setAttributes( { bio: value } )
						}
						placeholder={ __( 'Short bio…', 'suitemart' ) }
						allowedFormats={ [] }
					/>
					<div { ...innerBlocksProps } />
				</div>
			</div>
		</>
	);
}
