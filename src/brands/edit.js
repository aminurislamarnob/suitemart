import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, RangeControl, ToggleControl } from '@wordpress/components';

const ALLOWED = [ 'core/image' ];

const TEMPLATE = Array.from( { length: 6 }, () => [ 'core/image', {} ] );

export default function Edit( { attributes, setAttributes } ) {
	const { columns, columnsMobile, muted, dividers, logoHeight } = attributes;

	const blockProps = useBlockProps( {
		className: [
			'sm-brands',
			`sm-brands--cols-${ columns }`,
			`sm-brands--mcols-${ columnsMobile }`,
			muted ? 'sm-brands--muted' : '',
			dividers ? 'sm-brands--dividers' : '',
		]
			.filter( Boolean )
			.join( ' ' ),
		style: { '--sm-brands-height': `${ logoHeight }px` },
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED,
		template: TEMPLATE,
		orientation: 'horizontal',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Logo strip', 'suitemart' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Columns', 'suitemart' ) }
						value={ columns }
						min={ 1 }
						max={ 10 }
						onChange={ ( value ) =>
							setAttributes( { columns: value ?? 6 } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Columns on mobile', 'suitemart' ) }
						value={ columnsMobile }
						min={ 1 }
						max={ 6 }
						onChange={ ( value ) =>
							setAttributes( { columnsMobile: value ?? 2 } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Logo height', 'suitemart' ) }
						help={ __(
							'Every logo is scaled to this height, which is what makes a mixed set look consistent.',
							'suitemart'
						) }
						value={ logoHeight }
						min={ 16 }
						max={ 200 }
						step={ 2 }
						onChange={ ( value ) =>
							setAttributes( { logoHeight: value ?? 48 } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Mute until hover', 'suitemart' ) }
						help={ __(
							'Shows logos in grey, restoring colour on hover or keyboard focus.',
							'suitemart'
						) }
						checked={ muted }
						onChange={ ( value ) =>
							setAttributes( { muted: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Dividers', 'suitemart' ) }
						checked={ dividers }
						onChange={ ( value ) =>
							setAttributes( { dividers: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...innerBlocksProps } />
		</>
	);
}
