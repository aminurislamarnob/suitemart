import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const TEMPLATE = [
	[ 'core/paragraph', { placeholder: __( 'Slide content…', 'suitemart' ) } ],
];

export default function Edit() {
	const blockProps = useBlockProps( { className: 'sm-slider__slide' } );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
		templateLock: false,
	} );

	return <div { ...innerBlocksProps } />;
}
