import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
	RangeControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import Icon from '../_shared/Icon';

export default function Edit( { attributes, setAttributes } ) {
	const { placeholder, postType, resultLimit, showImages, buttonText } =
		attributes;

	const blockProps = useBlockProps( { className: 'sm-search' } );

	// Offer only post types this site actually has, so the control cannot be
	// set to something that would never return results.
	const postTypeOptions = useSelect( ( select ) => {
		const types =
			select( coreStore ).getPostTypes( { per_page: -1 } ) ?? [];

		return [
			{ label: __( 'Everything', 'suitemart' ), value: 'any' },
			...types
				.filter(
					( type ) =>
						type.viewable &&
						[ 'post', 'page', 'product' ].includes( type.slug )
				)
				.map( ( type ) => ( {
					label: type.labels?.singular_name ?? type.slug,
					value: type.slug,
				} ) ),
		];
	}, [] );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Live search', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Search in', 'suitemart' ) }
						value={ postType }
						options={ postTypeOptions }
						onChange={ ( value ) =>
							setAttributes( { postType: value } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Suggestions to show', 'suitemart' ) }
						value={ resultLimit }
						min={ 1 }
						max={ 20 }
						onChange={ ( value ) =>
							setAttributes( { resultLimit: value ?? 6 } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show thumbnails', 'suitemart' ) }
						checked={ showImages }
						onChange={ ( value ) =>
							setAttributes( { showImages: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Placeholder', 'suitemart' ) }
						value={ placeholder }
						placeholder={ __( 'Search…', 'suitemart' ) }
						onChange={ ( value ) =>
							setAttributes( { placeholder: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Button label', 'suitemart' ) }
						help={ __(
							'Read by screen readers. The button itself shows an icon.',
							'suitemart'
						) }
						value={ buttonText }
						placeholder={ __( 'Search', 'suitemart' ) }
						onChange={ ( value ) =>
							setAttributes( { buttonText: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="sm-search__form">
					<input
						className="sm-search__input"
						type="search"
						disabled
						placeholder={
							placeholder || __( 'Search…', 'suitemart' )
						}
					/>
					<span className="sm-search__submit">
						<Icon name="search" size={ 20 } />
					</span>
				</div>
			</div>
		</>
	);
}
