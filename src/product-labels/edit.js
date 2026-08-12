import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'sm-product-labels',
	} );

	return (
		<div { ...blockProps }>
			<span className="sm-product-labels__label sm-product-labels__label--sale">
				{ __( 'Sale', 'suitemart' ) }
			</span>
			<span className="sm-product-labels__label sm-product-labels__label--new">
				{ __( 'New', 'suitemart' ) }
			</span>
			<span className="sm-product-labels__label sm-product-labels__label--out-of-stock">
				{ __( 'Out of stock', 'suitemart' ) }
			</span>
		</div>
	);
}
