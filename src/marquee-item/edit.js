import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const TEMPLATE = [
	[
		'core/paragraph',
		{ placeholder: __( 'Free delivery over £50', 'suitemart' ) },
	],
];

export default function Edit() {
	const blockProps = useBlockProps( { className: 'sm-marquee__item' } );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
		templateLock: false,
	} );

	return <div { ...innerBlocksProps } />;
}
