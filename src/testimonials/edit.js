import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';

const ALLOWED = [ 'suitemart/testimonial' ];

const TEMPLATE = [
	[ 'suitemart/testimonial' ],
	[ 'suitemart/testimonial' ],
	[ 'suitemart/testimonial' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { columns, columnsTablet } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-testimonials sm-testimonials--cols-${ columns } sm-testimonials--tcols-${ columnsTablet }`,
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED,
		template: TEMPLATE,
		orientation: 'horizontal',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Testimonials', 'suitemart' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Columns', 'suitemart' ) }
						value={ columns }
						min={ 1 }
						max={ 4 }
						onChange={ ( value ) =>
							setAttributes( { columns: value ?? 3 } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Columns on tablet', 'suitemart' ) }
						value={ columnsTablet }
						min={ 1 }
						max={ 3 }
						onChange={ ( value ) =>
							setAttributes( { columnsTablet: value ?? 2 } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...innerBlocksProps } />
		</>
	);
}
