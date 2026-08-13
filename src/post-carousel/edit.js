import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
	FormTokenField,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * The public post types, and the taxonomies attached to the chosen one.
 *
 * @param {string} postType Currently selected post type.
 * @return {{types: Array, taxonomies: Array}} Options for the controls.
 */
const useQueryOptions = ( postType ) =>
	useSelect(
		( select ) => {
			const { getPostTypes, getTaxonomies } = select( coreStore );

			const types = ( getPostTypes( { per_page: -1 } ) ?? [] ).filter(
				( type ) =>
					type.viewable &&
					! [ 'attachment', 'page' ].includes( type.slug )
			);

			const taxonomies = (
				getTaxonomies( { per_page: -1 } ) ?? []
			).filter(
				( tax ) =>
					tax.visibility?.public && tax.types?.includes( postType )
			);

			return { types, taxonomies };
		},
		[ postType ]
	);

export default function Edit( { attributes, setAttributes } ) {
	const {
		postType,
		taxonomy,
		terms,
		postsToShow,
		orderBy,
		order,
		headingLevel,
		showImage,
		showDate,
		showExcerpt,
		excerptLength,
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

	const { types, taxonomies } = useQueryOptions( postType );

	// The terms of the chosen taxonomy, so the filter can be picked by name
	// rather than by id.
	const termRecords = useSelect(
		( select ) =>
			taxonomy
				? select( coreStore ).getEntityRecords( 'taxonomy', taxonomy, {
						per_page: -1,
				  } ) ?? []
				: [],
		[ taxonomy ]
	);

	const blockProps = useBlockProps( {
		className: 'sm-post-carousel-wrapper',
	} );

	const selectedTermNames = terms
		.map(
			( id ) => termRecords.find( ( record ) => record.id === id )?.name
		)
		.filter( Boolean );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Which posts', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Post type', 'suitemart' ) }
						value={ postType }
						options={ types.map( ( type ) => ( {
							label: type.name,
							value: type.slug,
						} ) ) }
						onChange={ ( value ) =>
							setAttributes( {
								postType: value,
								// The old taxonomy almost certainly does not
								// apply to the new type, and a stale filter
								// silently returns nothing.
								taxonomy: '',
								terms: [],
							} )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Filter by', 'suitemart' ) }
						value={ taxonomy }
						options={ [
							{
								label: __( 'Everything', 'suitemart' ),
								value: '',
							},
							...taxonomies.map( ( tax ) => ( {
								label: tax.name,
								value: tax.slug,
							} ) ),
						] }
						onChange={ ( value ) =>
							setAttributes( { taxonomy: value, terms: [] } )
						}
					/>
					{ !! taxonomy && (
						<FormTokenField
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Terms', 'suitemart' ) }
							value={ selectedTermNames }
							suggestions={ termRecords.map(
								( record ) => record.name
							) }
							onChange={ ( names ) =>
								setAttributes( {
									terms: names
										.map(
											( name ) =>
												termRecords.find(
													( record ) =>
														record.name === name
												)?.id
										)
										.filter( Boolean ),
								} )
							}
						/>
					) }
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'How many', 'suitemart' ) }
						value={ postsToShow }
						min={ 1 }
						max={ 24 }
						onChange={ ( value ) =>
							setAttributes( { postsToShow: value ?? 9 } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Order by', 'suitemart' ) }
						value={ orderBy }
						options={ [
							{
								label: __( 'Date', 'suitemart' ),
								value: 'date',
							},
							{
								label: __( 'Title', 'suitemart' ),
								value: 'title',
							},
							{
								label: __( 'Menu order', 'suitemart' ),
								value: 'menu_order',
							},
							{
								label: __( 'Random', 'suitemart' ),
								value: 'rand',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { orderBy: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Direction', 'suitemart' ) }
						value={ order }
						options={ [
							{
								label: __( 'Newest first', 'suitemart' ),
								value: 'desc',
							},
							{
								label: __( 'Oldest first', 'suitemart' ),
								value: 'asc',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { order: value } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'The card', 'suitemart' ) }>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Featured image', 'suitemart' ) }
						checked={ showImage }
						onChange={ ( value ) =>
							setAttributes( { showImage: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Date', 'suitemart' ) }
						checked={ showDate }
						onChange={ ( value ) =>
							setAttributes( { showDate: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Excerpt', 'suitemart' ) }
						checked={ showExcerpt }
						onChange={ ( value ) =>
							setAttributes( { showExcerpt: value } )
						}
					/>
					{ showExcerpt && (
						<RangeControl
							__nextHasNoMarginBottom
							label={ __( 'Excerpt words', 'suitemart' ) }
							value={ excerptLength }
							min={ 5 }
							max={ 100 }
							onChange={ ( value ) =>
								setAttributes( { excerptLength: value ?? 20 } )
							}
						/>
					) }
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Title heading level', 'suitemart' ) }
						help={ __(
							'Pick the level that follows the heading above the carousel, so the page outline stays in order.',
							'suitemart'
						) }
						value={ headingLevel }
						min={ 2 }
						max={ 6 }
						onChange={ ( value ) =>
							setAttributes( { headingLevel: value ?? 3 } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'The carousel', 'suitemart' ) }
					initialOpen={ false }
				>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Cards shown (mobile)', 'suitemart' ) }
						value={ slidesPerView }
						min={ 1 }
						max={ 8 }
						onChange={ ( value ) =>
							setAttributes( { slidesPerView: value ?? 1 } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Cards shown (tablet)', 'suitemart' ) }
						value={ slidesPerViewTablet }
						min={ 1 }
						max={ 8 }
						onChange={ ( value ) =>
							setAttributes( {
								slidesPerViewTablet: value ?? 2,
							} )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Cards shown (desktop)', 'suitemart' ) }
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
						label={ __( 'Gap (px)', 'suitemart' ) }
						value={ spaceBetween }
						min={ 0 }
						max={ 96 }
						step={ 4 }
						onChange={ ( value ) =>
							setAttributes( { spaceBetween: value ?? 24 } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Arrows', 'suitemart' ) }
						checked={ showArrows }
						onChange={ ( value ) =>
							setAttributes( { showArrows: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Pagination dots', 'suitemart' ) }
						checked={ showPagination }
						onChange={ ( value ) =>
							setAttributes( { showPagination: value } )
						}
					/>
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
						label={ __( 'Advance on its own', 'suitemart' ) }
						help={ __(
							'A pause button appears with it, because anything that moves for more than five seconds needs one.',
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
							label={ __( 'Time on each (ms)', 'suitemart' ) }
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
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Carousel name', 'suitemart' ) }
						help={ __(
							'Announced to screen readers as the name of this carousel.',
							'suitemart'
						) }
						placeholder={ __( 'Posts', 'suitemart' ) }
						value={ label }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ /*
				 * Rendered on the server: the cards come from a query, and
				 * rebuilding that query in JavaScript would be a second
				 * implementation to keep in step with the first.
				 */ }
				<ServerSideRender
					block="suitemart/post-carousel"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
