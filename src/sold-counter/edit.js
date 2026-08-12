import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'sm-sold-counter',
	} );

	return (
		<div { ...blockProps }>
			{ /* translators: %d: number of units sold. */ }
			{ __( '124 units sold', 'suitemart' ) }
		</div>
	);
}
