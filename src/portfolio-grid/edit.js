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

export default function Edit( { attributes, setAttributes } ) {
	const {
		postsToShow,
		columns,
		orderBy,
		order,
		terms,
		showFilters,
		showExcerpt,
		headingLevel,
		label,
	} = attributes;

	const categories = useSelect(
		( select ) =>
			select( coreStore ).getEntityRecords( 'taxonomy', 'project-cat', {
				per_page: -1,
			} ) ?? [],
		[]
	);

	const blockProps = useBlockProps( {
		className: 'sm-portfolio-grid-wrapper',
	} );

	const selectedNames = terms
		.map( ( id ) => categories.find( ( term ) => term.id === id )?.name )
		.filter( Boolean );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Which projects', 'suitemart' ) }>
					<FormTokenField
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Categories', 'suitemart' ) }
						help={ __(
							'Leave empty for every category. This narrows what the grid queries; the filter buttons are built from whatever it finds.',
							'suitemart'
						) }
						value={ selectedNames }
						suggestions={ categories.map( ( term ) => term.name ) }
						onChange={ ( names ) =>
							setAttributes( {
								terms: names
									.map(
										( name ) =>
											categories.find(
												( term ) => term.name === name
											)?.id
									)
									.filter( Boolean ),
							} )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'How many', 'suitemart' ) }
						help={ __(
							'All of them are in the page at once, so filtering never waits on a request. Keep this to what the page can carry.',
							'suitemart'
						) }
						value={ postsToShow }
						min={ 1 }
						max={ 48 }
						onChange={ ( value ) =>
							setAttributes( { postsToShow: value ?? 12 } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Order by', 'suitemart' ) }
						value={ orderBy }
						options={ [
							{ label: __( 'Date', 'suitemart' ), value: 'date' },
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

				<PanelBody title={ __( 'Layout', 'suitemart' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Columns', 'suitemart' ) }
						value={ columns }
						min={ 1 }
						max={ 6 }
						onChange={ ( value ) =>
							setAttributes( { columns: value ?? 3 } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Category filters', 'suitemart' ) }
						help={ __(
							'Shown only when the projects in the grid span more than one category.',
							'suitemart'
						) }
						checked={ showFilters }
						onChange={ ( value ) =>
							setAttributes( { showFilters: value } )
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
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Title heading level', 'suitemart' ) }
						help={ __(
							'Pick the level that follows the heading above the grid, so the page outline stays in order.',
							'suitemart'
						) }
						value={ headingLevel }
						min={ 2 }
						max={ 6 }
						onChange={ ( value ) =>
							setAttributes( { headingLevel: value ?? 3 } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Grid name', 'suitemart' ) }
						help={ __(
							'Announced to screen readers as the name of this group.',
							'suitemart'
						) }
						placeholder={ __( 'Portfolio', 'suitemart' ) }
						value={ label }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ /* Rendered on the server, because the cards come from a
				  query and the filter bar from what that query found. */ }
				<ServerSideRender
					block="suitemart/portfolio-grid"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
